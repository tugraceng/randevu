<?php

declare(strict_types=1);

namespace App\Controllers\Customer;

use App\Models\Appointment;
use App\Models\CustomerPackage;
use App\Models\Service;
use App\Models\Staff;
use App\Services\AppointmentService;

class CustomerAppointmentController
{
    public function index(): void
    {
        require_customer();
        view('customer/appointments/index', [
            'title' => 'Randevularım',
            'appointments' => (new Appointment())->forCustomer((int) customer_user()['id']),
        ]);
    }

    public function create(): void
    {
        require_verified_customer();
        view('customer/appointments/create', [
            'title' => 'Yeni Randevu',
            'services' => (new Service())->allActive(),
            'packages' => (new CustomerPackage())->forCustomer((int) customer_user()['id']),
        ]);
    }

    public function store(): void
    {
        require_verified_customer();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $result = (new AppointmentService())->create([
            'customer_id' => (int) customer_user()['id'],
            'service_id' => (int) $_POST['service_id'],
            'staff_id' => !empty($_POST['staff_id']) ? (int) $_POST['staff_id'] : null,
            'customer_package_id' => !empty($_POST['customer_package_id']) ? (int) $_POST['customer_package_id'] : null,
            'appointment_date' => $_POST['appointment_date'],
            'start_time' => $_POST['start_time'] . ':00',
            'notes' => trim($_POST['notes'] ?? ''),
        ]);
        flash($result['success'] ? 'success' : 'error', $result['message'] ?? '');
        redirect(customer_url('?route=appointments'));
    }
}
