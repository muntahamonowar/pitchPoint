<?php
declare(strict_types=1);
// Strict typing makes PHP more predictable and safer.


/**
 * Load the EmailMessage model.
 * This model handles database operations for emails:
 *  - list emails
 *  - find a single email
 *  - save outgoing emails
 *  - save incoming emails
 *  - mark email as read
 */
require_once __DIR__ . '/../models/EmailMessage.php';


/**
 * EmailsController
 *
 * Handles:
 *   - viewing inbox / sent / all emails
 *   - showing an email
 *   - composing + sending an email
 *   - marking an email as read
 */
class EmailsController extends BaseAdminController
{
    /**
     * Admin email address used to separate:
     *   - Inbox  → receiver_email = admin
     *   - Sent   → sender_email   = admin
     *
     * Make sure the value matches what is stored in your DB.
     */
    private string $adminEmail = 'admin@example.com';


    /**
     * ============================================================
     * LIST EMAILS (Inbox / Sent / All)
     * ============================================================
     */
    public function index(): void
    {
        // Read the selected folder from URL:
        // ?folder=inbox  OR ?folder=sent  OR default = 'all'
        $folder = $_GET['folder'] ?? 'all';

        // Fetch emails from the model based on folder type
        // all(), inbox(), or sent()
        $emails        = EmailMessage::all($folder, $this->adminEmail);

        // Page title
        $title         = 'Email Management';

        // For keeping the filter selected
        $currentFolder = $folder;

        // Pass admin email to the view
        $adminEmail    = $this->adminEmail;

        // Render admin/views/emails/index.php with the data
        $this->render(
            'emails/index',
            compact('title', 'emails', 'currentFolder', 'adminEmail')
        );
    }


    /**
     * ============================================================
     * SHOW A SINGLE EMAIL
     * ============================================================
     */
    public function show(): void
    {
        // Get email ID from URL, e.g. ?id=14
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        // Invalid or missing ID → go back to list
        if ($id <= 0) {
            $this->redirect('c=emails&a=index');
        }

        // Fetch email from database
        $email = EmailMessage::find($id);

        // If email does not exist → redirect
        if (!$email) {
            $this->redirect('c=emails&a=index');
        }

        // If email is unread → mark as read in the database
        if (empty($email['is_read'])) {
            EmailMessage::markRead($id);
            $email['is_read'] = 1; // update local array so UI shows "Read"
        }

        // Title for top of page
        $title      = 'View email #' . $id;

        // Pass admin email to view (used to show incoming/outgoing)
        $adminEmail = $this->adminEmail;

        // Render detail page: admin/views/emails/show.php
        $this->render('emails/show', compact('title', 'email', 'adminEmail'));
    }


    /**
     * ============================================================
     * COMPOSE + SEND EMAIL (ADMIN → ANY USER)
     * ============================================================
     */
    public function compose(): void
    {
        // Errors array for validation messages
        $errors = [];

        // Default form values (used if page reloads after errors)
        $data = [
            'to'      => $_POST['to']      ?? '',
            'subject' => $_POST['subject'] ?? '',
            'body'    => $_POST['body']    ?? '',
        ];

        // Only process form when user submits POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            /* ----------------------------
               VALIDATE FORM FIELDS
               ---------------------------- */

            // Validate recipient email
            if (!filter_var($data['to'], FILTER_VALIDATE_EMAIL)) {
                $errors['to'] = 'Please enter a valid recipient email address.';
            }

            // Subject must not be empty
            if ($data['subject'] === '') {
                $errors['subject'] = 'Subject is required.';
            }

            // Message body must not be empty
            if ($data['body'] === '') {
                $errors['body'] = 'Message body is required.';
            }

            /* ----------------------------
               IF NO ERRORS: SEND EMAIL
               ---------------------------- */
            if (!$errors) {

                // Admin email is the "from" address
                $from = $this->adminEmail;

                // Standard email headers
                $headers  = 'From: ' . $from . "\r\n";
                $headers .= 'Reply-To: ' . $from . "\r\n";
                $headers .= 'X-Mailer: PHP/' . phpversion();

                // Send actual email (MailHog will catch it locally)
                @mail($data['to'], $data['subject'], $data['body'], $headers);

                // Save outgoing email into database
                EmailMessage::createOutgoing(
                    $data['to'],   // receiver
                    $from,         // sender (admin)
                    $data['subject'],
                    $data['body']
                );

                // After sending → go to Sent folder
                $this->redirect('c=emails&a=index&folder=sent');
                return;
            }
        }

        // Page title for compose form
        $title = 'Compose email';

        // Show the compose form with any validation errors
        $this->render('emails/compose', compact('title', 'data', 'errors'));
    }


    /**
     * ============================================================
     * MARK EMAIL AS READ (FROM LIST)
     * ============================================================
     */
    public function markRead(): void
    {
        // Only respond to POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Read email_id from hidden form field
            $id = (int)($_POST['email_id'] ?? 0);

            // If valid ID → update database
            if ($id > 0) {
                EmailMessage::markRead($id);
            }
        }

        // Go back to email list
        $this->redirect('c=emails&a=index');
    }
}
