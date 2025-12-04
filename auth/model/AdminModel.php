<?php
// auth/model/AdminModel.php
require_once __DIR__ . '/../config/db.php';

class AdminModel {
    public static function findByEmail(string $email): ?array {
        $pdo = db();
        $stmt = $pdo->prepare("
            SELECT admin_id, admin_name, email, password_hash, is_active
            FROM administrator
            WHERE LOWER(email) = LOWER(:e)
            LIMIT 1
        ");
        $stmt->execute([':e' => trim($email)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Check credentials and return session array on success, null on failure.
     * This requires the account to be active.
     */
    public static function verify(string $email, string $password): ?array {
        $a = self::findByEmail($email);
        if (!$a) return null;

        if ((int)$a['is_active'] !== 1) return null;
        if (!password_verify($password, $a['password_hash'])) return null;

        return [
            'admin_id' => $a['admin_id'],
            'admin_name' => $a['admin_name'],
            'email' => $a['email']
        ];
    }

    /**
     * Update password hash for an admin
     */
    public static function updatePasswordHash(string $email, string $password): bool {
        $pdo = db();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            UPDATE administrator 
            SET password_hash = ? 
            WHERE email = ?
        ");
        return $stmt->execute([$hash, $email]) && $stmt->rowCount() > 0;
    }

}
