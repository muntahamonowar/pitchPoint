<?php
// auth/controller/StaffAuthController.php
require_once __DIR__ . '/../helpers/csrf.php';
require_once __DIR__ . '/../helpers/rateLimiter.php';
require_once __DIR__ . '/../helpers/mailAdmin.php';
require_once __DIR__ . '/../model/StaffModel.php';
require_once __DIR__ . '/../helpers/staff_auth.php';

class StaffAuthController {
    public static function login(array $post): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Rate limiting: 5 attempts per minute per IP
        $clientIp = get_client_ip();
        if (!rate_limit_check($clientIp, 5, 60, 'staff_login')) {
            header('Location: /pitchPoint/auth/loginStaff.php?err=ratelimited');
            exit;
        }

        $email = trim(filter_var($post['email'] ?? '', FILTER_SANITIZE_EMAIL));
        $password = (string)($post['password'] ?? '');
        $csrf = $post['csrf_token'] ?? '';

        // CSRF
        if (!validateCSRFToken($csrf)) {
            header('Location: /pitchPoint/auth/loginStaff.php?err=csrf');
            exit;
        }

        // Basic validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            header('Location: /pitchPoint/auth/loginStaff.php?err=invalid');
            exit;
        }

        // Quick check for existence to give clearer errors
        $row = StaffModel::findByEmail($email);
        if (!$row) {
            header('Location: /pitchPoint/auth/loginStaff.php?err=bad');
            exit;
        }

        // Check credentials (password + active + staff profile, but not is_verified)
        // This allows sending verification email even if account is not yet verified
        $staff = StaffModel::checkCredentials($email, $password);
        if (!$staff) {
            header('Location: /pitchPoint/auth/loginStaff.php?err=bad');
            exit;
        }

        // Success - generate token and send verification email
        $token = bin2hex(random_bytes(24));
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['staff_verification_token'] = $token;
        $_SESSION['staff_verification_email'] = $email; // Store email for verification
        
        sendStaffVerificationEmail($email, $token, $staff['name']);
        header('Location: /pitchPoint/auth/loginStaff.php?ok=1');
        exit;
    }

    public static function logout(): void {
        // Use the centralized staff_logout function from staff_auth.php
        staff_logout();
    }
}

