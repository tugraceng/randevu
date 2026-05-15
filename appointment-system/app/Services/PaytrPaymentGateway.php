<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;

class PaytrPaymentGateway implements PaymentGatewayInterface
{
    private $settings;

    public function __construct()
    {
        $this->settings = new Setting();
    }

    public function createPayment(array $payment, array $customer, array $meta = []): array
    {
        $merchantId = $this->settings->get('paytr_merchant_id', '');
        $merchantKey = $this->settings->get('paytr_merchant_key', '');
        $merchantSalt = $this->settings->get('paytr_merchant_salt', '');

        if (!$merchantId || !$merchantKey || !$merchantSalt) {
            return ['success' => false, 'message' => 'PayTR yapılandırması eksik.'];
        }

        $conversationId = 'PAYTR-' . $payment['id'];
        $amountKurus = (int) round((float) $payment['amount'] * 100);
        $userBasket = base64_encode(json_encode([[$meta['item_name'] ?? 'Ödeme', number_format((float) $payment['amount'], 2, '.', ''), 1]]));

        $hashStr = $merchantId . ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1') . $conversationId . $customer['email']
            . $amountKurus . $userBasket . '0' . '0' . 'TRY' . '1' . $merchantSalt;
        $paytrToken = base64_encode(hash_hmac('sha256', $hashStr, $merchantKey, true));

        $post = [
            'merchant_id' => $merchantId,
            'user_ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'merchant_oid' => $conversationId,
            'email' => $customer['email'],
            'payment_amount' => $amountKurus,
            'paytr_token' => $paytrToken,
            'user_basket' => $userBasket,
            'debug_on' => 1,
            'no_installment' => 1,
            'max_installment' => 0,
            'user_name' => $customer['first_name'] . ' ' . $customer['last_name'],
            'user_address' => 'Turkey',
            'user_phone' => $customer['phone'] ?? '05550000000',
            'merchant_ok_url' => $meta['success_url'] ?? base_url('?payment=success'),
            'merchant_fail_url' => $meta['fail_url'] ?? base_url('?payment=fail'),
            'timeout_limit' => 30,
            'currency' => 'TL',
            'test_mode' => 1,
        ];

        $ch = curl_init('https://www.paytr.com/odeme/api/get-token');
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post]);
        $result = json_decode((string) curl_exec($ch), true) ?: [];
        curl_close($ch);

        return [
            'success' => ($result['status'] ?? '') === 'success',
            'conversation_id' => $conversationId,
            'provider_payment_id' => $result['token'] ?? null,
            'checkout_url' => isset($result['token']) ? 'https://www.paytr.com/odeme/guvenli/' . $result['token'] : null,
            'raw' => $result,
        ];
    }

    public function verifyPayment(array $payload): array
    {
        $merchantKey = $this->settings->get('paytr_merchant_key', '');
        $merchantSalt = $this->settings->get('paytr_merchant_salt', '');
        $hash = base64_encode(hash_hmac('sha256', ($payload['merchant_oid'] ?? '') . $merchantSalt . ($payload['status'] ?? '') . ($payload['total_amount'] ?? ''), $merchantKey, true));

        $valid = hash_equals($hash, $payload['hash'] ?? '');
        $paid = $valid && ($payload['status'] ?? '') === 'success';

        return [
            'success' => $paid,
            'conversation_id' => $payload['merchant_oid'] ?? null,
            'provider_payment_id' => $payload['merchant_oid'] ?? null,
            'raw' => $payload,
        ];
    }

    public function refundPayment(array $payment, float $amount): array
    {
        return ['success' => false, 'message' => 'PayTR iade API entegrasyonu ayrıca yapılandırılmalıdır.'];
    }

    public function handleCallback(array $payload): array
    {
        return $this->verifyPayment($payload);
    }
}
