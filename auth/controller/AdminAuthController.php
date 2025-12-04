<?php
// auth/controller/AdminAuthController.php
require_once __DIR__ . '/../helpers/mailAdmin.php';
require_once __DIR__ . '/../helpers/csrf.php';
require_once __DIR__ . '/../helpers/rateLimiter.php';
require_once __DIR__ . '/../model/AdminModel.php';
require_once __DIR__ . '/../helpers/admin_auth.php';

class AdminAuthController {
    public static function login(array $post): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Rate limiting: 5 attempts per minute per IP
        $clientIp = get_client_ip();
        if (!rate_limit_check($clientIp, 5, 60, 'admin_login')) {
            header('Location: /pitchPoint/auth/loginAdmin.php?err=ratelimited');
            exit;
        }

        $email = trim(filter_var($post['email'] ?? '', FILTER_SANITIZE_EMAIL));
        $password = (string)($post['password'] ?? '');
        $csrf = $post['csrf_token'] ?? '';

        // CSRF
        if (!validateCSRFToken($csrf)) {
            header('Location: /pitchPoint/auth/loginAdmin.php?err=csrf');
            exit;
        }

        // Basic validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            header('Location: /pitchPoint/auth/loginAdmin.php?err=invalid');
            exit;
        }

        // Quick check for existence to give clearer errors
        $row = AdminModel::findByEmail($email);
        if (!$row) {
            header('Location: /pitchPoint/auth/loginAdmin.php?err=bad');
            exit;
        }

        // Now check password + active
        $admin = AdminModel::verify($email, $password);
        if (!$admin) {
            header('Location: /pitchPoint/auth/loginAdmin.php?err=bad');
            exit;
        }

        // Success - generate token and send verification email
        $token = bin2hex(random_bytes(24));
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['admin_verification_token'] = $token;
        
        sendAdminVerificationEmail($email, $token, $admin['admin_name']);
        header('Location: /pitchPoint/auth/loginAdmin.php?ok=1');
        exit;
    }

    public static function logout(): void {
        // Use the centralized adminLogout function from admin_auth.php
        // This handles logging, session cleanup, and redirect
        adminLogout();
    }
}
