<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\PaymentService;

class PaymentController
{
    public function buyPackage(): void
    {
        if (!verify_csrf()) {
            csrf_abort();
        }
        require_verified_customer();
        $result = (new PaymentService())->initiatePackagePayment(
            (int) customer_user()['id'],
            (int) ($_POST['package_id'] ?? 0)
        );
        if ($result['success'] && !empty($result['checkout_url'])) {
            redirect($result['checkout_url']);
        }
        flash('error', $result['message'] ?? 'Ödeme başlatılamadı.');
        redirect(customer_url('?route=packages'));
    }
}
