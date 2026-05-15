<?php

declare(strict_types=1);

namespace App\Controllers\Customer;

use App\Models\Payment;

class CustomerPaymentController
{
    public function index(): void
    {
        require_customer();
        view('customer/payments/index', [
            'title' => 'Ödeme Geçmişim',
            'payments' => (new Payment())->forCustomer((int) customer_user()['id']),
        ]);
    }
}
