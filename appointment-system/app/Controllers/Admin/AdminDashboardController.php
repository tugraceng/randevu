<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\CustomerPackage;
use App\Models\MessageLog;
use App\Models\Payment;

class AdminDashboardController
{
    public function index(): void
    {
        require_admin();
        $apt = new Appointment();
        $msg = (new MessageLog())->countThisMonth();
        view('admin/dashboard', [
            'title' => 'Kontrol Paneli',
            'breadcrumb' => [['label' => 'Kontrol Paneli', 'url' => admin_url('?route=')]],
            'stats' => [
                'today' => $apt->countToday(),
                'today_pending' => $apt->countTodayByStatus('pending'),
                'today_completed' => $apt->countTodayByStatus('completed'),
                'today_cancelled' => $apt->countTodayByStatus('cancelled'),
                'pending' => $apt->countByStatus('pending'),
                'completed' => $apt->countByStatus('completed'),
                'cancelled' => $apt->countByStatus('cancelled'),
                'appointments' => $apt->countAll(),
                'customers' => (new Customer())->countAll(),
                'active_packages' => (new CustomerPackage())->countActive(),
                'critical_sessions' => (new CustomerPackage())->criticalCount(),
                'revenue' => (new Payment())->monthlyRevenue(),
                'pending_payments' => (new Payment())->countPending(),
                'new_customers' => (new Customer())->countNewThisMonth(),
                'sms_sent' => $msg['sms'] ?? 0,
                'whatsapp_sent' => $msg['whatsapp'] ?? 0,
                'email_sent' => $msg['email'] ?? 0,
            ],
            'upcoming' => $apt->upcoming(8),
            'recent_payments' => (new Payment())->recent(5),
        ]);
    }

    public function chartData(): void
    {
        require_admin();
        $apt = new Appointment();
        json_response([
            'daily' => $apt->chartDaily(7),
            'by_service' => $apt->chartByService(),
            'by_staff' => $apt->chartByStaff(),
            'payments' => (new Payment())->chartByStatus(),
            'packages' => $this->packageSalesChart(),
        ]);
    }

    private function packageSalesChart(): array
    {
        return db()->query(
            "SELECT p.name AS label, COUNT(cp.id) AS cnt FROM customer_packages cp
             JOIN packages p ON p.id = cp.package_id
             WHERE cp.purchase_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY cp.package_id ORDER BY cnt DESC LIMIT 6"
        )->fetchAll();
    }
}
