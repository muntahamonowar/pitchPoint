<?php
declare(strict_types=1); // strict typing

require_once __DIR__ . '/../models/IdeaApproval.php'; // load model

/**
 * ApprovalsController
 * -------------------
 * Admin interface for:
 *   - listing idea approvals
 *   - updating decisions (Approved / Rejected / Pending)
 */
class ApprovalsController extends BaseAdminController
{
    /**
     * Show approvals list (with optional decision filter).
     */
    public function index(): void
    {
        // Read decision filter from query string (GET)
        $decision = $_GET['decision'] ?? '';

        // Ensure it's a string
        if (!is_string($decision)) {
            $decision = '';
        }

        // Only allow specific decision values; otherwise treat as "All"
        $allowed = ['Approved', 'Rejected', 'Pending'];
        if (!in_array($decision, $allowed, true)) {
            $decision = '';
        }

        // If a decision filter is set, pass it to model, else null = all
        $decisionFilter = $decision !== '' ? $decision : null;

        // Fetch approvals including admin + project info
        $approvals = IdeaApproval::allWithAdmin($decisionFilter);

        // Page title
        $title = 'Idea approvals';

        // Render admin/views/approvals/index.php
        $this->render(
            'approvals/index',
            compact('title', 'approvals', 'decision')
        );
    }

    /**
     * Handle form submission from the "Update" column.
     *
     * Expected POST fields:
     *   - approval_id
     *   - decision   (Approved / Rejected / Pending)
     *   - comments   (optional textarea)
     *   - csrf_token (for security)
     */
    public function update(): void
    {
        // Only allow POST requests and valid CSRF token
        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST' ||
            !csrf_verify($_POST['csrf_token'] ?? '')
        ) {
            $this->redirect('c=approvals&a=index');
        }

        // Get approval_id from POST and cast to int
        $approvalId = isset($_POST['approval_id'])
            ? (int)$_POST['approval_id']
            : 0;

        // Get decision as a string
        $decision = (string)($_POST['decision'] ?? '');

        // Optional comments field – if not set at all, we use null
        $comments = array_key_exists('comments', $_POST)
            ? (string)$_POST['comments']
            : null;

        // Only proceed if we have a valid approval ID
        if ($approvalId <= 0) {
            $this->redirect('c=approvals&a=index');
        }

        // Validate decision value
        $allowed = ['Approved', 'Rejected', 'Pending'];
        if (!in_array($decision, $allowed, true)) {
            $this->redirect('c=approvals&a=index');
        }

        // Get the currently logged-in admin from the session
        $admin   = admin_current();
        $adminId = isset($admin['admin_id']) ? (int)$admin['admin_id'] : 0;

        // Only update if we know which admin is doing this
        if ($adminId > 0) {
            IdeaApproval::updateDecision($approvalId, $decision, $comments, $adminId);
        }

        // Redirect back to the list (to avoid re-submitting the form)
        $this->redirect('c=approvals&a=index');
    }
}
