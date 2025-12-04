<?php
declare(strict_types=1);
// Enables strict typing. PHP will now require proper type handling,
// which helps prevent bugs.

require_once __DIR__ . '/../models/Project.php';
// Loads the Project model file so we can call Project::filterByStatus(),
// Project::findForAdmin(), Project::updateStatus(), etc.


class ProjectsController extends BaseAdminController
{
    /**
     * List projects with optional status filter and search.
     *
     * This function loads the projects list page for the admin.
     * It supports:
     * - Filtering by project status (published, draft, archived)
     * - Searching by project title or entrepreneur name
     */
    public function index(): void
    {
        // Read "status" filter from query string (?status=published)
        $status = $_GET['status'] ?? '';

        // Read search keyword (?search=...) and trim spaces
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        // If status is empty string, treat it as null (no filter)
        $statusFilter = $status !== '' ? $status : null;

        // If search field is empty, ignore searching
        $searchFilter = $search !== '' ? $search : null;

        // Get filtered project list from model
        $projects = Project::filterByStatus($statusFilter, $searchFilter);

        // Page title
        $title = 'Manage Projects';

        // Render the admin view located at views/projects/index.php
        $this->render('projects/index', compact(
            'title',
            'projects',
            'status',
            'search'
        ));
    }


    /**
     * Show a specific project (admin view only).
     * Example URL: ?c=projects&a=show&id=3
     */
    public function show(): void
    {
        // Read project ID from query string
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        // If missing or invalid, redirect back to list
        if ($id <= 0) {
            $this->redirect('c=projects&a=index');
        }

        // Load full project details for admin
        $project = Project::findForAdmin($id);

        // If project doesn't exist, redirect back
        if (!$project) {
            $this->redirect('c=projects&a=index');
        }

        // Page title
        $title = 'Project details';

        // Show admin view (views/projects/show.php)
        $this->render('projects/show', compact('title', 'project'));
    }


    /**
     * Change status of a project (approve, reject, delete).
     * Expects POST:
     * - project_id
     * - action  (approve | reject | delete)
     */
    public function changeStatus(): void
    {
        // Only allow POST requests.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('c=projects&a=index');
        }

        // Read POST data
        $projectId = isset($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
        $action    = $_POST['action'] ?? '';

        // Validate input
        if ($projectId <= 0 || $action === '') {
            $this->redirect('c=projects&a=index');
        }

        $verb = ''; // will be used in logging

        // Decide what to do based on action type
        switch ($action) {

            case 'approve':
                // "approve" means publish the project
                Project::updateStatus($projectId, 'published');
                $verb = 'approved';
                break;

            case 'reject':
                // "reject" means archive the project
                Project::updateStatus($projectId, 'archived');
                $verb = 'rejected';
                break;

            case 'delete':
                // Permanently remove project
                Project::deleteById($projectId);
                $verb = 'deleted';
                break;

            default:
                // Unknown action → return to list
                $this->redirect('c=projects&a=index');
        }


        /**
         * LOG ADMIN ACTIVITY
         * Example log message:
         *   "Approved project 5"
         */
        if ($verb !== '') {
            $current = admin_current(); // admin session info

            if ($current) {
                log_admin_activity(
                    (int)$current['admin_id'], // which admin did it
                    ucfirst($verb) . " project {$projectId}" // message
                );
            }
        }

        // Always redirect back to project list after action is completed
        $this->redirect('c=projects&a=index');
    }
}
