<?php
// app/models/Project.php
declare(strict_types=1);

require_once __DIR__ . '/../../config/db.php';

class Project
{
    public static function getPublicProjects(?string $q = null): array
    {
        $pdo = db();
        $sql = "
          SELECT p.project_id, p.title, p.summary, p.stage, p.budget, p.created_at,
                 c.category_name, e.company_name, u.name AS owner_name
          FROM projects p
          JOIN entrepreneurs e ON p.entrepreneur_id = e.entrepreneur_id
          JOIN users u ON e.user_id = u.user_id
          LEFT JOIN categories c ON p.category_id = c.category_id
          WHERE p.status = 'published' AND p.visibility = 'public'
        ";
        $params = [];
        if ($q !== null && $q !== '') {
            $sql .= " AND (p.title LIKE :q OR p.summary LIKE :q)";
            $params[':q'] = "%{$q}%";
        }
        $sql .= " ORDER BY p.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Public-safe fetch: only published + public */
    public static function getPublicById(int $id): ?array
    {
        $pdo = db();
        $sql = "
          SELECT p.*, c.category_name, e.company_name, u.name AS owner_name
          FROM projects p
          JOIN entrepreneurs e ON p.entrepreneur_id = e.entrepreneur_id
          JOIN users u ON e.user_id = u.user_id
          LEFT JOIN categories c ON p.category_id = c.category_id
          WHERE p.project_id = :id AND p.status = 'published' AND p.visibility = 'public'
          LIMIT 1
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    // Inside the Project class
    public static function getAll(?string $status = null): array
    {
        $pdo = db();
        $sql = "
          SELECT p.project_id, p.title, p.stage, p.category_id, c.category_name,
                 p.visibility, p.status, p.created_at, u.name AS owner
          FROM projects p
          JOIN entrepreneurs e ON p.entrepreneur_id = e.entrepreneur_id
          JOIN users u ON e.user_id = u.user_id
          LEFT JOIN categories c ON p.category_id = c.category_id
        ";
        $params = [];
        if ($status) {
            $sql .= " WHERE p.status = :status";
            $params[':status'] = $status;
        }
        $sql .= " ORDER BY p.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function review(int $projectId, int $staffId, string $decision, ?string $comments = null): void
    {
        $pdo = db();
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("
            INSERT INTO project_reviews (staff_id, project_id, decision, comments)
            VALUES (:sid, :pid, :d, :c)
        ");
        $stmt->execute([
            ':sid' => $staffId,
            ':pid' => $projectId,
            ':d'   => $decision,
            ':c'   => $comments ?: null,
        ]);

        // update project status
        if ($decision === 'approved') {
            $pdo->prepare("UPDATE projects SET status='published' WHERE project_id=:id")->execute([':id' => $projectId]);
        } elseif ($decision === 'rejected') {
            $pdo->prepare("UPDATE projects SET status='archived' WHERE project_id=:id")->execute([':id' => $projectId]);
        }
        $pdo->commit();
    }

}
    