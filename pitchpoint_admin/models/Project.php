<?php
declare(strict_types=1);
// Enables strict typing so PHP requires correct parameter and return types.


// BaseModel gives us the db() function → connects to database.
require_once __DIR__ . '/BaseModel.php';


class Project extends BaseModel
{
    /**
     * FILTER PROJECTS FOR ADMIN (Manage Projects page)
     *
     * @param string|null $status  filter by draft/published/archived OR null for all
     * @param string|null $search  keywords to search title, company, or owner
     * @return array               list of projects
     *
     * This function is used on:
     * admin/views/projects/index.php
     */
    public static function filterByStatus(?string $status, ?string $search = null): array
    {
        // Connect to DB using BaseModel's db() helper
        $pdo = self::db();

        // Base SQL: select projects + entrepreneur company + owner name
        $sql = "
            SELECT
                p.project_id,
                p.title,
                p.stage,
                p.status,
                p.visibility,
                e.company_name,
                u.name AS owner_name
            FROM projects p
            JOIN entrepreneurs e ON p.entrepreneur_id = e.entrepreneur_id
            JOIN users u         ON e.user_id = u.user_id
            WHERE 1 = 1
        ";
        // WHERE 1=1 is used to make adding AND conditions easier.

        $params = [];  // SQL parameters array


        // -----------------------------------------
        // STATUS FILTER (draft, published, archived)
        // -----------------------------------------
        if ($status !== null) {
            $sql .= " AND p.status = :status";
            $params[':status'] = $status;
        }


        // -----------------------------------------
        // TEXT SEARCH FILTER (title/company/owner)
        // -----------------------------------------
        if ($search !== null && $search !== '') {
            $sql .= "
                AND (
                    p.title        LIKE :search OR
                    e.company_name LIKE :search OR
                    u.name         LIKE :search
                )
            ";
            $params[':search'] = '%' . $search . '%';
        }


        // -----------------------------------------
        // ORDER RESULTS (latest first)
        // -----------------------------------------
        $sql .= " ORDER BY p.created_at DESC";


        // Prepare and run query
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Return all matching projects
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    /**
     * FETCH SINGLE PROJECT FOR ADMIN SHOW PAGE
     *
     * @param int $projectId
     * @return array|null   project data OR null if not found
     */
    public static function findForAdmin(int $projectId): ?array
    {
        $pdo = self::db();

        // Select the project plus company and owner name
        $sql = "
            SELECT
                p.*,
                e.company_name,
                u.name AS owner_name
            FROM projects p
            JOIN entrepreneurs e ON p.entrepreneur_id = e.entrepreneur_id
            JOIN users u         ON e.user_id = u.user_id
            WHERE p.project_id = :id
            LIMIT 1
        ";

        // Execute with project ID
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $projectId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // If no project found → return null
        return $row ?: null;
    }



    /**
     * UPDATE PROJECT STATUS
     *
     * Used for approving, rejecting, publishing, archiving.
     */
    public static function updateStatus(int $projectId, string $status): void
    {
        $pdo = self::db();

        // Update project status
        $stmt = $pdo->prepare("UPDATE projects SET status = ? WHERE project_id = ?");
        $stmt->execute([$status, $projectId]);
    }



    /**
     * DELETE PROJECT PERMANENTLY
     *
     * Make sure foreign keys allow deleting (CASCADE or manual cleanup).
     */
    public static function deleteById(int $projectId): void
    {
        $pdo = self::db();

        // Delete project row
        $stmt = $pdo->prepare("DELETE FROM projects WHERE project_id = ?");
        $stmt->execute([$projectId]);
    }



    /**
     * FRONT-END: Fetch only PUBLIC, APPROVED projects.
     *
     * Requirements:
     *  - status must be "published"
     *  - visibility must be "public"
     *  - must be approved by:
     *       admin   (idea_approval.decision = 'Approved')
     *       OR
     *       staff   (project_reviews.decision = 'approved')
     *
     * Optional:
     *  - $search filter (title, company, owner)
     *  - category filter
     */
    public static function findPublicApproved(
        ?string $search = null,
        ?int $categoryId = null
    ): array {
        $pdo = self::db();

        // Base SQL with lots of joins
        $sql = "
            SELECT
                p.project_id,
                p.title,
                p.summary,
                p.stage,
                p.budget,
                p.category_id,
                c.category_name,
                e.company_name,
                u.name AS owner_name,
                p.created_at
            FROM projects p

            -- JOIN company + user
            JOIN entrepreneurs e ON p.entrepreneur_id = e.entrepreneur_id
            JOIN users u         ON e.user_id = u.user_id

            -- Include category name
            LEFT JOIN categories c
                   ON p.category_id = c.category_id

            /* Admin approvals table */
            LEFT JOIN idea_approval ia
                   ON ia.project_id = p.project_id
                  AND ia.decision   = 'Approved'

            /* Staff review table */
            LEFT JOIN project_reviews pr
                   ON pr.project_id = p.project_id
                  AND pr.decision   = 'approved'

            WHERE p.status     = 'published'
              AND p.visibility = 'public'

              -- A project must have *either* admin approval OR staff approval
              AND (
                    ia.approval_id IS NOT NULL
                 OR pr.review_id   IS NOT NULL
              )
        ";

        $params = [];


        // ----------------------------
        // FILTER BY CATEGORY (optional)
        // ----------------------------
        if ($categoryId !== null && $categoryId > 0) {
            $sql .= " AND p.category_id = :cat_id";
            $params[':cat_id'] = $categoryId;
        }


        // ----------------------------
        // SEARCH FILTER (optional)
        // ----------------------------
        if ($search !== null && $search !== '') {
            $sql .= "
                AND (
                    p.title        LIKE :search OR
                    e.company_name LIKE :search OR
                    u.name         LIKE :search
                )
            ";
            $params[':search'] = '%' . $search . '%';
        }


        // Latest approved projects first
        $sql .= " ORDER BY p.created_at DESC";


        // Prepare + execute final SQL
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Return all results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
