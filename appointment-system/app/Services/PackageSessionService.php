<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Appointment;
use App\Models\CustomerPackage;
use PDO;

class PackageSessionService
{
    private $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function validateForBooking(int $customerPackageId): array
    {
        $cp = (new CustomerPackage())->find($customerPackageId);
        if (!$cp) {
            return ['valid' => false, 'message' => 'Paket bulunamadı.'];
        }
        if ($cp['status'] !== 'active') {
            return ['valid' => false, 'message' => 'Paket aktif değil.'];
        }
        if ((int) $cp['remaining_sessions'] <= 0) {
            return ['valid' => false, 'message' => 'Kalan seans yok.'];
        }
        if ($cp['expiry_date'] && $cp['expiry_date'] < date('Y-m-d')) {
            return ['valid' => false, 'message' => 'Paket süresi dolmuş.', 'expired' => true];
        }
        return ['valid' => true, 'package' => $cp];
    }

    public function onAppointmentCompleted(int $appointmentId): void
    {
        $appointment = (new Appointment())->find($appointmentId);
        if (!$appointment || !$appointment['customer_package_id']) {
            return;
        }
        $cpId = (int) $appointment['customer_package_id'];
        $cp = (new CustomerPackage())->find($cpId);
        if (!$cp) {
            return;
        }

        $used = (int) $cp['used_sessions'] + 1;
        $remaining = max(0, (int) $cp['remaining_sessions'] - 1);
        $status = $remaining <= 0 ? 'completed' : 'active';

        (new CustomerPackage())->updateSessions($cpId, $used, $remaining, $status);
        $this->log($cpId, $appointmentId, 'used', 1, 'Randevu tamamlandı - seans düşüldü');
    }

    public function onAppointmentCancelled(int $appointmentId, bool $wasCompleted = false): void
    {
        if (!$wasCompleted) {
            return;
        }
        $appointment = (new Appointment())->find($appointmentId);
        if (!$appointment || !$appointment['customer_package_id']) {
            return;
        }
        $cpId = (int) $appointment['customer_package_id'];
        $cp = (new CustomerPackage())->find($cpId);
        if (!$cp) {
            return;
        }

        $used = max(0, (int) $cp['used_sessions'] - 1);
        $remaining = (int) $cp['remaining_sessions'] + 1;
        (new CustomerPackage())->updateSessions($cpId, $used, $remaining, 'active');
        $this->log($cpId, $appointmentId, 'restored', 1, 'Randevu iptal - seans iade edildi');
    }

    public function log(int $cpId, ?int $appointmentId, string $action, int $count, string $note): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO package_session_logs (customer_package_id, appointment_id, action, session_count, note) VALUES (?,?,?,?,?)'
        );
        $stmt->execute([$cpId, $appointmentId, $action, $count, $note]);
    }

    public function manualAdjust(int $cpId, int $delta, string $note): array
    {
        $cp = (new CustomerPackage())->find($cpId);
        if (!$cp) {
            return ['success' => false, 'message' => 'Paket bulunamadı.'];
        }
        $remaining = (int) $cp['remaining_sessions'] + $delta;
        $used = (int) $cp['used_sessions'] - $delta;
        if ($remaining < 0 || $used < 0) {
            return ['success' => false, 'message' => 'Geçersiz seans işlemi.'];
        }
        $status = $remaining <= 0 ? 'completed' : 'active';
        (new CustomerPackage())->updateSessions($cpId, $used, $remaining, $status);
        $action = $delta > 0 ? 'manual_add' : 'manual_remove';
        $this->log($cpId, null, $action, abs($delta), $note ?: 'Manuel seans düzenleme');
        log_system('package_session_adjust', "Paket #$cpId delta $delta", 'admin', admin_user()['id'] ?? null);
        return ['success' => true];
    }

    public function sessionLogs(int $cpId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM package_session_logs WHERE customer_package_id = ? ORDER BY id DESC'
        );
        $stmt->execute([$cpId]);
        return $stmt->fetchAll();
    }
}
