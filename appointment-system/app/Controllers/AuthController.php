<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Customer;
use App\Services\NotificationService;

class AuthController
{
    public function register(): void
    {
        if (!verify_csrf()) {
            csrf_abort();
        }
        if (!rate_limit_check('register')) {
            json_response(['success' => false, 'message' => 'Çok fazla deneme.'], 429);
        }

        $email = trim($_POST['email'] ?? '');
        $model = new Customer();
        if ($model->findByEmail($email)) {
            json_response(['success' => false, 'message' => 'Bu e-posta kayıtlı.']);
        }

        $token = bin2hex(random_bytes(32));
        $id = $model->create([
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => $email,
            'password' => password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT),
            'verification_token' => $token,
            'sms_permission' => (int) ($_POST['sms_permission'] ?? 0),
            'whatsapp_permission' => (int) ($_POST['whatsapp_permission'] ?? 0),
        ]);

        (new NotificationService())->sendEmailVerification($id, $token);
        json_response(['success' => true, 'message' => 'Kayıt başarılı. E-postanızı doğrulayın.']);
    }

    public function login(): void
    {
        if (!verify_csrf()) {
            csrf_abort();
        }
        $customer = (new Customer())->findByEmail(trim($_POST['email'] ?? ''));
        if (!$customer || !password_verify($_POST['password'] ?? '', $customer['password'])) {
            json_response(['success' => false, 'message' => 'Geçersiz bilgiler.']);
        }
        login_customer($customer);
        json_response(['success' => true, 'redirect' => customer_url()]);
    }

    public function verify(): void
    {
        $customer = (new Customer())->findByToken($_GET['token'] ?? '');
        if (!$customer) {
            flash('error', 'Geçersiz doğrulama bağlantısı.');
            redirect(customer_url('?route=login'));
        }
        (new Customer())->verifyEmail((int) $customer['id']);
        flash('success', 'E-posta doğrulandı. Giriş yapabilirsiniz.');
        redirect(customer_url('?route=login'));
    }
}
