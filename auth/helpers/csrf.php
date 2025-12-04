<?php
// helpers/csrf.php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!function_exists('generateCSRFToken')) {
    function generateCSRFToken(): string {
        if (!isset($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }
}

if (!function_exists('validateCSRFToken')) {
    function validateCSRFToken(string $token): bool {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
    }
}
