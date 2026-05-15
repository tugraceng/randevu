<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\CustomerPackage;
use App\Models\Service;
use App\Models\Staff;
use App\Services\AppointmentService;

class AdminAppointmentController
{
    public function index(): void
    {
        require_admin();
        view('admin/appointments/index', [
            'title' => 'Randevu Yönetimi',
            'appointments' => (new Appointment())->filter(['status' => $_GET['status'] ?? null]),
            'customers' => (new Customer())->paginate(1, 100)['data'],
            'services' => (new Service())->allActive(),
            'staff' => (new Staff())->allActive(),
        ]);
    }

    public function save(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $admin = admin_user();
        $result = (new AppointmentService())->create([
            'customer_id' => (int) $_POST['customer_id'],
            'service_id' => (int) $_POST['service_id'],
            'staff_id' => !empty($_POST['staff_id']) ? (int) $_POST['staff_id'] : null,
            'customer_package_id' => !empty($_POST['customer_package_id']) ? (int) $_POST['customer_package_id'] : null,
            'appointment_date' => $_POST['appointment_date'],
            'start_time' => $_POST['start_time'] . ':00',
            'status' => $_POST['status'] ?? 'approved',
            'source' => 'admin',
            'notes' => trim($_POST['notes'] ?? ''),
            'created_by_admin_id' => $admin['id'],
        ], true);

        flash($result['success'] ? 'success' : 'error', $result['message'] ?? ($result['success'] ? 'Randevu oluşturuldu.' : 'Hata'));
        redirect(admin_url('?route=appointments'));
    }

    public function updateStatus(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $id = (int) $_POST['id'];
        $status = $_POST['status'] ?? '';
        $appointment = (new Appointment())->find($id);
        (new AppointmentService())->updateStatus($id, $status, $appointment['status'] ?? null);
        flash('success', 'Durum güncellendi.');
        redirect(admin_url('?route=appointments'));
    }
}
