<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Services\WhatsAppCloudApiService;

$service = new WhatsAppCloudApiService();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $challenge = $service->verifyWebhook(
        $_GET['hub_mode'] ?? '',
        $_GET['hub_verify_token'] ?? '',
        $_GET['hub_challenge'] ?? ''
    );
    if ($challenge !== null) {
        echo $challenge;
        exit;
    }
    http_response_code(403);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true) ?: [];
log_system('whatsapp_webhook', json_encode($payload), 'system');
http_response_code(200);
echo json_encode(['status' => 'ok']);
