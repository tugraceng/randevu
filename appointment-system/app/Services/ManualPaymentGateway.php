<?php

declare(strict_types=1);

namespace App\Services;

class ManualPaymentGateway implements PaymentGatewayInterface
{
    public function createPayment(array $payment, array $customer, array $meta = []): array
    {
        return [
            'success' => true,
            'conversation_id' => 'MANUAL-' . $payment['id'],
            'provider_payment_id' => 'MANUAL-' . $payment['id'],
            'checkout_url' => null,
            'manual' => true,
        ];
    }

    public function verifyPayment(array $payload): array
    {
        return ['success' => true, 'conversation_id' => $payload['conversation_id'] ?? null, 'raw' => $payload];
    }

    public function refundPayment(array $payment, float $amount): array
    {
        return ['success' => true, 'message' => 'Manuel iade kaydedildi.'];
    }

    public function handleCallback(array $payload): array
    {
        return $this->verifyPayment($payload);
    }
}
