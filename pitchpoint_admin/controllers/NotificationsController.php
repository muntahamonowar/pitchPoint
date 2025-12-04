<?php
declare(strict_types=1);
// Enable strict typing for safer and clearer PHP behaviour.


// Load the Notification model,
// which handles fetching and updating notifications in the database.
require_once __DIR__ . '/../models/Notification.php';


/**
 * NotificationsController
 *
 * Handles:
 *   - listing all system notifications
 *   - marking notifications as read
 */
class NotificationsController extends BaseAdminController
{
    /**
     * ============================================================
     * LIST ALL NOTIFICATIONS
     * ============================================================
     *
     * This method loads all notifications from the database,
     * joins them with the user email,
     * and displays them in notifications/index view.
     */
    public function index(): void
    {
        // Fetch notifications, joined with user table (email, user_id)
        $notifications = Notification::allWithUser();

        // Title shown at the top of the page
        $title = 'Notifications';

        // Render the notifications page
        $this->render('notifications/index', compact('title','notifications'));
    }


    /**
     * ============================================================
     * MARK NOTIFICATION AS READ
     * ============================================================
     *
     * Called when admin clicks "mark as read" on a notification.
     * Only accepts POST requests for security.
     *
     * Also requires CSRF verification.
     */
    public function markRead(): void
    {
        // Check:
        // 1. Request must be POST
        // 2. CSRF token must be valid
        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST' ||
            !csrf_verify($_POST['csrf_token'] ?? '')
        ) {
            // If invalid, go back to notifications list
            $this->redirect('c=notifications&a=index');
        }

        // Sanitize and cast notification_id from POST
        $id = (int)($_POST['notification_id'] ?? 0);

        // If valid ID, mark it as read in database
        if ($id) {
            Notification::markRead($id);
        }

        // Redirect back after update (prevents resubmission)
        $this->redirect('c=notifications&a=index');
    }
}
