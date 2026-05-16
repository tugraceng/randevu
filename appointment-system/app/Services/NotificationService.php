<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Appointment;
use App\Models\Campaign;
use App\Models\Customer;
use App\Models\CustomerPackage;
use App\Models\MessageTemplate;
use App\Models\Payment;
use App\Models\Setting;

class NotificationService
{
    private $settings;
    private $templates;
    private $mail;
    private $sms;
    private $whatsapp;

    public function __construct()
    {
        $this->settings = new Setting();
        $this->templates = new MessageTemplate();
        $this->mail = new MailService();
        $this->sms = new NetgsmSmsService();
        $this->whatsapp = new WhatsAppCloudApiService();
    }

    public function sendAppointmentCreated(int $appointmentId): void
    {
        $this->dispatchAppointment($appointmentId, 'appointment_created');
    }

    public function sendAppointmentApproved(int $appointmentId): void
    {
        $this->dispatchAppointment($appointmentId, 'appointment_approved');
    }

    public function sendAppointmentCancelled(int $appointmentId): void
    {
        $this->dispatchAppointment($appointmentId, 'appointment_cancelled');
    }

    public function sendAppointmentReminder(int $appointmentId): void
    {
        $this->dispatchAppointment($appointmentId, 'appointment_reminder');
    }

    public function sendPaymentSuccess(int $paymentId): void
    {
        $payment = (new Payment())->find($paymentId);
        if (!$payment) {
            return;
        }
        $customer = (new Customer())->find((int) $payment['customer_id']);
        if (!$customer) {
            return;
        }
        $vars = $this->baseVars($customer);
        $vars['amount'] = format_money((float) $payment['amount']);
        $this->sendChannel('email', 'payment_success', $customer, $vars);
        $this->sendChannel('sms', 'payment_success', $customer, $vars);
    }

    public function sendPackageRemaining(int $customerPackageId): void
    {
        $cp = (new CustomerPackage())->find($customerPackageId);
        if (!$cp) {
            return;
        }
        $customer = (new Customer())->find((int) $cp['customer_id']);
        $vars = $this->baseVars($customer);
        $vars['package'] = $cp['package_name'];
        $vars['remaining_sessions'] = (string) $cp['remaining_sessions'];
        $this->sendChannel('sms', 'package_remaining', $customer, $vars);
        $this->sendChannel('whatsapp', 'package_remaining', $customer, $vars);
    }

    public function sendCampaign(array $customerIds, int $campaignId): void
    {
        $campaign = (new Campaign())->find($campaignId);
        if (!$campaign) {
            return;
        }
        foreach ($customerIds as $customerId) {
            $customer = (new Customer())->find((int) $customerId);
            if (!$customer || !(int) $customer['marketing_permission']) {
                continue;
            }
            $vars = $this->baseVars($customer);
            $vars['campaign'] = $campaign['title'] . ' - ' . ($campaign['description'] ?? '');
            $this->sendChannel('sms', 'campaign_message', $customer, $vars);
            $this->sendChannel('whatsapp', 'campaign_message', $customer, $vars);
            $this->sendChannel('email', 'campaign_message', $customer, $vars);
        }
    }

    public function sendEmailVerification(int $customerId, string $token): void
    {
        $customer = (new Customer())->find($customerId);
        if (!$customer) {
            return;
        }
        $vars = $this->baseVars($customer);
        $vars['verification_link'] = customer_url('?route=verify&token=' . urlencode($token));
        $this->sendChannel('email', 'verify_email', $customer, $vars);
    }

    public function sendPasswordReset(int $customerId, string $token): void
    {
        $customer = (new Customer())->find($customerId);
        if (!$customer) {
            return;
        }
        $vars = $this->baseVars($customer);
        $vars['reset_link'] = base_url('?route=reset-password&token=' . urlencode($token));
        $this->sendChannel('email', 'password_reset', $customer, $vars);
    }

    private function dispatchAppointment(int $appointmentId, string $templateKey): void
    {
        $appointment = (new Appointment())->find($appointmentId);
        if (!$appointment) {
            return;
        }
        $customer = (new Customer())->find((int) $appointment['customer_id']);
        if (!$customer) {
            return;
        }
        $vars = $this->appointmentVars($appointment, $customer);
        $this->sendChannel('email', $templateKey, $customer, $vars, $appointmentId);
        if ((int) $customer['sms_permission']) {
            $this->sendChannel('sms', $templateKey, $customer, $vars, $appointmentId);
        }
        if ((int) $customer['whatsapp_permission']) {
            $this->sendChannel('whatsapp', $templateKey, $customer, $vars, $appointmentId);
        }
    }

    private function sendChannel(string $channel, string $key, array $customer, array $vars, ?int $appointmentId = null): void
    {
        $template = $this->templates->findByKey($channel, $key);
        if (!$template) {
            return;
        }
        $body = replace_template_vars($template['body'], $vars);
        $phone = $customer['phone'] ?? '';
        $email = $customer['email'];

        if ($channel === 'email') {
            $this->mail->send($email, $template['subject'] ?? $template['title'], $body, (int) $customer['id'], $appointmentId, $key);
        } elseif ($channel === 'sms' && $phone) {
            $this->sms->send($phone, $body, (int) $customer['id'], $appointmentId, $key);
        } elseif ($channel === 'whatsapp' && $phone) {
            if ($template['provider_template_name']) {
                $this->whatsapp->sendTemplate(
                    $phone,
                    $template['provider_template_name'],
                    $template['language_code'] ?? 'tr',
                    array_values($vars),
                    (int) $customer['id'],
                    $appointmentId,
                    $key
                );
            } else {
                $this->whatsapp->sendText($phone, $body, (int) $customer['id'], $appointmentId);
            }
        }
    }

    private function baseVars(array $customer): array
    {
        return [
            'name' => trim($customer['first_name'] . ' ' . $customer['last_name']),
            'business_name' => $this->settings->get('business_name', 'RandevuTakip'),
        ];
    }

    private function appointmentVars(array $appointment, array $customer): array
    {
        $vars = $this->baseVars($customer);
        $vars['date'] = format_date($appointment['appointment_date']);
        $vars['time'] = format_time($appointment['start_time']);
        $vars['service'] = $appointment['service_name'] ?? '';
        $vars['staff'] = $appointment['staff_name'] ?? '';
        return $vars;
    }
}
