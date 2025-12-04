<?php
declare(strict_types=1); // Use strict typing for safer code

require_once __DIR__ . '/BaseModel.php'; // Load BaseModel so we can use self::db()

/**
 * IdeaApproval model
 * ------------------
 * Handles data for the idea_approval table:
 *   - listing approvals with project + admin info
 *   - updating a decision for a specific approval
 */
class IdeaApproval extends BaseModel
{
    /**
     * Get all approvals, including:
     *   - project title
     *   - admin name (if someone has approved/rejected it)
     *
     * @param string|null $decision Optional filter: 'Approved', 'Rejected', 'Pending' or null for all
     * @return array                List of approvals as associative arrays
     */
    public static function allWithAdmin(?string $decision = null): array
    {
        // Get PDO connection from BaseModel
        $pdo = self::db();

        // Base SQL query – joins idea_approval with projects and administrator
        $sql = "
            SELECT
                ia.approval_id,          -- primary key
                ia.project_id,           -- project being reviewed
                ia.decision,             -- Approved / Rejected / Pending
                ia.comments,             -- staff/admin comments
                ia.approval_date,        -- last time this record was updated
                ia.admin_id,             -- which admin last changed it
                p.title AS project_title,
                a.admin_name
            FROM idea_approval ia
            JOIN projects p
                ON ia.project_id = p.project_id
            LEFT JOIN administrator a
                ON ia.admin_id = a.admin_id
            WHERE 1 = 1
        ";

        // Parameters array for prepared statement
        $params = [];

        // If a valid decision filter is given, restrict the results
        if ($decision !== null && $decision !== '') {
            $sql .= " AND ia.decision = :decision";
            $params[':decision'] = $decision;
        }

        // Newest decisions first
        $sql .= " ORDER BY ia.approval_date DESC, ia.approval_id DESC";

        // Prepare and execute query
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Return all rows as associative arrays
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update the decision for a single approval row.
     *
     * @param int         $id       approval_id to update
     * @param string      $decision New decision: 'Approved', 'Rejected', 'Pending'
     * @param string|null $comments Optional comments text (null = keep existing)
     * @param int         $adminId  ID of admin performing this update
     */
    public static function updateDecision(
        int $id,
        string $decision,
        ?string $comments,
        int $adminId
    ): void {
        // Sanity: only allow specific decision values
        $allowed = ['Approved', 'Rejected', 'Pending'];
        if (!in_array($decision, $allowed, true)) {
            // If someone passes an invalid decision, stop silently
            return;
        }

        // Get PDO
        $pdo = self::db();

        // We always update decision and approval_date.
        // For comments:
        //   - if $comments is null, we keep the previous DB value (using COALESCE)
        //
        // NOTE about admin_id:
        //   - If the decision is Pending, we consider it "not decided yet",
        //     so we do NOT show an approver in the UI (view handles it).
        //   - We still store admin_id, because it shows who last touched it,
        //     but the view will only display the name when decision is NOT Pending.
        $sql = "
            UPDATE idea_approval
            SET
                decision      = :decision,
                comments      = COALESCE(:comments, comments),
                admin_id      = :admin_id,
                approval_date = NOW()
            WHERE approval_id = :id
        ";

        // Prepare the statement
        $stmt = $pdo->prepare($sql);

        // Execute with bound parameters
        $stmt->execute([
            ':decision' => $decision,
            // If $comments is null, COALESCE(comments) keeps old value
            ':comments' => $comments,
            ':admin_id' => $adminId,
            ':id'       => $id,
        ]);
    }
}
