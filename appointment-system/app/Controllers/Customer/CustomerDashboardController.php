<?php

declare(strict_types=1);

namespace App\Controllers\Customer;

use App\Models\Appointment;
use App\Models\CustomerPackage;
use App\Models\Payment;

class CustomerDashboardController
{
    public function index(): void
    {
        require_customer();
        $id = (int) customer_user()['id'];
        view('customer/dashboard', [
            'title' => 'Müşteri Paneli',
            'appointments' => array_slice((new Appointment())->forCustomer($id), 0, 5),
            'packages' => (new CustomerPackage())->forCustomer($id),
            'payments' => array_slice((new Payment())->forCustomer($id), 0, 5),
        ]);
    }
}
