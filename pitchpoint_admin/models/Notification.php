<?php
declare(strict_types=1);  
// This forces strict typing in PHP (recommended for cleaner, safer code)

require_once __DIR__ . '/BaseModel.php';
// Load BaseModel so we can use the database connection (db())

/**
 * Notification model
 * -------------------
 * This class interacts with the `notifications` table in the database.
 * It allows the admin panel to:
 *   - list all notifications
 *   - mark notifications as read
 */
class Notification extends BaseModel
{
    /**
     * Fetch all notifications AND the email of the user they belong to.
     *
     * @return array - returns an array of notifications with user email included
     */
    public static function allWithUser(): array
    {
        // Run a SQL query joining notifications with users to get the user's email
        $stmt = self::db()->query("
            SELECT 
                n.notification_id,    -- ID of the notification
                n.user_id,            -- ID of the user the notification belongs to
                n.type,               -- Type of notification (e.g., project_approved)
                n.payload_json,       -- Extra data in JSON format
                n.is_read,            -- 0 = unread, 1 = read
                n.created_at,         -- Timestamp when the notification was created
                u.email               -- Email of the user
            FROM notifications n
            JOIN users u ON u.user_id = n.user_id   -- link notification to user
            ORDER BY n.created_at DESC              -- newest notifications first
        ");

        // Fetch all rows as an array
        return $stmt->fetchAll();
    }

    /**
     * Mark a notification as read.
     *
     * @param int $id - ID of the notification to update
     */
    public static function markRead(int $id): void
    {
        // Prepare a SQL update query to set is_read = 1
        $stmt = self::db()->prepare("
            UPDATE notifications 
            SET is_read = 1 
            WHERE notification_id = ?
        ");

        // Execute the query with the ID passed into the method
        $stmt->execute([$id]);
    }
}
