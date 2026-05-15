<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Staff;
use App\Services\AppointmentAvailabilityService;
use App\Services\AppointmentService;

class AppointmentController
{
    public function slots(): void
    {
        $staffId = !empty($_GET['staff_id']) ? (int) $_GET['staff_id'] : null;
        $serviceId = (int) ($_GET['service_id'] ?? 0);
        $date = $_GET['date'] ?? '';
        $slots = (new AppointmentAvailabilityService())->getAvailableSlots($staffId, $serviceId, $date);
        json_response(['success' => true, 'slots' => $slots]);
    }

    public function store(): void
    {
        if (!verify_csrf()) {
            csrf_abort();
        }
        if (!is_customer_logged_in()) {
            json_response(['success' => false, 'message' => 'Giriş yapmalısınız.', 'redirect' => customer_url('?route=login')]);
        }
        $user = customer_user();
        if (empty($user['email_verified_at'])) {
            json_response(['success' => false, 'message' => 'E-posta doğrulaması gerekli.', 'redirect' => customer_url('?route=verify-email')]);
        }
        $result = (new AppointmentService())->create([
            'customer_id' => $user['id'],
            'service_id' => (int) $_POST['service_id'],
            'staff_id' => !empty($_POST['staff_id']) ? (int) $_POST['staff_id'] : null,
            'customer_package_id' => !empty($_POST['customer_package_id']) ? (int) $_POST['customer_package_id'] : null,
            'appointment_date' => $_POST['appointment_date'],
            'start_time' => $_POST['start_time'] . ':00',
            'source' => 'website',
            'notes' => trim($_POST['notes'] ?? ''),
        ]);

        json_response($result);
    }
}
