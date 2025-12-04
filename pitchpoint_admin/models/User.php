<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel
{
    /**
     * Filter users by role and active status.
     *
     * @param string|null $role   entrepreneur|investor|staff|admin|null
     * @param string|null $active '0' | '1' | null
     * @param string|null $search search term for name or email
     */
    public static function filter(?string $role, ?string $active, ?string $search = null): array
    {
        $sql = "
            SELECT 
                u.user_id, 
                u.name, 
                u.email, 
                u.role, 
                u.created_at,
                COALESCE(
                    (SELECT um.action 
                     FROM user_management um 
                     WHERE um.user_id = u.user_id 
                     ORDER BY um.action_date DESC 
                     LIMIT 1),
                    'activated'
                ) as last_action
            FROM users u
            WHERE 1 = 1
        ";
        $params = [];

        if ($role !== null && in_array($role, ['entrepreneur', 'investor', 'staff', 'admin'], true)) {
            $sql      .= " AND u.role = ?";
            $params[]  = $role;
        }

        if ($search !== null && $search !== '') {
            $sql      .= " AND (u.name LIKE ? OR u.email LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[]  = $searchTerm;
            $params[]  = $searchTerm;
        }

        $sql .= " ORDER BY u.created_at DESC";

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll();

        // Filter by active status based on last action
        if ($active === '0' || $active === '1') {
            $users = array_filter($users, function($user) use ($active) {
                $isActive = ($user['last_action'] === 'activated');
                return $active === '1' ? $isActive : !$isActive;
            });
        }

        return array_values($users);
    }

    /**
     * Set user active status using user_management table.
     */
    public static function setStatus(int $userId, int $newStatus): bool
    {
        $action = $newStatus ? 'activated' : 'deactivated';
        $adminId = 1; // You might want to get this from session
        
        $stmt = self::db()->prepare(
            "INSERT INTO user_management (user_id, admin_id, action, action_date) 
             VALUES (?, ?, ?, NOW())"
        );

        return $stmt->execute([$userId, $adminId, $action]);
    }

    /**
     * Get current status of a user from user_management table.
     */
    public static function getStatus(int $userId): string
    {
        $stmt = self::db()->prepare(
            "SELECT action FROM user_management 
             WHERE user_id = ? 
             ORDER BY action_date DESC 
             LIMIT 1"
        );
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        return $result ? $result['action'] : 'activated'; // Default to activated if no record
    }

    /**
     * Permanently delete a user by id.
     */
    public static function deleteById(int $userId): bool
    {
        // First delete from user_management table
        $stmt1 = self::db()->prepare("DELETE FROM user_management WHERE user_id = ?");
        $stmt1->execute([$userId]);
        
        // Then delete from users table
        $stmt2 = self::db()->prepare("DELETE FROM users WHERE user_id = ?");
        return $stmt2->execute([$userId]);
    }
}