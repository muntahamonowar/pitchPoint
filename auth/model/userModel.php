<?php
// model/AdminModel.php
require_once __DIR__ . '/../config/db.php';

class userModel {

    public static function findByEmail(string $email): ?array {
        $pdo = db();
        $stmt = $pdo->prepare("
            SELECT user_id, name, email, password_hash, role, is_active, is_verified, verify_token
            FROM users
            WHERE email = :e
            LIMIT 1
        ");
        $stmt->execute([':e' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Create user and return array with verification token and user_id on success, null on failure
     */
    public static function createUser(string $name, string $email, string $password, string $role = 'entrepreneur'): ?array {
        $pdo = db();

        if (self::findByEmail($email)) {
            return null; // already exists
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32));

        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password_hash, role, is_active, is_verified, verify_token, created_at, updated_at)
            VALUES (:n, :e, :h, :r, 1, 0, :t, NOW(), NOW())
        ");

        $ok = $stmt->execute([
            ':n' => $name,
            ':e' => $email,
            ':h' => $hash,
            ':r' => $role,
            ':t' => $token
        ]);

        if (!$ok) {
            return null;
        }

        $userId = (int)$pdo->lastInsertId();

        return [
            'token' => $token,
            'user_id' => $userId
        ];
    }

    /**
     * Mark account verified. Returns true if a row was updated.
     */
    public static function verifyAccount(string $token): bool {
        $pdo = db();
        $stmt = $pdo->prepare("
            UPDATE users
            SET is_verified = 1, verify_token = NULL, updated_at = NOW()
            WHERE verify_token = :t
            LIMIT 1
        ");
        $stmt->execute([':t' => $token]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Check credentials and return session array on success, null on failure.
     * This requires the account to be verified and active.
     */
    public static function verify(string $email, string $password): ?array {
        $u = self::findByEmail($email);
        if (!$u) return null;

        if ((int)$u['is_verified'] !== 1) return null;
        if ((int)$u['is_active'] !== 1) return null;
        if (!password_verify($password, $u['password_hash'])) return null;

        return [
            'id' => $u['user_id'],
            'name' => $u['name'],
            'email' => $u['email'],
            'role' => $u['role']
        ];
    }
}
