<?php
// app/controllers/staffcontroller.php
declare(strict_types=1);

//show staff dashboard,show the review page,approve/reject projects
class StaffController
{
    private PDO $db;//store db connection for running queries in all methods

    public function __construct(PDO $db)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = $db;
    }

    public static function requireStaff(): void//checks if staff if logged in from the auth folder , if not then redirects to the login page
    { 
        require_once __DIR__ . '/../../../auth/helpers/staff_auth.php';
        require_staff();
    }
//show staff dashboard +ensures the staff is logged in + fetch matching projects fromm db
//pass data to the staff dashboard 
public function dashboard(): void
{
    self::requireStaff();
    //  show projects that are in "draft" = pending review
    $filter = $_GET['status'] ?? 'draft';
    $allowedStatus = ['draft', 'published', 'rejected', 'archived'];
    $params = [];
//fetch projects with the owners name
    $sql = " 
        SELECT 
            p.project_id,
            p.title,
            p.stage,
            p.status,
            p.created_at,
            u.name AS owner_name
        FROM projects p
        JOIN entrepreneurs e ON e.entrepreneur_id = p.entrepreneur_id
        JOIN users u ON e.user_id = u.user_id
        WHERE 1=1
    ";

    if (in_array($filter, $allowedStatus, true)) {
        $sql .= " AND p.status = :status";
        $params[':status'] = $filter;
    }

    $sql .= " ORDER BY p.created_at DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // tells the view which tab is active
    $currentStatusFilter = in_array($filter, $allowedStatus, true) ? $filter : 'draft';

    require __DIR__ . '/../views/staff/dashboard.php';
}


/**
 * the review page
 * GET  -> show project details + form
 * POST -> save decision + comment, redirect back to dashboard
 */
public function review(): void
{
    self::requireStaff();

    $projectId = (int)($_GET['id'] ?? 0);
    $error = '';

    if ($projectId <= 0) {
        header('Location: /pitchPoint/pitchpoint_staff/public/staff.php');
        exit;
    }

    // Handle POST (decision + comment)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $decision = $_POST['decision'] ?? '';
        $comment  = trim($_POST['comment'] ?? '');

        // your form currently sends 'approve' / 'reject'
        if (!in_array($decision, ['approve', 'reject'], true)) {
            $error = 'Please choose approve or reject.';
        } elseif ($decision === 'reject' && $comment === '') {
            $error = 'Please provide a reason for rejection.';
        } else {
            // Map decision (form) -> project status
            $newStatus = $decision === 'approve' ? 'published' : 'rejected';

            // Map decision (form) 
            // enum is: 'approved','rejected','changes_requested'
            $decisionForDb = $decision === 'approve' ? 'approved' : 'rejected';

            // Update project status
            $stmt = $this->db->prepare("
                UPDATE projects
                SET status = :st
                WHERE project_id = :id
            ");
            $stmt->execute([
                ':st' => $newStatus,
                ':id' => $projectId,
            ]);

            // Save review row in project_reviews
            $stmt2 = $this->db->prepare("
                INSERT INTO project_reviews (project_id, staff_id, decision, comments, created_at)
                VALUES (:pid, :sid, :decision, :comments, NOW())
            ");
            $stmt2->execute([
                ':pid'      => $projectId,
                ':sid'      => (int)($_SESSION['staff_id'] ?? 0),
                ':decision' => $decisionForDb,
                ':comments' => $comment,
            ]);

            header('Location: /pitchPoint/pitchpoint_staff/public/staff.php');
            exit;
        }
    }

    // Load project details 
    $stmt = $this->db->prepare("
        SELECT 
            p.project_id,
            p.title,
            p.summary,
            p.stage,
            p.status,
            p.budget,
            p.created_at,
            u.name  AS owner_name,
            e.company_name
        FROM projects p
        JOIN entrepreneurs e ON e.entrepreneur_id = p.entrepreneur_id
        JOIN users u        ON u.user_id = e.user_id
        WHERE p.project_id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $projectId]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        header('Location: /pitchPoint/pitchpoint_staff/public/staff.php');
        exit;
    }

    require __DIR__ . '/../views/staff/review.php';
}



}
