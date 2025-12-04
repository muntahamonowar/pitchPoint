<?php
declare(strict_types=1);

require_once __DIR__ . '/../../pitchpoint_admin/config/database.php';
require_once __DIR__ . '/csrf.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get the currently logged-in admin (from session) or null.
 */
function admin_current(): ?array
{
    return $_SESSION['admin'] ?? null;
}

/**
 * Require an admin; redirect to login if not logged in.
 */
function require_admin(): void
{
    if (!admin_current()) {
        header('Location: /pitchPoint/pitchpoint_admin/index.php?c=auth&a=login');
        exit;
    }
}

/**
 * Log an admin activity into activity_log.
 */
function log_admin_activity(int $adminId, string $description, string $status = 'Success'): void
{
    $stmt = db()->prepare("
        INSERT INTO activity_log (admin_id, activity_description, status)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$adminId, $description, $status]);
}

/**
 * Log admin activities to file.
 */
function log_admin_to_file(int $adminId, string $action, string $email = '', string $status = 'Success'): void
{
    $logDir = __DIR__ . '/../admin_log';
    
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/admin_login';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 100);
    
    $logEntry = sprintf(
        "[%s] %s [%s] admin_id=%d email=%s ip=%s user_agent=%s\n",
        date('c'),
        strtoupper($action),
        $status,
        $adminId,
        $email ?: 'N/A',
        $ip,
        $userAgent
    );
    
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Log the admin in and store them in the session.
 */
function admin_login(array $admin): void
{
    $_SESSION['admin'] = [
        'admin_id'   => $admin['admin_id'],
        'admin_name' => $admin['admin_name'],
        'email'      => $admin['email'],
    ];

    db()->prepare("UPDATE administrator SET last_login = NOW() WHERE admin_id = ?")
        ->execute([$admin['admin_id']]);

    log_admin_activity((int)$admin['admin_id'], 'Logged in', 'Success');
    log_admin_to_file((int)$admin['admin_id'], 'LOGIN', $admin['email'] ?? '', 'Success');
    session_regenerate_id(true);
}

/**
 * Logout the current admin.
 */
function admin_logout(): void
{
    if ($currentAdmin = admin_current()) {
        log_admin_activity((int)$currentAdmin['admin_id'], 'Logged out', 'Success');
        log_admin_to_file((int)$currentAdmin['admin_id'], 'LOGOUT', $currentAdmin['email'] ?? '', 'Success');
    }
    
    unset($_SESSION['admin']);
    session_regenerate_id(true);
    header('Location: /pitchPoint/pitchpoint_admin/index.php?c=auth&a=login');
    exit;
}

/* ---------- CSRF helpers (aliases for csrf.php functions) ---------- */

/**
 * Generate CSRF token - alias for generateCSRFToken from csrf.php
 */
function csrf_token(): string
{
    return generateCSRFToken();
}

/**
 * Verify CSRF token - alias for validateCSRFToken from csrf.php
 */
function csrf_verify(string $token): bool
{
    return validateCSRFToken($token);
}

/* ---------- Backward compatibility aliases (camelCase) ---------- */

function adminCurrent(): ?array
{
    return admin_current();
}

function requireAdmin(): void
{
    require_admin();
}

function logAdminActivity(int $adminId, string $desc, string $status = 'Success'): void
{
    log_admin_activity($adminId, $desc, $status);
}

function logAdminToFile(int $adminId, string $action, string $email = '', string $status = 'Success'): void
{
    log_admin_to_file($adminId, $action, $email, $status);
}

function adminLogin(array $admin): void
{
    admin_login($admin);
}

function adminLogout(): void
{
    admin_logout();
}
