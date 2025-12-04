<?php
// auth/model/StaffModel.php
require_once __DIR__ . '/../config/db.php';

class StaffModel {
    public static function findByEmail(string $email): ?array {
        $pdo = db();
        $stmt = $pdo->prepare("
            SELECT u.user_id, u.name, u.email, u.password_hash, u.role, u.is_active, u.is_verified,
                   s.staff_id, s.department, s.phone
            FROM users u
            LEFT JOIN staff s ON s.user_id = u.user_id
            WHERE LOWER(u.email) = LOWER(:e)
              AND u.role = 'staff'
            LIMIT 1
        ");
        $stmt->execute([':e' => trim($email)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Check credentials and return session array on success, null on failure.
     * This requires the account to be active, verified, and have a staff profile.
     * For email verification flow, use checkCredentials() instead.
     */
    public static function verify(string $email, string $password): ?array {
        $s = self::findByEmail($email);
        if (!$s) return null;

        if ((int)$s['is_active'] !== 1) return null;
        if ((int)$s['is_verified'] !== 1) return null;
        if (!password_verify($password, $s['password_hash'])) return null;
        if (!$s['staff_id']) return null; // Must have staff profile

        return [
            'user_id' => $s['user_id'],
            'name' => $s['name'],
            'email' => $s['email'],
            'role' => $s['role'],
            'staff_id' => $s['staff_id']
        ];
    }

    /**
     * Check credentials without requiring verification status.
     * Used for email verification flow where we need to verify password
     * before sending verification email.
     */
    public static function checkCredentials(string $email, string $password): ?array {
        $s = self::findByEmail($email);
        if (!$s) return null;

        if ((int)$s['is_active'] !== 1) return null;
        if (!password_verify($password, $s['password_hash'])) return null;
        if (!$s['staff_id']) return null; // Must have staff profile

        return [
            'user_id' => $s['user_id'],
            'name' => $s['name'],
            'email' => $s['email'],
            'role' => $s['role'],
            'staff_id' => $s['staff_id']
        ];
    }

    /**
     * Update password hash for a staff member
     */
    public static function updatePasswordHash(string $email, string $password): bool {
        $pdo = db();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            UPDATE users 
            SET password_hash = ? 
            WHERE email = ? AND role = 'staff'
        ");
        return $stmt->execute([$hash, $email]) && $stmt->rowCount() > 0;
    }
}

