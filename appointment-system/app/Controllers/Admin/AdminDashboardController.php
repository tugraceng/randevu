<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\CustomerPackage;
use App\Models\Payment;

class AdminDashboardController
{
    public function index(): void
    {
        require_admin();
        view('admin/dashboard', [
            'title' => 'Kontrol Paneli',
            'stats' => [
                'appointments' => (new Appointment())->countAll(),
                'active_packages' => (new CustomerPackage())->countActive(),
                'revenue' => (new Payment())->monthlyRevenue(),
                'new_customers' => (new Customer())->countNewThisMonth(),
            ],
            'upcoming' => (new Appointment())->upcoming(8),
            'recent_payments' => (new Payment())->recent(5),
        ]);
    }
}
