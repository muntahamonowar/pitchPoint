<?php
declare(strict_types=1);
// Enables strict typing, making PHP more strict about type compatibility.
// This helps prevent hidden bugs and forces cleaner code.


// Load BaseAdminController from the same folder
require_once __DIR__ . '/BaseAdminController.php';
// __DIR__ gives the directory of THIS file.
// This loads BaseAdminController.php so we can extend it.


// Load User model
require_once __DIR__ . '/../models/User.php';
// Loads the User model from the "models" folder one level above.
// This lets us call User::filter(), User::deleteById(), etc.


class UsersController extends BaseAdminController
{
    /**
     * List / filter users.
     */
    public function index(): void
    {
        // Get filters from the URL query string (?role=..., ?active=..., ?search=...)
        $roleFilter   = $_GET['role']   ?? '';
        // Example: "staff", "investor", "admin", or empty string.

        $statusFilter = $_GET['active'] ?? '';
        // Example: "1" for active, "0" for inactive, or empty string.

        $searchFilter = $_GET['search'] ?? '';
        // Example: a name or email search keyword.

        // Call User::filter() to retrieve filtered user records.
        // If a filter is empty (''), we pass null to ignore it.
        $users = User::filter(
            $roleFilter   !== '' ? $roleFilter   : null,
            $statusFilter !== '' ? $statusFilter : null,
            $searchFilter !== '' ? $searchFilter : null
        );

        $title = 'Manage Users';
        // Title to be shown in the view.

        // Render the users/index.php view file.
        // compact() converts variable names into an associative array automatically.
        $this->render(
            'users/index',
            compact('title', 'users', 'roleFilter', 'statusFilter', 'searchFilter')
        );
    }


    /**
     * Handle actions from the "Action" column:
     *  - activate
     *  - deactivate
     *  - delete
     *
     * Expects POST: user_id, action, csrf_token
     */
    public function doAction(): void
    {
        // Only process POST requests AND require a valid CSRF token.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' ||
            !csrf_verify($_POST['csrf_token'] ?? '')
        ) {
            // Redirect back if invalid request.
            $this->redirect('c=users&a=index');
        }

        // Retrieve user_id from POST and convert to integer.
        $userId = (int)($_POST['user_id'] ?? 0);

        // Requested action: activate, deactivate, or delete
        $action = $_POST['action'] ?? '';

        // Validate user ID and action
        if ($userId <= 0 ||
            !in_array($action, ['activate', 'deactivate', 'delete'], true)
        ) {
            $this->redirect('c=users&a=index');
        }

        // Get currently logged-in admin information
        $current = admin_current();
        $adminId = $current ? (int)$current['admin_id'] : 0;
        // admin_current() returns an associative array with logged-in admin data.


        // Perform the requested action
        switch ($action) {

            case 'delete':
                // Delete user from database
                if (User::deleteById($userId)) {

                    // Log the action if an admin is logged in
                    if ($adminId) {
                        log_admin_activity(
                            $adminId,
                            "Deleted user #{$userId}"
                        );
                    }
                }
                break;


            case 'activate':
            case 'deactivate':
                // Determine whether we are activating (1) or deactivating (0) the user.
                $newStatus = ($action === 'activate') ? 1 : 0;

                // Update the user's status in database
                if (User::setStatus($userId, $newStatus)) {

                    // Log the action with correct wording
                    if ($adminId) {
                        $verb = $newStatus ? 'activated' : 'deactivated';
                        log_admin_activity(
                            $adminId,
                            "User #{$userId} {$verb}"
                        );
                    }
                }
                break;
        }

        // After processing action, always redirect back to user list
        $this->redirect('c=users&a=index');
    }
}
