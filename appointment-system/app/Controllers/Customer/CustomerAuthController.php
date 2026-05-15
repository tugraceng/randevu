<?php

declare(strict_types=1);

namespace App\Controllers\Customer;

use App\Models\Customer;
use App\Services\NotificationService;

class CustomerAuthController
{
    public function loginForm(): void
    {
        if (is_customer_logged_in()) {
            redirect(customer_url());
        }
        view('customer/auth/login', ['title' => 'Giriş Yap']);
    }

    public function registerForm(): void
    {
        view('customer/auth/register', ['title' => 'Kayıt Ol']);
    }

    public function login(): void
    {
        if (!verify_csrf()) {
            csrf_abort();
        }
        $customer = (new Customer())->findByEmail(trim($_POST['email'] ?? ''));
        if (!$customer || !password_verify($_POST['password'] ?? '', $customer['password'])) {
            flash('error', 'Geçersiz bilgiler.');
            redirect(customer_url('?route=login'));
        }
        login_customer($customer);
        redirect(customer_url());
    }

    public function register(): void
    {
        if (!verify_csrf()) {
            csrf_abort();
        }
        $email = trim($_POST['email'] ?? '');
        if ((new Customer())->findByEmail($email)) {
            flash('error', 'E-posta kayıtlı.');
            redirect(customer_url('?route=register'));
        }
        $token = bin2hex(random_bytes(32));
        $id = (new Customer())->create([
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
        flash('success', 'Kayıt başarılı. E-postanızı doğrulayın.');
        redirect(customer_url('?route=login'));
    }

    public function logout(): void
    {
        logout_customer();
        redirect(customer_url('?route=login'));
    }

    public function verifyPrompt(): void
    {
        require_customer();
        view('customer/auth/verify', ['title' => 'E-posta Doğrulama']);
    }

    public function verify(): void
    {
        $customer = (new Customer())->findByToken($_GET['token'] ?? '');
        if (!$customer) {
            flash('error', 'Geçersiz bağlantı.');
            redirect(customer_url('?route=login'));
        }
        (new Customer())->verifyEmail((int) $customer['id']);
        if (is_customer_logged_in()) {
            $u = customer_user();
            $u['email_verified_at'] = date('Y-m-d H:i:s');
            login_customer(array_merge($customer, $u));
        }
        flash('success', 'E-posta doğrulandı.');
        redirect(customer_url());
    }
}
