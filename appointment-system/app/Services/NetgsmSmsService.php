<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MessageLog;
use App\Models\Setting;

class NetgsmSmsService
{
    private $settings;
    private $log;

    public function __construct()
    {
        $this->settings = new Setting();
        $this->log = new MessageLog();
    }

    public function isEnabled(): bool
    {
        return $this->settings->get('netgsm_status', '0') === '1';
    }

    public function send(string $phone, string $message, ?int $customerId = null, ?int $appointmentId = null, ?string $templateKey = null): bool
    {
        $logId = $this->log->create([
            'customer_id' => $customerId,
            'appointment_id' => $appointmentId,
            'channel' => 'sms',
            'recipient' => $phone,
            'template_key' => $templateKey,
            'message' => $message,
            'provider' => 'netgsm',
            'status' => 'pending',
        ]);

        if (!$this->isEnabled()) {
            $this->log->updateStatus($logId, 'failed', 'NetGSM devre dışı');
            return false;
        }

        $usercode = $this->settings->get('netgsm_usercode', '');
        $password = $this->settings->get('netgsm_password', '');
        $header = $this->settings->get('netgsm_header', '');
        $endpoint = $this->settings->get('netgsm_endpoint', config('sms')['endpoint']);

        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '9' . $phone;
        } elseif (!str_starts_with($phone, '90')) {
            $phone = '90' . $phone;
        }

        $url = $endpoint . '?' . http_build_query([
            'usercode' => $usercode,
            'password' => $password,
            'gsmno' => $phone,
            'message' => $message,
            'msgheader' => $header,
            'dil' => 'TR',
        ]);

        $response = @file_get_contents($url);
        $code = trim((string) $response);
        $success = $response !== false && str_starts_with($code, '00');

        $this->log->updateStatus($logId, $success ? 'sent' : 'failed', (string) $response);
        return $success;
    }
}
