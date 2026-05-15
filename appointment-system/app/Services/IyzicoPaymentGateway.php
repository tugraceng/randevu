<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;

class IyzicoPaymentGateway implements PaymentGatewayInterface
{
    private $settings;

    public function __construct()
    {
        $this->settings = new Setting();
    }

    public function createPayment(array $payment, array $customer, array $meta = []): array
    {
        $apiKey = $this->settings->get('iyzico_api_key', '');
        $secret = $this->settings->get('iyzico_secret_key', '');
        $baseUrl = rtrim($this->settings->get('iyzico_base_url', 'https://sandbox-api.iyzipay.com'), '/');

        if (!$apiKey || !$secret) {
            return ['success' => false, 'message' => 'iyzico API anahtarları yapılandırılmamış.'];
        }

        $conversationId = 'PAY-' . $payment['id'] . '-' . time();
        $request = [
            'locale' => 'tr',
            'conversationId' => $conversationId,
            'price' => number_format((float) $payment['amount'], 2, '.', ''),
            'paidPrice' => number_format((float) $payment['amount'], 2, '.', ''),
            'currency' => $payment['currency'] ?? 'TRY',
            'basketId' => 'B' . $payment['id'],
            'paymentGroup' => 'PRODUCT',
            'callbackUrl' => ($meta['callback_url'] ?? base_url('webhook/payment.php')) . '?provider=iyzico',
            'enabledInstallments' => [1],
            'buyer' => [
                'id' => (string) $customer['id'],
                'name' => $customer['first_name'],
                'surname' => $customer['last_name'],
                'email' => $customer['email'],
                'identityNumber' => '11111111111',
                'registrationAddress' => 'Turkey',
                'city' => 'Istanbul',
                'country' => 'Turkey',
            ],
            'shippingAddress' => ['contactName' => $customer['first_name'], 'city' => 'Istanbul', 'country' => 'Turkey'],
            'billingAddress' => ['contactName' => $customer['first_name'], 'city' => 'Istanbul', 'country' => 'Turkey'],
            'basketItems' => [[
                'id' => (string) $payment['id'],
                'name' => $meta['item_name'] ?? 'Ödeme',
                'category1' => 'Randevu',
                'itemType' => 'VIRTUAL',
                'price' => number_format((float) $payment['amount'], 2, '.', ''),
            ]],
        ];

        $response = $this->request($baseUrl . '/payment/iyzipos/checkoutform/initialize/auth/ecom', $request, $apiKey, $secret);

        return [
            'success' => ($response['status'] ?? '') === 'success',
            'conversation_id' => $conversationId,
            'provider_payment_id' => $response['token'] ?? null,
            'checkout_url' => $response['paymentPageUrl'] ?? null,
            'raw' => $response,
        ];
    }

    public function verifyPayment(array $payload): array
    {
        $token = $payload['token'] ?? '';
        $apiKey = $this->settings->get('iyzico_api_key', '');
        $secret = $this->settings->get('iyzico_secret_key', '');
        $baseUrl = rtrim($this->settings->get('iyzico_base_url', 'https://sandbox-api.iyzipay.com'), '/');

        $response = $this->request($baseUrl . '/payment/iyzipos/checkoutform/auth/ecom/detail', [
            'locale' => 'tr',
            'token' => $token,
        ], $apiKey, $secret);

        $paid = ($response['paymentStatus'] ?? '') === 'SUCCESS';
        return [
            'success' => $paid,
            'conversation_id' => $response['conversationId'] ?? null,
            'provider_payment_id' => $response['paymentId'] ?? null,
            'raw' => $response,
        ];
    }

    public function refundPayment(array $payment, float $amount): array
    {
        return ['success' => false, 'message' => 'iyzico iade işlemi admin panelden manuel yapılmalıdır.'];
    }

    public function handleCallback(array $payload): array
    {
        return $this->verifyPayment($payload);
    }

    private function request(string $url, array $body, string $apiKey, string $secret): array
    {
        $json = json_encode($body, JSON_UNESCAPED_UNICODE);
        $auth = 'IYZWS ' . $apiKey . ':' . base64_encode(sha1($apiKey . $secret . $json, true));

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: ' . $auth,
            ],
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_TIMEOUT => 30,
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode((string) $result, true) ?: [];
    }
}
