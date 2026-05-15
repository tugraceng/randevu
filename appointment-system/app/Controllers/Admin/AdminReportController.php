<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

class AdminReportController
{
    public function index(): void
    {
        require_admin();
        $pdo = db();
        $revenueByMonth = $pdo->query(
            "SELECT DATE_FORMAT(paid_at,'%Y-%m') AS month, SUM(amount) AS total
             FROM payments WHERE status='paid' AND paid_at IS NOT NULL
             GROUP BY month ORDER BY month DESC LIMIT 12"
        )->fetchAll();

        $appointmentsByStatus = $pdo->query(
            "SELECT status, COUNT(*) AS cnt FROM appointments GROUP BY status"
        )->fetchAll();

        view('admin/reports/index', [
            'title' => 'Raporlama',
            'revenue' => $revenueByMonth,
            'appointments' => $appointmentsByStatus,
            'logs' => $pdo->query('SELECT * FROM system_logs ORDER BY id DESC LIMIT 30')->fetchAll(),
        ]);
    }
}
