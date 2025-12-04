<?php
// controller/SignUpController.php
require_once __DIR__ . '/../model/userModel.php';
require_once __DIR__ . '/../helpers/rateLimiter.php';
require_once __DIR__ . '/../helpers/mail.php';
require_once __DIR__ . '/../waf/theFire.php';

class SignUpController {

    public static function register(array $post): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Rate limiting: 3 registrations per 10 minutes per IP
        $clientIp = get_client_ip();
        if (!rate_limit_check($clientIp, 3, 600, 'signup')) {
            header('Location: /pitchPoint/auth/signUp.php?err=ratelimited');
            exit;
        }

        $name = trim($post['name'] ?? '');
        $email = trim($post['email'] ?? '');
        $password = trim($post['password'] ?? '');
        $role = $post['role'] ?? 'entrepreneur';

        if ($name === '' || $email === '' || $password === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: /pitchPoint/auth/signUp.php?err=invalid');
            exit;
        }

        if (strlen($password) < 8) {
            header('Location: /pitchPoint/auth/signUp.php?err=shortpass');
            exit;
        }

        // Create user and get user_id
        $result = userModel::createUser($name, $email, $password, $role);

        if (!$result) {
            header('Location: /pitchPoint/auth/signUp.php?err=exists');
            exit;
        }

        // userModel::createUser returns array with 'token' and 'user_id'
        $token = $result['token'];
        $userId = $result['user_id'];

        // If role is entrepreneur, create entrepreneur record
        if (strtolower($role) === 'entrepreneur') {
            require_once __DIR__ . '/../config/db.php';
            $pdo = db();
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO entrepreneurs (user_id, company_name, created_at, updated_at)
                    VALUES (:uid, NULL, NOW(), NOW())
                ");
                $stmt->execute([
                    ':uid' => $userId
                ]);
            } catch (PDOException $e) {
                // Log error but continue - entrepreneur record creation failure shouldn't block user creation
                error_log('Failed to create entrepreneur record for user_id ' . $userId . ': ' . $e->getMessage());
            }
        }

        // Send verification email 
        sendVerificationEmail($email, $token, $name);

        header('Location: /pitchPoint/auth/login.php?ok=1');
        exit;
    }

    // Add this method to handle verification
    public static function verifyEmail(string $token): void {
        $ok = userModel::verifyAccount($token);

        if ($ok) {
            header('Location: /pitchPoint/auth/login.php?verify=success');
        } else {
            header('Location: /pitchPoint/auth/login.php?verify=invalid');
        }
        exit;
    }
}
