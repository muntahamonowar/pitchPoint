<?php
// controller/AdminController.php
require_once __DIR__ . '/../model/userModel.php';
require_once __DIR__ . '/../helpers/csrf.php';
require_once __DIR__ . '/../helpers/rateLimiter.php';

class userController {

    public static function login(array $post): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Rate limiting: 5 attempts per minute per IP
        $clientIp = get_client_ip();
        if (!rate_limit_check($clientIp, 5, 60, 'user_login')) {
            header('Location: /pitchPoint/auth/login.php?err=ratelimited');
            exit;
        }

        $email = trim(filter_var($post['email'] ?? '', FILTER_SANITIZE_EMAIL));
        $password = (string)($post['password'] ?? '');
        $csrf = $post['csrf_token'] ?? '';

        // CSRF
        if (!validateCSRFToken($csrf)) {
            header('Location: /pitchPoint/auth/login.php?err=csrf');
            exit;
        }

        // Basic validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            header('Location: /pitchPoint/auth/login.php?err=invalid');
            exit;
        }

        // Quick check for existence / verification state to give clearer errors
        $row = userModel::findByEmail($email);
        if (!$row) {
            header('Location: /pitchPoint/auth/login.php?err=bad');
            exit;
        }

        if ((int)$row['is_verified'] !== 1) {
            header('Location: /pitchPoint/auth/login.php?err=unverified');
            exit;
        }

        // Now check password + active
        $user = userModel::verify($email, $password);
        if (!$user) {
            header('Location: /pitchPoint/auth/login.php?err=bad');
            exit;
        }

        // For entrepreneurs, fetch entrepreneur_id from database
        $entrepreneurId = null;
        if (strtolower(trim($user['role'] ?? '')) === 'entrepreneur') {
            $pdo = db();
            $stmt = $pdo->prepare("SELECT entrepreneur_id FROM entrepreneurs WHERE user_id = :uid LIMIT 1");
            $stmt->execute([':uid' => $user['id']]);
            $entRow = $stmt->fetch();
            $entrepreneurId = $entRow ? (int)$entRow['entrepreneur_id'] : null;
        }

        // Success - set session with compatible structure for both systems
        $_SESSION['user'] = [
            'id' => $user['id'],
            'user_id' => $user['id'], // For entrepreneur system compatibility
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'entrepreneur_id' => $entrepreneurId
        ];
        $_SESSION['user_id'] = $user['id']; // Also set user_id for investor dashboard compatibility

        // Redirect by role (case-insensitive check)
        $userRole = isset($user['role']) ? strtolower(trim($user['role'])) : '';
        
        if ($userRole === 'investor') {
            header('Location: /pitchPoint/pitchpoint_investor/index/investorindex.php?url=project/explore');
            exit;
        }
        
        if ($userRole === 'entrepreneur') {
            header('Location: /pitchPoint/pitchpoint_entrepreneur/public/index.php');
            exit;
        }
        
        // Default redirect - change as needed
        header('Location: /pitchPoint/index.php');
        exit;
    }

    public static function logout(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: /pitchPoint/pitchpoint_staff/public/index.php');
        exit;
    }
}
