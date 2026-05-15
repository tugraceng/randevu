<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Customer;
use App\Models\CustomerPackage;
use App\Models\Package;
use App\Services\PaymentService;

class AdminCustomerController
{
    public function index(): void
    {
        require_admin();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        view('admin/customers/index', [
            'title' => 'Müşteri ve Paket Yönetimi',
            'customers' => (new CustomerPackage())->withCustomers($page),
            'packages' => (new Package())->allActive(),
            'critical' => (new CustomerPackage())->criticalCount(),
            'total_customers' => (new Customer())->countAll(),
            'active_packages' => (new CustomerPackage())->countActive(),
            'revenue' => (new \App\Models\Payment())->monthlyRevenue(),
        ]);
    }

    public function assignPackage(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $customerId = (int) ($_POST['customer_id'] ?? 0);
        $packageId = (int) ($_POST['package_id'] ?? 0);
        $method = $_POST['payment_method'] ?? 'manual';

        if ($method === 'online') {
            $result = (new PaymentService())->initiatePackagePayment($customerId, $packageId);
            if (!empty($result['checkout_url'])) {
                flash('success', 'Ödeme linki oluşturuldu.');
            }
        } else {
            $package = (new Package())->find($packageId);
            (new PaymentService())->recordManual($customerId, (float) $package['price'], 'package', $packageId);
            flash('success', 'Paket manuel olarak tanımlandı.');
        }
        redirect(admin_url('?route=customers'));
    }
}
