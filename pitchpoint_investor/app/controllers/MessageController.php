<?php // Opening PHP tag to start PHP code execution
// app/controllers/MessageController.php
declare(strict_types=1); // Enable strict type checking for this file

require_once __DIR__ . '/../config/database.php'; // Include the database configuration file

class MessageController extends Controller // Define the MessageController class that extends the base Controller class
{
    private PDO $db; // Declare a private PDO property to store the database connection

    public function __construct() // Define the constructor method that runs when the controller is instantiated
    {
        $this->db = db();  // from app/config/database.php - Initialize database connection using the global db() helper function
    }

    // /message  -> inbox
    public function index(): void // Define the index method that handles the default message route
    {
        $this->inbox(); // Call the inbox method to display messages
    }

    /**
     * INBOX
     * - entrepreneurMessages: from `messages`
     * - adminEmailMessages:   from `email_management`
     * Also passes currentUserId + currentUserEmail for chat bubbles.
     */
    public function inbox(): void // Define the inbox method to display all messages
    {
        if (session_status() === PHP_SESSION_NONE) { // Check if session is not already started
            session_start(); // Start a new session or resume existing session
        }

        $userId = $_SESSION['user_id'] ?? null; // Get the user ID from session, or null if not set
        if (!$userId) { // Check if user ID is missing
            $this->redirect('auth/login'); // Redirect to login page if user is not authenticated
        }

        // Get current user's email
        $uStmt = $this->db->prepare("SELECT email FROM users WHERE user_id = ?"); // Prepare SQL statement to get user email
        $uStmt->execute([$userId]); // Execute the prepared statement with user ID parameter
        $userEmail = $uStmt->fetchColumn() ?: ''; // Fetch the email column value, or empty string if not found
// === 1) Messages with entrepreneurs (messages table) ===

// Build SQL query for entrepreneur messages
$mSql = "
    SELECT
        m.message_id,        -- Message ID from messages table
        m.body,              -- Message body text
        m.is_read,           -- Read status flag
        m.created_at,        -- Message creation timestamp
        m.sender_user_id,    -- ID of the user who sent the message
        m.receiver_user_id,  -- ID of the user who received the message
        u_from.name AS sender_name,   -- Sender's name (from users table)
        u_to.name   AS receiver_name, -- Receiver's name (from users table)
        p.title     AS project_title,  -- Related project title
        p.entrepreneur_id,   -- Project's entrepreneur ID
        u_entrepreneur.name AS project_owner_name  -- Project owner's name
    FROM messages m
    INNER JOIN users u_from ON m.sender_user_id = u_from.user_id   -- sender user
    INNER JOIN users u_to   ON m.receiver_user_id = u_to.user_id   -- receiver user
    LEFT JOIN projects p ON m.project_id = p.project_id            -- project info
    LEFT JOIN entrepreneurs e ON p.entrepreneur_id = e.entrepreneur_id  -- entrepreneur info
    LEFT JOIN users u_entrepreneur ON e.user_id = u_entrepreneur.user_id  -- project owner user
    WHERE m.sender_user_id   = :uid
       OR m.receiver_user_id = :uid
    ORDER BY m.created_at ASC
";

$mStmt = $this->db->prepare($mSql);
$mStmt->execute(['uid' => $userId]);
$entrepreneurMessages = $mStmt->fetchAll(PDO::FETCH_ASSOC);


        /// === 2) Messages with admins (email_management table)
$adminEmailMessages = [];

if ($userEmail !== '') {
    // Build SQL query for admin email messages
    $eSql = "
        SELECT
            email_id,        -- Email ID from email_management table
            sender_email,    -- Email address of the sender
            receiver_email,  -- Email address of the receiver
            subject,         -- Email subject line
            body,            -- Email body text
            sent_date,       -- Date when the email was sent
            is_read          -- Read status flag
        FROM email_management
        WHERE sender_email = :email     -- current user is the sender
           OR receiver_email = :email   -- or current user is the receiver
        ORDER BY sent_date ASC          -- oldest first
    ";

    $eStmt = $this->db->prepare($eSql);
    $eStmt->execute(['email' => $userEmail]);
    $adminEmailMessages = $eStmt->fetchAll(PDO::FETCH_ASSOC);
}


        // Pass data & current user info to chat view
        $this->view('message/inbox', [ // Render the inbox view with the following data
            'entrepreneurMessages' => $entrepreneurMessages, // Pass entrepreneur messages array
            'adminEmailMessages'   => $adminEmailMessages, // Pass admin email messages array
            'currentUserId'        => $userId, // Pass current user ID
            'currentUserEmail'     => $userEmail, // Pass current user email
        ]); // End of view method call
    }

    /**
     * COMPOSE PAGE
     * - top form: sendEntrepreneur() -> messages
     * - bottom form: sendAdmin() -> email_management
     */
    public function compose(): void // Define the compose method to display message composition form
    {
        if (session_status() === PHP_SESSION_NONE) { // Check if session is not already started
            session_start(); // Start a new session or resume existing session
        }

        $userId = $_SESSION['user_id'] ?? null; // Get the user ID from session, or null if not set
        if (!$userId) { // Check if user ID is missing
            $this->redirect('auth/login'); // Redirect to login page if user is not authenticated
        }

        $selectedProjectId  = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0; // Get selected project ID from GET parameter, or 0 if not set
        $selectedReceiverId = isset($_GET['to_user'])    ? (int)$_GET['to_user']    : 0; // Get selected receiver user ID from GET parameter, or 0 if not set

        // Entrepreneurs (for messages table)
        $eStmt = $this->db->query(" 
            SELECT user_id, name 
            FROM users 
            WHERE role = 'entrepreneur' 
            ORDER BY name 
        "); // End of SQL query
        $entrepreneurs = $eStmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all results as associative array

        // Projects (to link message to a project) - include entrepreneur_id
        // FIXED: Use LEFT JOIN to include all projects, even if entrepreneur/user data is missing
        // This ensures all published/public projects are shown, even if entrepreneur data is incomplete
        // IMPORTANT: Get the entrepreneur's user_id from the entrepreneurs table, not from a generic users join
        $pStmt = $this->db->query(" 
            SELECT  
                p.project_id, 
                p.title,  
                p.entrepreneur_id, 
                e.user_id AS entrepreneur_user_id,
                u.name AS entrepreneur_name 
            FROM projects p 
            LEFT JOIN entrepreneurs e ON p.entrepreneur_id = e.entrepreneur_id 
            LEFT JOIN users u ON e.user_id = u.user_id 
            WHERE p.status = 'published' 
              AND p.visibility = 'public' 
            ORDER BY p.title 
        "); // End of SQL query
        $projects = $pStmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all results as associative array

        // Get default admin email from administrator table
        $adminStmt = $this->db->query(" 
            SELECT email 
            FROM administrator 
            WHERE is_active = 1 
            ORDER BY admin_id ASC 
            LIMIT 1 
        "); // End of SQL query
        $defaultAdmin = $adminStmt->fetch(PDO::FETCH_ASSOC); // Fetch one result as associative array
        $defaultAdminEmail = $defaultAdmin ? $defaultAdmin['email'] : 'rafia@pitchpoint.com'; // Get email from result, or use default email if not found

        $this->view('message/compose', [ // Render the compose view with the following data
            'entrepreneurs'      => $entrepreneurs, // Pass entrepreneurs array
            'projects'           => $projects, // Pass projects array
            'defaultAdminEmail'  => $defaultAdminEmail, // Pass default admin email
            'selectedProjectId'  => $selectedProjectId, // Pass selected project ID
            'selectedReceiverId' => $selectedReceiverId, // Pass selected receiver user ID
        ]); // End of view method call
    }

    /**
     * POST /message/sendEntrepreneur
     * Insert into `messages`
     */
    public function sendEntrepreneur(): void // Define the sendEntrepreneur method to handle sending messages to entrepreneurs
    {
        if (session_status() === PHP_SESSION_NONE) { // Check if session is not already started
            session_start(); // Start a new session or resume existing session
        }

        $senderId = $_SESSION['user_id'] ?? null; // Get the sender user ID from session, or null if not set
        if (!$senderId) { // Check if sender ID is missing
            $this->redirect('auth/login'); // Redirect to login page if user is not authenticated
        }

        $receiverId = isset($_POST['receiver_user_id']) ? (int)$_POST['receiver_user_id'] : 0; // Get receiver user ID from POST data, or 0 if not set
        $projectId  = isset($_POST['project_id'])       ? (int)$_POST['project_id']       : 0; // Get project ID from POST data, or 0 if not set
        $body       = trim($_POST['body'] ?? ''); // Get message body from POST data, trim whitespace, or empty string if not set

        if ($receiverId <= 0 || $projectId <= 0 || $body === '') { // Check if any required field is missing or invalid
            $this->redirect('message/compose'); // Redirect back to compose page if validation fails
        }

        $sql = " 
            INSERT INTO messages (project_id, sender_user_id, receiver_user_id, body)
            VALUES (?, ?, ?, ?) 
        "; // End of SQL statement
        $stmt = $this->db->prepare($sql); // Prepare the SQL statement
        $stmt->execute([$projectId, $senderId, $receiverId, $body]); // Execute the prepared statement with parameter values

        $this->redirect('message/inbox'); // Redirect to inbox after successful message send
    }

    /**
     * POST /message/sendAdmin
     * Insert into `email_management`
     */
    public function sendAdmin(): void // Define the sendAdmin method to handle sending messages to admins
    {
        if (session_status() === PHP_SESSION_NONE) { // Check if session is not already started
            session_start(); // Start a new session or resume existing session
        }

        $userId = $_SESSION['user_id'] ?? null; // Get the user ID from session, or null if not set
        if (!$userId) { // Check if user ID is missing
            $this->redirect('auth/login'); // Redirect to login page if user is not authenticated
        }

        // current user's email = sender_email
        $uStmt = $this->db->prepare("SELECT email FROM users WHERE user_id = ?"); // Prepare SQL statement to get user email
        $uStmt->execute([$userId]); // Execute the prepared statement with user ID parameter
        $senderEmail = $uStmt->fetchColumn(); // Fetch the email column value

        $receiverEmail = trim($_POST['receiver_email'] ?? ''); // Get receiver email from POST data, trim whitespace, or empty string if not set
        $subject       = trim($_POST['subject'] ?? ''); // Get subject from POST data, trim whitespace, or empty string if not set
        $body          = trim($_POST['body_admin'] ?? ''); // Get message body from POST data, trim whitespace, or empty string if not set

        if ($receiverEmail === '' || $body === '') { // Check if required fields are missing
            $this->redirect('message/compose'); // Redirect back to compose page if validation fails
        }

        $sql = " 
            INSERT INTO email_management (sender_email, receiver_email, subject, body)  
            VALUES (?, ?, ?, ?) 
        "; // End of SQL statement
        $stmt = $this->db->prepare($sql); // Prepare the SQL statement
        $stmt->execute([$senderEmail, $receiverEmail, $subject, $body]); // Execute the prepared statement with parameter values

        $this->redirect('message/inbox'); // Redirect to inbox after successful message send
    }
}
