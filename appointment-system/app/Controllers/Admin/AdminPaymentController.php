<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Customer;
use App\Models\Payment;
use App\Services\PaymentService;

class AdminPaymentController
{
    public function index(): void
    {
        require_admin();
        $stmt = db()->query('SELECT p.*, CONCAT(c.first_name," ",c.last_name) AS customer_name FROM payments p JOIN customers c ON c.id=p.customer_id ORDER BY p.id DESC LIMIT 50');
        view('admin/payments/index', [
            'title' => 'Ödeme Yönetimi',
            'payments' => $stmt->fetchAll(),
            'customers' => (new Customer())->paginate(1, 100)['data'],
        ]);
    }

    public function manual(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        (new PaymentService())->recordManual(
            (int) $_POST['customer_id'],
            (float) $_POST['amount'],
            $_POST['payment_type'] ?? 'manual',
            !empty($_POST['package_id']) ? (int) $_POST['package_id'] : null,
            !empty($_POST['appointment_id']) ? (int) $_POST['appointment_id'] : null
        );
        flash('success', 'Manuel ödeme kaydedildi.');
        redirect(admin_url('?route=payments'));
    }
}
