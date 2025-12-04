<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Investment extends BaseModel
{
    public static function allWithRelations(): array
    {
        $sql = "
          SELECT i.investment_id, i.amount, i.payment_method, i.investment_date,
                 p.title AS project_title,
                 u.name AS investor_name
          FROM investments i
          JOIN projects p  ON p.project_id = i.project_id
          JOIN investors inv ON inv.investor_id = i.investor_id
          JOIN users u ON u.user_id = inv.user_id
          ORDER BY i.investment_date DESC
        ";
        return self::db()->query($sql)->fetchAll();
    }

    public static function stats(): array
    {
        $pdo = self::db();
        $totalInvestments = (int)$pdo->query("SELECT COUNT(*) FROM investments")->fetchColumn();
        $totalAmount      = (float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM investments")->fetchColumn();
        return compact('totalInvestments','totalAmount');
    }
}
