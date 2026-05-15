<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use PDO;

class AppointmentAvailabilityService
{
    private $db;
    private $appointments;

    public function __construct()
    {
        $this->db = db();
        $this->appointments = new Appointment();
    }

    public function validateBooking(array $data, bool $isAdmin = false): array
    {
        $customer = (new Customer())->find((int) $data['customer_id']);
        if (!$customer) {
            return ['valid' => false, 'message' => 'Müşteri bulunamadı.'];
        }
        if ((int) $customer['is_blacklisted']) {
            return ['valid' => false, 'message' => 'Randevu alma yetkiniz bulunmuyor.'];
        }
        if (!$isAdmin && empty($customer['email_verified_at'])) {
            return ['valid' => false, 'message' => 'E-posta doğrulaması gerekli.'];
        }

        $service = (new Service())->find((int) $data['service_id']);
        if (!$service || !(int) $service['status']) {
            return ['valid' => false, 'message' => 'Hizmet bulunamadı.'];
        }

        if ($this->isHoliday($data['appointment_date'])) {
            return ['valid' => false, 'message' => 'Seçilen tarih tatil günüdür.'];
        }

        if (!$this->isWithinWorkingHours((int) ($data['staff_id'] ?? 0), $data['appointment_date'], $data['start_time'], $data['end_time'])) {
            return ['valid' => false, 'message' => 'Çalışma saatleri dışında randevu alınamaz.'];
        }

        if ($this->appointments->hasConflict(
            $data['staff_id'] ? (int) $data['staff_id'] : null,
            $data['appointment_date'],
            $data['start_time'],
            $data['end_time'],
            $data['exclude_id'] ?? null
        )) {
            return ['valid' => false, 'message' => 'Bu saat aralığı dolu.'];
        }

        if (!empty($data['customer_package_id'])) {
            $pkgCheck = (new PackageSessionService())->validateForBooking((int) $data['customer_package_id']);
            if (!$pkgCheck['valid']) {
                return $pkgCheck;
            }
        }

        return ['valid' => true, 'service' => $service, 'customer' => $customer];
    }

    public function calculateEndTime(int $serviceId, string $startTime): string
    {
        $service = (new Service())->find($serviceId);
        $minutes = (int) ($service['duration_minutes'] ?? 30);
        return date('H:i:s', strtotime($startTime) + ($minutes * 60));
    }

    public function getAvailableSlots(?int $staffId, int $serviceId, string $date): array
    {
        if ($this->isHoliday($date)) {
            return [];
        }
        $service = (new Service())->find($serviceId);
        $duration = (int) ($service['duration_minutes'] ?? 30);
        $dow = (int) date('w', strtotime($date));

        $hours = $this->getWorkingHours($staffId, $dow);
        if (!$hours || (int) $hours['is_closed']) {
            return [];
        }

        $slots = [];
        $start = strtotime($date . ' ' . $hours['start_time']);
        $end = strtotime($date . ' ' . $hours['end_time']);
        $step = $duration * 60;

        while ($start + $step <= $end) {
            $slotStart = date('H:i:s', $start);
            $slotEnd = date('H:i:s', $start + $step);
            if (!$this->appointments->hasConflict($staffId, $date, $slotStart, $slotEnd)) {
                $slots[] = ['start' => substr($slotStart, 0, 5), 'end' => substr($slotEnd, 0, 5)];
            }
            $start += 900;
        }
        return $slots;
    }

    private function isHoliday(string $date): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM holidays WHERE holiday_date = ? AND status = 1');
        $stmt->execute([$date]);
        return (int) $stmt->fetchColumn() > 0;
    }

    private function getWorkingHours(?int $staffId, int $dow): ?array
    {
        if ($staffId) {
            $stmt = $this->db->prepare('SELECT * FROM working_hours WHERE staff_id = ? AND day_of_week = ? LIMIT 1');
            $stmt->execute([$staffId, $dow]);
            $row = $stmt->fetch();
            if ($row) {
                return $row;
            }
        }
        $stmt = $this->db->prepare('SELECT * FROM working_hours WHERE staff_id IS NULL AND day_of_week = ? LIMIT 1');
        $stmt->execute([$dow]);
        return $stmt->fetch() ?: null;
    }

    private function isWithinWorkingHours(int $staffId, string $date, string $start, string $end): bool
    {
        $dow = (int) date('w', strtotime($date));
        $hours = $this->getWorkingHours($staffId ?: null, $dow);
        if (!$hours || (int) $hours['is_closed']) {
            return false;
        }
        return $start >= $hours['start_time'] && $end <= $hours['end_time'];
    }
}
