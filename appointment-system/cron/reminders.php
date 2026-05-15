<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Services\NotificationService;

$hours = (int) ((new \App\Models\Setting())->get('reminder_hours', '24'));
$silentStart = (new \App\Models\Setting())->get('silent_mode_start', '22:00');
$silentEnd = (new \App\Models\Setting())->get('silent_mode_end', '08:00');
$now = date('H:i');

if ($now >= $silentStart || $now <= $silentEnd) {
    exit("Silent mode active\n");
}

$targetDate = date('Y-m-d', strtotime("+{$hours} hours"));
$stmt = db()->prepare(
    "SELECT id FROM appointments WHERE appointment_date = ? AND status IN ('pending','approved')"
);
$stmt->execute([$targetDate]);
$notify = new NotificationService();

while ($row = $stmt->fetch()) {
    $notify->sendAppointmentReminder((int) $row['id']);
    echo "Reminder sent for appointment #{$row['id']}\n";
}
