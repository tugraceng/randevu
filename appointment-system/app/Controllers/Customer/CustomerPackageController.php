<?php

declare(strict_types=1);

namespace App\Controllers\Customer;

use App\Models\CustomerPackage;
use App\Models\Package;
use App\Services\PaymentService;

class CustomerPackageController
{
    public function index(): void
    {
        require_customer();
        view('customer/packages/index', [
            'title' => 'Paketlerim',
            'my_packages' => (new CustomerPackage())->forCustomer((int) customer_user()['id'], false),
            'available' => (new Package())->allActive(),
        ]);
    }

    public function buy(): void
    {
        require_verified_customer();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $result = (new PaymentService())->initiatePackagePayment(
            (int) customer_user()['id'],
            (int) ($_POST['package_id'] ?? 0)
        );
        if (!empty($result['checkout_url'])) {
            redirect($result['checkout_url']);
        }
        flash('error', $result['message'] ?? 'Ödeme başlatılamadı.');
        redirect(customer_url('?route=packages'));
    }
}
