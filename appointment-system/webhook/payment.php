<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Services\PaymentService;

$provider = $_GET['provider'] ?? $_POST['provider'] ?? 'iyzico';
$payload = $_POST ?: $_GET;

$result = (new PaymentService())->handleWebhook($provider, $payload);

if ($provider === 'paytr') {
    echo $result['success'] ? 'OK' : 'FAIL';
    exit;
}

header('Content-Type: application/json');
echo json_encode($result);
