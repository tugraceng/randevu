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

    public function export(): void
    {
        require_admin();
        $from = $_GET['from'] ?? date('Y-m-01');
        $to = $_GET['to'] ?? date('Y-m-d');
        $stmt = db()->prepare(
            "SELECT a.appointment_date, a.start_time, a.status, a.payment_status,
                    CONCAT(c.first_name,' ',c.last_name) AS customer, s.name AS service, st.name AS staff
             FROM appointments a
             JOIN customers c ON c.id = a.customer_id
             JOIN services s ON s.id = a.service_id
             LEFT JOIN staff st ON st.id = a.staff_id
             WHERE a.appointment_date BETWEEN ? AND ? ORDER BY a.appointment_date"
        );
        $stmt->execute([$from, $to]);
        $rows = $stmt->fetchAll();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=randevu-rapor-' . date('Y-m-d') . '.csv');
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['Tarih', 'Saat', 'Müşteri', 'Hizmet', 'Personel', 'Durum', 'Ödeme']);
        foreach ($rows as $r) {
            fputcsv($out, [$r['appointment_date'], $r['start_time'], $r['customer'], $r['service'], $r['staff'] ?? '', $r['status'], $r['payment_status']]);
        }
        fclose($out);
        exit;
    }
}
