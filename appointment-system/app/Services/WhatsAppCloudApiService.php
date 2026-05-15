<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MessageLog;
use App\Models\Setting;

class WhatsAppCloudApiService
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
        return $this->settings->get('whatsapp_status', '0') === '1';
    }

    public function sendTemplate(string $phone, string $templateName, string $languageCode, array $bodyParams, ?int $customerId = null, ?int $appointmentId = null, ?string $templateKey = null): bool
    {
        $logId = $this->log->create([
            'customer_id' => $customerId,
            'appointment_id' => $appointmentId,
            'channel' => 'whatsapp',
            'recipient' => $phone,
            'template_key' => $templateKey,
            'message' => $templateName . ' | ' . json_encode($bodyParams, JSON_UNESCAPED_UNICODE),
            'provider' => 'meta_whatsapp',
            'status' => 'pending',
        ]);

        if (!$this->isEnabled()) {
            $this->log->updateStatus($logId, 'failed', 'WhatsApp devre dışı');
            return false;
        }

        $phone = preg_replace('/\D/', '', $phone);
        $version = $this->settings->get('whatsapp_api_version', 'v20.0');
        $phoneId = $this->settings->get('whatsapp_phone_number_id', '');
        $token = $this->settings->get('whatsapp_access_token', '');
        $url = "https://graph.facebook.com/{$version}/{$phoneId}/messages";

        $components = [];
        if ($bodyParams) {
            $components[] = [
                'type' => 'body',
                'parameters' => array_map(fn ($v) => ['type' => 'text', 'text' => (string) $v], array_values($bodyParams)),
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $languageCode],
                'components' => $components,
            ],
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token, 'Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode((string) $response, true) ?: [];
        $success = $httpCode >= 200 && $httpCode < 300;
        $msgId = $data['messages'][0]['id'] ?? null;

        $this->log->updateStatus($logId, $success ? 'sent' : 'failed', (string) $response, $msgId);
        return $success;
    }

    public function sendText(string $phone, string $message, ?int $customerId = null, ?int $appointmentId = null): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }
        $phone = preg_replace('/\D/', '', $phone);
        $version = $this->settings->get('whatsapp_api_version', 'v20.0');
        $phoneId = $this->settings->get('whatsapp_phone_number_id', '');
        $token = $this->settings->get('whatsapp_access_token', '');
        $url = "https://graph.facebook.com/{$version}/{$phoneId}/messages";

        $payload = ['messaging_product' => 'whatsapp', 'to' => $phone, 'type' => 'text', 'text' => ['body' => $message]];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token, 'Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return str_contains((string) $response, '"messages"');
    }

    public function verifyWebhook(string $mode, string $token, string $challenge): ?string
    {
        $verifyToken = $this->settings->get('whatsapp_verify_token', '');
        if ($mode === 'subscribe' && hash_equals($verifyToken, $token)) {
            return $challenge;
        }
        return null;
    }
}
