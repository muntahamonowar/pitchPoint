<?php
declare(strict_types=1);
// Turn on strict typing so PHP enforces correct data types.
// Helps catch bugs early and makes code cleaner.

require_once __DIR__ . '/BaseModel.php';
// Load BaseModel so EmailMessage can use `db()` for database connection.


class EmailMessage extends BaseModel
{
    /**
     * Fetch all emails, optionally filtered by a specific folder.
     *
     * @param string|null $folder    Allowed: 'inbox', 'sent', or null/'all'
     * @param string      $adminEmail  The admin's email used to detect inbox/sent
     *
     * @return array  List of emails
     */
    public static function all(
        ?string $folder = null,
        string $adminEmail = 'admin@example.com'
    ): array {
        // Get PDO connection (db() comes from BaseModel/helper)
        $pdo = db();

        // Base SQL – fetch everything from the email table
        $sql = "SELECT * FROM email_management WHERE 1=1";
        // WHERE 1=1 allows adding AND conditions easily

        $params = []; // parameters for prepared query


        // --------------------------------------------------
        // Folder filter logic
        // --------------------------------------------------

        if ($folder === 'inbox') {
            // Inbox = messages WHERE admin is the receiver
            $sql .= " AND receiver_email = :adminEmail";
            $params[':adminEmail'] = $adminEmail;

        } elseif ($folder === 'sent') {
            // Sent = messages WHERE admin is the sender
            $sql .= " AND sender_email = :adminEmail";
            $params[':adminEmail'] = $adminEmail;
        }

        // Sort by date (latest first) and by ID fallback
        $sql .= " ORDER BY sent_date DESC, email_id DESC";


        // Prepare + execute the SQL query
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Fetch all emails as associative array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    /**
     * Fetch a single email by its ID.
     *
     * @param int $id   email_id from the database
     * @return array|null  the email, or null if not found
     */
    public static function find(int $id): ?array
    {
        $pdo = db();

        // Prepare query to fetch one email
        $stmt = $pdo->prepare("SELECT * FROM email_management WHERE email_id = ?");
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the email data or null if no match
        return $row ?: null;
    }



    /**
     * Create a new outgoing email (admin sends email).
     *
     * @param string $receiverEmail  The person receiving the email
     * @param string $senderEmail    The admin email (sending)
     * @param string $subject        Email subject
     * @param string $body           Email body
     *
     * @return int  The new email ID
     */
    public static function createOutgoing(
        string $receiverEmail,
        string $senderEmail,
        string $subject,
        string $body
    ): int {
        $pdo = db();

        // Insert a new OUTGOING email
        $stmt = $pdo->prepare("
            INSERT INTO email_management
                (sender_email, receiver_email, subject, body, sent_date, is_read)
            VALUES
                (:sender_email, :receiver_email, :subject, :body, NOW(), :is_read)
        ");

        // Outgoing email defaults to read = 1 (admin already knows they sent it)
        $stmt->execute([
            ':sender_email'   => $senderEmail,
            ':receiver_email' => $receiverEmail,
            ':subject'        => $subject,
            ':body'           => $body,
            ':is_read'        => 1,
        ]);

        // Return ID of newly inserted email
        return (int)$pdo->lastInsertId();
    }



    /**
     * Create a new incoming email (someone sends to admin).
     *
     * @return int   The new email ID
     */
    public static function createIncoming(
        string $senderEmail,
        string $receiverEmail,
        string $subject,
        string $body
    ): int {
        $pdo = db();

        // Insert a new INCOMING email
        $stmt = $pdo->prepare("
            INSERT INTO email_management
                (sender_email, receiver_email, subject, body, sent_date, is_read)
            VALUES
                (:sender_email, :receiver_email, :subject, :body, NOW(), :is_read)
        ");

        // Incoming email should be unread by default
        $stmt->execute([
            ':sender_email'   => $senderEmail,
            ':receiver_email' => $receiverEmail,
            ':subject'        => $subject,
            ':body'           => $body,
            ':is_read'        => 0,
        ]);

        // Return new email ID
        return (int)$pdo->lastInsertId();
    }



    /**
     * Mark an email as "read".
     * Used when admin opens an email.
     */
    public static function markRead(int $id): void
    {
        $pdo = db();

        // Update is_read flag to true
        $stmt = $pdo->prepare("
            UPDATE email_management
            SET is_read = 1
            WHERE email_id = ?
        ");

        $stmt->execute([$id]);
    }
}
