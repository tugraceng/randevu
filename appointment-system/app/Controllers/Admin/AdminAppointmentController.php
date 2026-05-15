<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\CustomerPackage;
use App\Models\Service;
use App\Models\Staff;
use App\Services\AppointmentAvailabilityService;
use App\Services\AppointmentService;
use App\Services\NotificationService;

class AdminAppointmentController
{
    public function index(): void
    {
        require_admin();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $filters = [
            'status' => $_GET['status'] ?? null,
            'date' => $_GET['date'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null,
            'service_id' => $_GET['service_id'] ?? null,
            'staff_id' => $_GET['staff_id'] ?? null,
            'payment_status' => $_GET['payment_status'] ?? null,
            'package_only' => $_GET['package_only'] ?? '',
            'search' => $_GET['search'] ?? null,
        ];
        view('admin/appointments/index', [
            'title' => 'Randevu Yönetimi',
            'breadcrumb' => [
                ['label' => 'Kontrol Paneli', 'url' => admin_url('?route=')],
                ['label' => 'Randevular'],
            ],
            'result' => (new Appointment())->filter($filters, $page),
            'filters' => $filters,
            'services' => (new Service())->allActive(),
            'staff' => (new Staff())->allActive(),
        ]);
    }

    public function create(): void
    {
        require_admin();
        view('admin/appointments/create', [
            'title' => 'Yeni Randevu',
            'breadcrumb' => [
                ['label' => 'Randevular', 'url' => admin_url('?route=appointments')],
                ['label' => 'Yeni'],
            ],
            'customers' => (new Customer())->paginate(1, 200)['data'],
            'services' => (new Service())->allActive(),
            'staff' => (new Staff())->allActive(),
        ]);
    }

    public function show(): void
    {
        require_admin();
        $id = (int) ($_GET['id'] ?? 0);
        $appointment = (new Appointment())->find($id);
        if (!$appointment) {
            flash('error', 'Randevu bulunamadı.');
            redirect(admin_url('?route=appointments'));
        }
        view('admin/appointments/show', [
            'title' => 'Randevu #' . $id,
            'breadcrumb' => [
                ['label' => 'Randevular', 'url' => admin_url('?route=appointments')],
                ['label' => '#' . $id],
            ],
            'appointment' => $appointment,
        ]);
    }

    public function edit(): void
    {
        require_admin();
        $id = (int) ($_GET['id'] ?? 0);
        $appointment = (new Appointment())->find($id);
        if (!$appointment) {
            flash('error', 'Randevu bulunamadı.');
            redirect(admin_url('?route=appointments'));
        }
        view('admin/appointments/edit', [
            'title' => 'Randevu Düzenle',
            'appointment' => $appointment,
            'customers' => (new Customer())->paginate(1, 200)['data'],
            'services' => (new Service())->allActive(),
            'staff' => (new Staff())->allActive(),
            'packages' => (new CustomerPackage())->forCustomer((int) $appointment['customer_id'], true),
        ]);
    }

    public function calendar(): void
    {
        require_admin();
        $month = $_GET['month'] ?? date('Y-m');
        view('admin/appointments/calendar', [
            'title' => 'Randevu Takvimi',
            'month' => $month,
            'events' => (new Appointment())->forCalendar($month),
        ]);
    }

    public function customerPackages(): void
    {
        require_admin();
        $customerId = (int) ($_GET['customer_id'] ?? 0);
        json_response(['packages' => (new CustomerPackage())->forCustomer($customerId, true)]);
    }

    public function save(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $admin = admin_user();
        $notify = !empty($_POST['notify_mail']) || !empty($_POST['notify_sms']) || !empty($_POST['notify_whatsapp']);
        $result = (new AppointmentService())->create([
            'customer_id' => (int) $_POST['customer_id'],
            'service_id' => (int) $_POST['service_id'],
            'staff_id' => !empty($_POST['staff_id']) ? (int) $_POST['staff_id'] : null,
            'customer_package_id' => !empty($_POST['customer_package_id']) ? (int) $_POST['customer_package_id'] : null,
            'appointment_date' => $_POST['appointment_date'],
            'start_time' => $_POST['start_time'] . (strlen($_POST['start_time']) === 5 ? ':00' : ''),
            'status' => $_POST['status'] ?? 'approved',
            'source' => 'admin',
            'notes' => trim($_POST['notes'] ?? ''),
            'payment_required' => !empty($_POST['payment_required']) ? 1 : 0,
            'payment_status' => $_POST['payment_status'] ?? 'not_required',
            'deposit_amount' => (float) ($_POST['deposit_amount'] ?? 0),
            'created_by_admin_id' => $admin['id'],
        ], true);

        if ($result['success'] && !$notify) {
            // AppointmentService zaten varsayılan bildirim gönderir; notify kapalıysa özelleştirme ileride eklenebilir
        }

        flash($result['success'] ? 'success' : 'error', $result['message'] ?? ($result['success'] ? 'Randevu oluşturuldu.' : 'Hata'));
        redirect(admin_url('?route=appointments'));
    }

    public function update(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $id = (int) $_POST['id'];
        $availability = new AppointmentAvailabilityService();
        $endTime = $availability->calculateEndTime((int) $_POST['service_id'], $_POST['start_time'] . ':00');
        (new Appointment())->update($id, [
            'customer_id' => (int) $_POST['customer_id'],
            'service_id' => (int) $_POST['service_id'],
            'staff_id' => !empty($_POST['staff_id']) ? (int) $_POST['staff_id'] : null,
            'customer_package_id' => !empty($_POST['customer_package_id']) ? (int) $_POST['customer_package_id'] : null,
            'appointment_date' => $_POST['appointment_date'],
            'start_time' => $_POST['start_time'] . ':00',
            'end_time' => $endTime,
            'status' => $_POST['status'],
            'payment_required' => !empty($_POST['payment_required']) ? 1 : 0,
            'payment_status' => $_POST['payment_status'] ?? 'not_required',
            'deposit_amount' => (float) ($_POST['deposit_amount'] ?? 0),
            'notes' => trim($_POST['notes'] ?? ''),
        ]);
        flash('success', 'Randevu güncellendi.');
        redirect(admin_url('?route=appointments/show&id=' . $id));
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
        redirect($_POST['redirect'] ?? admin_url('?route=appointments'));
    }

    public function updateNote(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        (new Appointment())->updateNotes((int) $_POST['id'], trim($_POST['notes'] ?? ''));
        flash('success', 'Not kaydedildi.');
        redirect($_POST['redirect'] ?? admin_url('?route=appointments'));
    }

    public function markPaid(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $id = (int) $_POST['id'];
        (new Appointment())->updatePaymentStatus($id, 'paid');
        flash('success', 'Ödeme alındı olarak işaretlendi.');
        redirect($_POST['redirect'] ?? admin_url('?route=appointments'));
    }

    public function sendMessage(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $id = (int) $_POST['appointment_id'];
        $channel = $_POST['channel'] ?? 'sms';
        $appointment = (new Appointment())->find($id);
        if (!$appointment) {
            flash('error', 'Randevu bulunamadı.');
            redirect(admin_url('?route=appointments'));
        }
        $notify = new NotificationService();
        if ($channel === 'whatsapp') {
            $notify->sendAppointmentReminder($id);
        } elseif ($channel === 'email') {
            $notify->sendAppointmentApproved($id);
        } else {
            $notify->sendAppointmentReminder($id);
        }
        flash('success', 'Mesaj kuyruğa alındı.');
        redirect($_POST['redirect'] ?? admin_url('?route=appointments/show&id=' . $id));
    }
}
