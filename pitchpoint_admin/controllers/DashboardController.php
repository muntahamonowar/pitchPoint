<?php
declare(strict_types=1);

// Load the Investment model (used for stats)
require_once __DIR__ . '/../models/Investment.php';

/**
 * DashboardController
 *
 * Controls what appears on the admin dashboard page.
 * Shows metrics (KPIs) and recent admin activity.
 */
class DashboardController extends BaseAdminController
{
    /**
     * Main dashboard page.
     * This method gathers all dashboard data, then renders the view.
     */
    public function index(): void
    {
        // Get database connection (from db.php helper)
        $pdo = db();

        /* ============================================================
           DASHBOARD KPI CARDS (TOP STATISTICS)
           ============================================================ */

        // Count all registered users in the system
        $totalUsers = (int)$pdo
            ->query("SELECT COUNT(*) FROM users")
            ->fetchColumn();

        // Count all projects in database (regardless of status)
        $totalProjects = (int)$pdo
            ->query("SELECT COUNT(*) FROM projects")
            ->fetchColumn();

        // Count only published projects
        $publishedProjects = (int)$pdo
            ->query("SELECT COUNT(*) FROM projects WHERE status = 'published'")
            ->fetchColumn();

        // Investment statistics (using Investment::stats() model function)
        $stats = Investment::stats();
        $totalInvestments = $stats['totalInvestments']; // Number of investment entries
        $totalAmount      = $stats['totalAmount'];      // Total invested money

        /* ============================================================
           RECENT ADMIN ACTIVITY LOG
           ============================================================ */
        //
        // IMPORTANT:
        // activity_log table uses column **admin_id** (not user_id)
        // and it stores the ID of the administrator who did the action.
        // We LEFT JOIN administrator to show admin_name on the dashboard.
        //

        $sql = "
            SELECT 
                l.activity_description,
                l.status,
                l.logged_at,
                a.admin_id,
                a.admin_name
            FROM activity_log l
            LEFT JOIN administrator a
                ON l.admin_id = a.admin_id
            ORDER BY l.logged_at DESC
            LIMIT 10
        ";

        // Run the query and fetch 10 recent actions
        $stmt = $pdo->query($sql);
        $recentActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /* ============================================================
           PASS ALL DATA TO THE VIEW
           ============================================================ */

        $title = 'Admin Dashboard';

        // Render the dashboard view using BaseAdminController
        $this->render('dashboard/index', compact(
            'title',
            'totalUsers',
            'totalProjects',
            'publishedProjects',
            'totalInvestments',
            'totalAmount',
            'recentActivities'
        ));
    }
}
