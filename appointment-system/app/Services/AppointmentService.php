<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Appointment;

class AppointmentService
{
    public function create(array $data, bool $isAdmin = false): array
    {
        $availability = new AppointmentAvailabilityService();
        if (empty($data['end_time'])) {
            $data['end_time'] = $availability->calculateEndTime((int) $data['service_id'], $data['start_time']);
        }

        $check = $availability->validateBooking($data, $isAdmin);
        if (!$check['valid']) {
            return ['success' => false, 'message' => $check['message']];
        }

        $id = (new Appointment())->create($data);
        (new NotificationService())->sendAppointmentCreated($id);
        log_system('appointment_created', 'Randevu #' . $id, $isAdmin ? 'admin' : 'customer', $isAdmin ? ($data['created_by_admin_id'] ?? null) : (int) $data['customer_id']);

        return ['success' => true, 'appointment_id' => $id];
    }

    public function updateStatus(int $id, string $newStatus, ?string $oldStatus = null): array
    {
        $appointment = (new Appointment())->find($id);
        if (!$appointment) {
            return ['success' => false, 'message' => 'Randevu bulunamadı.'];
        }
        $oldStatus = $oldStatus ?? $appointment['status'];
        (new Appointment())->updateStatus($id, $newStatus);

        $sessionService = new PackageSessionService();
        $notify = new NotificationService();

        if ($newStatus === 'completed' && $oldStatus !== 'completed') {
            $sessionService->onAppointmentCompleted($id);
            if ($appointment['customer_package_id']) {
                $cp = (new \App\Models\CustomerPackage())->find((int) $appointment['customer_package_id']);
                if ($cp && (int) $cp['remaining_sessions'] <= 2) {
                    $notify->sendPackageRemaining((int) $appointment['customer_package_id']);
                }
            }
        }

        if ($newStatus === 'cancelled' && $oldStatus === 'completed') {
            $sessionService->onAppointmentCancelled($id, true);
        }

        if ($newStatus === 'approved') {
            $notify->sendAppointmentApproved($id);
        } elseif ($newStatus === 'cancelled') {
            $notify->sendAppointmentCancelled($id);
        }

        return ['success' => true];
    }
}
