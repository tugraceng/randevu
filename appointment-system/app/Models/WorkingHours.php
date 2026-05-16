<?php

declare(strict_types=1);

namespace App\Models;

class WorkingHours extends BaseModel
{
    public const DAYS = [
        0 => 'Pazar',
        1 => 'Pazartesi',
        2 => 'Salı',
        3 => 'Çarşamba',
        4 => 'Perşembe',
        5 => 'Cuma',
        6 => 'Cumartesi',
    ];

    public function forStaff(?int $staffId): array
    {
        if ($staffId) {
            $stmt = $this->db->prepare('SELECT * FROM working_hours WHERE staff_id = ? ORDER BY day_of_week');
            $stmt->execute([$staffId]);
            $rows = $stmt->fetchAll();
            if ($rows) {
                return $this->indexedByDay($rows);
            }
        }
        $stmt = $this->db->query('SELECT * FROM working_hours WHERE staff_id IS NULL ORDER BY day_of_week');
        return $this->indexedByDay($stmt->fetchAll());
    }

    public function syncForStaff(int $staffId, array $schedule): void
    {
        $this->db->prepare('DELETE FROM working_hours WHERE staff_id = ?')->execute([$staffId]);
        $stmt = $this->db->prepare(
            'INSERT INTO working_hours (staff_id, day_of_week, start_time, end_time, is_closed) VALUES (?, ?, ?, ?, ?)'
        );
        foreach (self::DAYS as $dow => $label) {
            $day = $schedule[$dow] ?? $schedule[(string) $dow] ?? [];
            $closed = !empty($day['is_closed']);
            $start = $closed ? '00:00:00' : ($this->normalizeTime($day['start_time'] ?? '09:00') ?: '09:00:00');
            $end = $closed ? '00:00:00' : ($this->normalizeTime($day['end_time'] ?? '18:00') ?: '18:00:00');
            $stmt->execute([$staffId, $dow, $start, $end, $closed ? 1 : 0]);
        }
    }

    private function indexedByDay(array $rows): array
    {
        $out = [];
        foreach (self::DAYS as $dow => $label) {
            $out[$dow] = [
                'day_of_week' => $dow,
                'label' => $label,
                'start_time' => '09:00',
                'end_time' => '18:00',
                'is_closed' => 1,
            ];
        }
        foreach ($rows as $row) {
            $dow = (int) $row['day_of_week'];
            $out[$dow] = [
                'day_of_week' => $dow,
                'label' => self::DAYS[$dow] ?? '',
                'start_time' => substr((string) $row['start_time'], 0, 5),
                'end_time' => substr((string) $row['end_time'], 0, 5),
                'is_closed' => (int) $row['is_closed'],
            ];
        }
        return $out;
    }

    private function normalizeTime(string $time): string
    {
        $time = trim($time);
        if ($time === '') {
            return '';
        }
        if (strlen($time) === 5) {
            return $time . ':00';
        }
        return $time;
    }
}
