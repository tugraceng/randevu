<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\CustomerNote;
use App\Models\CustomerPackage;
use App\Models\Package;
use App\Models\Payment;
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

    public function show(): void
    {
        require_admin();
        $id = (int) ($_GET['id'] ?? 0);
        $customer = (new Customer())->find($id);
        if (!$customer) {
            flash('error', 'Müşteri bulunamadı.');
            redirect(admin_url('?route=customers'));
        }
        $msgStmt = db()->prepare('SELECT * FROM message_logs WHERE customer_id = ? ORDER BY id DESC LIMIT 30');
        $msgStmt->execute([$id]);
        view('admin/customers/show', [
            'title' => $customer['first_name'] . ' ' . $customer['last_name'],
            'breadcrumb' => [
                ['label' => 'Müşteriler', 'url' => admin_url('?route=customers')],
                ['label' => $customer['first_name'] . ' ' . $customer['last_name']],
            ],
            'customer' => $customer,
            'appointments' => (new Appointment())->forCustomer($id),
            'packages' => (new CustomerPackage())->forCustomer($id, false),
            'payments' => (new Payment())->forCustomer($id),
            'notes' => (new CustomerNote())->forCustomer($id),
            'messages' => $msgStmt->fetchAll(),
            'packages_catalog' => (new Package())->allActive(),
        ]);
    }

    public function save(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $id = (int) $_POST['id'];
        (new Customer())->update($id, [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'status' => (int) ($_POST['status'] ?? 1),
        ]);
        flash('success', 'Müşteri güncellendi.');
        redirect(admin_url('?route=customers/show&id=' . $id));
    }

    public function addNote(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $customerId = (int) $_POST['customer_id'];
        (new CustomerNote())->create($customerId, trim($_POST['note'] ?? ''), admin_user()['id'] ?? null);
        flash('success', 'Not eklendi.');
        redirect(admin_url('?route=customers/show&id=' . $customerId));
    }

    public function blacklist(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $id = (int) $_POST['customer_id'];
        (new Customer())->setBlacklist($id, !empty($_POST['blacklist']));
        flash('success', 'Kara liste güncellendi.');
        redirect(admin_url('?route=customers/show&id=' . $id));
    }
}
