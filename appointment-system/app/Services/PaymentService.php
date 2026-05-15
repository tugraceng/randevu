<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\CustomerPackage;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Setting;

class PaymentService
{
    private $paymentModel;
    private $settings;

    public function __construct()
    {
        $this->paymentModel = new Payment();
        $this->settings = new Setting();
    }

    public function gateway(?string $provider = null): PaymentGatewayInterface
    {
        $provider = $provider ?? $this->settings->get('payment_provider', 'iyzico');
        if ($provider === 'paytr') {
            return new PaytrPaymentGateway();
        }
        if ($provider === 'manual') {
            return new ManualPaymentGateway();
        }
        return new IyzicoPaymentGateway();
    }

    public function initiatePackagePayment(int $customerId, int $packageId): array
    {
        $customer = (new Customer())->find($customerId);
        $package = (new Package())->find($packageId);
        if (!$customer || !$package) {
            return ['success' => false, 'message' => 'Kayıt bulunamadı.'];
        }

        $cpId = (new CustomerPackage())->create([
            'customer_id' => $customerId,
            'package_id' => $packageId,
            'total_sessions' => $package['session_count'],
            'remaining_sessions' => $package['session_count'],
            'purchase_date' => date('Y-m-d'),
            'expiry_date' => date('Y-m-d', strtotime('+' . $package['validity_days'] . ' days')),
            'payment_status' => 'pending',
            'status' => 'active',
        ]);

        $paymentId = $this->paymentModel->create([
            'customer_id' => $customerId,
            'customer_package_id' => $cpId,
            'provider' => $this->settings->get('payment_provider', 'iyzico'),
            'payment_type' => 'package',
            'amount' => $package['price'],
        ]);

        $payment = $this->paymentModel->find($paymentId);
        $result = $this->gateway()->createPayment($payment, $customer, [
            'item_name' => $package['name'],
            'callback_url' => base_url('../webhook/payment.php'),
        ]);

        if (!empty($result['conversation_id'])) {
            db()->prepare('UPDATE payments SET provider_conversation_id=? WHERE id=?')
                ->execute([$result['conversation_id'], $paymentId]);
        }

        return array_merge($result, ['payment_id' => $paymentId, 'customer_package_id' => $cpId]);
    }

    public function initiateAppointmentPayment(int $appointmentId, string $type = 'appointment'): array
    {
        $appointment = (new Appointment())->find($appointmentId);
        $customer = (new Customer())->find((int) $appointment['customer_id']);
        if (!$appointment || !$customer) {
            return ['success' => false, 'message' => 'Randevu bulunamadı.'];
        }

        $amount = $type === 'deposit' ? (float) $appointment['deposit_amount'] : (float) ($appointment['service_price'] ?? 0);
        $paymentId = $this->paymentModel->create([
            'customer_id' => $customer['id'],
            'appointment_id' => $appointmentId,
            'provider' => $this->settings->get('payment_provider', 'iyzico'),
            'payment_type' => $type,
            'amount' => $amount,
        ]);

        $payment = $this->paymentModel->find($paymentId);
        $result = $this->gateway()->createPayment($payment, $customer, [
            'item_name' => $appointment['service_name'] ?? 'Randevu Ödemesi',
        ]);

        return array_merge($result, ['payment_id' => $paymentId]);
    }

    public function completePayment(int $paymentId, ?string $providerPaymentId = null, ?string $payload = null): void
    {
        $payment = $this->paymentModel->find($paymentId);
        if (!$payment || $payment['status'] === 'paid') {
            return;
        }

        $this->paymentModel->markPaid($paymentId, $providerPaymentId, $payload);

        if ($payment['customer_package_id']) {
            (new CustomerPackage())->activate((int) $payment['customer_package_id'], $paymentId);
        }
        if ($payment['appointment_id']) {
            (new Appointment())->updatePaymentStatus((int) $payment['appointment_id'], 'paid');
        }

        (new NotificationService())->sendPaymentSuccess($paymentId);
        log_system('payment_completed', 'Ödeme #' . $paymentId, 'system');
    }

    public function handleWebhook(string $provider, array $payload): array
    {
        $result = $this->gateway($provider)->handleCallback($payload);
        if (!$result['success']) {
            return $result;
        }

        $conversationId = $result['conversation_id'] ?? '';
        $stmt = db()->prepare('SELECT * FROM payments WHERE provider_conversation_id = ? LIMIT 1');
        $stmt->execute([$conversationId]);
        $payment = $stmt->fetch();
        if ($payment) {
            $this->completePayment((int) $payment['id'], $result['provider_payment_id'] ?? null, json_encode($result['raw'] ?? []));
        }
        return $result;
    }

    public function recordManual(int $customerId, float $amount, string $type, ?int $packageId = null, ?int $appointmentId = null): int
    {
        $cpId = null;
        if ($packageId) {
            $package = (new Package())->find($packageId);
            $cpId = (new CustomerPackage())->create([
                'customer_id' => $customerId,
                'package_id' => $packageId,
                'total_sessions' => $package['session_count'],
                'remaining_sessions' => $package['session_count'],
                'purchase_date' => date('Y-m-d'),
                'expiry_date' => date('Y-m-d', strtotime('+' . $package['validity_days'] . ' days')),
                'payment_status' => 'manual',
                'status' => 'active',
            ]);
        }

        $paymentId = $this->paymentModel->create([
            'customer_id' => $customerId,
            'appointment_id' => $appointmentId,
            'customer_package_id' => $cpId,
            'provider' => 'manual',
            'payment_type' => $type === 'package' ? 'package' : ($type === 'deposit' ? 'deposit' : 'manual'),
            'amount' => $amount,
            'status' => 'paid',
        ]);
        $this->paymentModel->markPaid($paymentId);
        if ($cpId) {
            (new CustomerPackage())->activate($cpId, $paymentId);
        }
        return $paymentId;
    }
}
