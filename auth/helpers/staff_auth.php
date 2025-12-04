<?php
declare(strict_types=1);

require_once __DIR__ . '/../../pitchpoint_admin/config/database.php';
require_once __DIR__ . '/csrf.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get the currently logged-in staff member (from session) or null.
 */
function staff_current(): ?array
{
    if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'staff') {
        return null;
    }
    
    return [
        'user_id'   => $_SESSION['user_id'] ?? null,
        'user_name' => $_SESSION['user_name'] ?? null,
        'user_role' => $_SESSION['user_role'] ?? null,
        'staff_id'  => $_SESSION['staff_id'] ?? null,
    ];
}

/**
 * Require a staff member; redirect to login if not logged in or not staff.
 */
function require_staff(): void
{
    if (!staff_current()) {
        header('Location: /pitchPoint/auth/loginStaff.php');
        exit;
    }
}

/**
 * Log a staff activity into activity_log.
 * Note: The activity_log table currently only supports admin_id.
 * Staff activities are logged to file only. Database logging is skipped
 * if the table structure doesn't support staff_id or user_id columns.
 */
function log_staff_activity(int $staffId, string $description, string $status = 'Success'): void
{
    // Try to insert with staff_id first
    try {
        $stmt = db()->prepare("
            INSERT INTO activity_log (staff_id, activity_description, status)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$staffId, $description, $status]);
        return; // Success, exit early
    } catch (PDOException $e) {
        // staff_id column doesn't exist, try with user_id
        try {
            // Get user_id from staff_id
            $userStmt = db()->prepare("SELECT user_id FROM staff WHERE staff_id = ?");
            $userStmt->execute([$staffId]);
            $user = $userStmt->fetch();
            if ($user) {
                $stmt = db()->prepare("
                    INSERT INTO activity_log (user_id, activity_description, status)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$user['user_id'], $description, $status]);
                return; // Success, exit early
            }
        } catch (PDOException $e2) {
            // user_id column also doesn't exist
            // The activity_log table only has admin_id, so we skip database logging
            // Staff activities are logged to file only (see log_staff_to_file)
        }
    }
    // If we reach here, database logging failed (table doesn't support staff/user_id)
    // This is expected - staff activities are logged to file only
}

/**
 * Log staff activities to file.
 */
function log_staff_to_file(int $staffId, string $action, string $email = '', string $status = 'Success'): void
{
    $logDir = __DIR__ . '/../staff_log';
    
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/staff_login';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 100);
    
    $logEntry = sprintf(
        "[%s] %s [%s] staff_id=%d email=%s ip=%s user_agent=%s\n",
        date('c'),
        strtoupper($action),
        $status,
        $staffId,
        $email ?: 'N/A',
        $ip,
        $userAgent
    );
    
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Log the staff member in and store them in the session.
 * 
 * $staff is a row from StaffModel::verify() with user_id, name, email, role, staff_id
 */
function staff_login(array $staff): void
{
    $_SESSION['user_id']   = (int)$staff['user_id'];
    $_SESSION['user_name'] = $staff['name'];
    $_SESSION['user_role'] = $staff['role'];
    $_SESSION['staff_id']  = (int)$staff['staff_id'];
    
    // Log the login activity
    log_staff_activity((int)$staff['staff_id'], 'Logged in', 'Success');
    log_staff_to_file((int)$staff['staff_id'], 'LOGIN', $staff['email'] ?? '', 'Success');
    
    session_regenerate_id(true);
}

/**
 * Logout the current staff member.
 */
function staff_logout(): void
{
    if ($currentStaff = staff_current()) {
        log_staff_activity((int)$currentStaff['staff_id'], 'Logged out', 'Success');
        // Get email from session or database
        $email = '';
        if (!empty($_SESSION['user_id'])) {
            try {
                $stmt = db()->prepare("SELECT email FROM users WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch();
                $email = $user['email'] ?? '';
            } catch (PDOException $e) {
                // Ignore if query fails
            }
        }
        log_staff_to_file((int)$currentStaff['staff_id'], 'LOGOUT', $email, 'Success');
    }
    
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_role'], $_SESSION['staff_id']);
    session_regenerate_id(true);
    header('Location: /pitchPoint/auth/loginStaff.php');
    exit;
}

/* ---------- Backward compatibility aliases ---------- */

function staffCurrent(): ?array
{
    return staff_current();
}

function requireStaff(): void
{
    require_staff();
}

function logStaffActivity(int $staffId, string $desc, string $status = 'Success'): void
{
    log_staff_activity($staffId, $desc, $status);
}

function logStaffToFile(int $staffId, string $action, string $email = '', string $status = 'Success'): void
{
    log_staff_to_file($staffId, $action, $email, $status);
}

function staffLogin(array $staff): void
{
    staff_login($staff);
}

function staffLogout(): void
{
    staff_logout();
}

