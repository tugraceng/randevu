<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MessageLog;
use App\Models\Setting;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailService
{
    private $settings;
    private $log;

    public function __construct()
    {
        $this->settings = new Setting();
        $this->log = new MessageLog();
    }

    public function send(string $to, string $subject, string $body, ?int $customerId = null, ?int $appointmentId = null, ?string $templateKey = null): bool
    {
        $logId = $this->log->create([
            'customer_id' => $customerId,
            'appointment_id' => $appointmentId,
            'channel' => 'email',
            'recipient' => $to,
            'template_key' => $templateKey,
            'message' => $body,
            'provider' => 'phpmailer',
            'status' => 'pending',
        ]);

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $this->settings->get('mail_host', config('mail')['host']);
            $mail->SMTPAuth = true;
            $mail->Username = $this->settings->get('mail_username', config('mail')['username']);
            $mail->Password = $this->settings->get('mail_password', config('mail')['password']);
            $mail->SMTPSecure = $this->settings->get('mail_encryption', config('mail')['encryption']);
            $mail->Port = (int) $this->settings->get('mail_port', (string) config('mail')['port']);
            $mail->CharSet = 'UTF-8';

            $fromEmail = $this->settings->get('mail_from_email', config('mail')['from_email']);
            $fromName = $this->settings->get('mail_from_name', config('mail')['from_name']);
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = nl2br(e($body));
            $mail->AltBody = strip_tags($body);
            $mail->send();

            $this->log->updateStatus($logId, 'sent');
            return true;
        } catch (Exception $e) {
            $this->log->updateStatus($logId, 'failed', $e->getMessage());
            return false;
        }
    }
}
