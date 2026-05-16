<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Customer;
use App\Models\PasswordReset;
use App\Services\NotificationService;

/**
 * Front-end (one-page) auth endpoints used by the auth drawer (AJAX) and
 * the public reset-password landing page.
 */
class AuthController
{
    private function isAjax(): bool
    {
        return (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest')
            || (($_SERVER['HTTP_ACCEPT'] ?? '') !== '' && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));
    }

    private function clientIp(): string
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    public function register(): void
    {
        if (!verify_csrf()) {
            csrf_abort();
        }
        if (!rate_limit_check('register')) {
            json_response(['success' => false, 'message' => 'Çok fazla deneme. Lütfen biraz bekleyin.'], 429);
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $first = trim($_POST['first_name'] ?? '');
        $last = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($first === '' || $last === '' || $email === '' || $password === '') {
            json_response(['success' => false, 'message' => 'Lütfen tüm zorunlu alanları doldurun.']);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            json_response(['success' => false, 'message' => 'Geçerli bir e-posta adresi girin.']);
        }
        if (strlen($password) < 8) {
            json_response(['success' => false, 'message' => 'Şifre en az 8 karakter olmalı.']);
        }

        $model = new Customer();
        if ($model->findByEmail($email)) {
            json_response(['success' => false, 'message' => 'Bu e-posta adresi zaten kayıtlı.']);
        }

        $token = bin2hex(random_bytes(32));
        $id = $model->create([
            'first_name' => $first,
            'last_name'  => $last,
            'phone'      => $phone,
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'verification_token' => $token,
            'sms_permission'      => (int) ($_POST['sms_permission'] ?? 0),
            'whatsapp_permission' => (int) ($_POST['whatsapp_permission'] ?? 0),
        ]);

        try {
            (new NotificationService())->sendEmailVerification($id, $token);
        } catch (\Throwable $e) {
            error_log('Verification mail error: ' . $e->getMessage());
        }

        $customer = $model->find($id);
        if ($customer) {
            login_customer($customer);
        }

        json_response([
            'success'  => true,
            'message'  => 'Kayıt başarılı. E-posta adresinizi doğrulamayı unutmayın.',
            'verified' => false,
            'user'     => ['first_name' => $first, 'email' => $email]
        ]);
    }

    public function login(): void
    {
        if (!verify_csrf()) {
            csrf_abort();
        }
        if (!rate_limit_check('login')) {
            json_response(['success' => false, 'message' => 'Çok fazla deneme. Lütfen birkaç dakika sonra tekrar deneyin.'], 429);
        }

        $email    = strtolower(trim($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $customer = (new Customer())->findByEmail($email);
        if (!$customer || !password_verify($password, $customer['password'])) {
            json_response(['success' => false, 'message' => 'E-posta veya şifre hatalı.']);
        }
        if (isset($customer['status']) && (int)$customer['status'] === 0) {
            json_response(['success' => false, 'message' => 'Hesabınız devre dışı bırakılmış.']);
        }
        if (!empty($customer['is_blacklisted'])) {
            json_response(['success' => false, 'message' => 'Bu hesap erişime kapatılmıştır.']);
        }

        login_customer($customer);
        json_response([
            'success'  => true,
            'message'  => 'Hoş geldiniz!',
            'verified' => !empty($customer['email_verified_at']),
            'user'     => ['first_name' => $customer['first_name'], 'email' => $customer['email']]
        ]);
    }

    public function forgotPassword(): void
    {
        if (!verify_csrf()) {
            csrf_abort();
        }
        if (!rate_limit_check('forgot_password')) {
            json_response(['success' => false, 'message' => 'Çok fazla deneme. Lütfen daha sonra tekrar deneyin.'], 429);
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $genericOk = [
            'success' => true,
            'message' => 'E-posta adresiniz sistemde kayıtlıysa şifre sıfırlama bağlantısı gönderildi.'
        ];

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            json_response($genericOk);
        }

        $customer = (new Customer())->findByEmail($email);
        if (!$customer) {
            json_response($genericOk);
        }

        $resetModel = new PasswordReset();
        $resetModel->deleteExpired();
        if ($resetModel->countRecentRequests((int) $customer['id'], 10) >= 3) {
            json_response(['success' => false, 'message' => 'Son 10 dakika içinde çok fazla istek. Lütfen mevcut e-postanızı kontrol edin.']);
        }

        $token = bin2hex(random_bytes(32));
        $resetModel->create((int) $customer['id'], $token, 60, $this->clientIp());

        try {
            (new NotificationService())->sendPasswordReset((int) $customer['id'], $token);
        } catch (\Throwable $e) {
            error_log('Password reset mail error: ' . $e->getMessage());
        }

        json_response($genericOk);
    }

    public function resetPasswordForm(): void
    {
        $token = (string) ($_GET['token'] ?? '');
        $valid = $token !== '' ? (new PasswordReset())->findValid($token) : null;
        view('frontend/auth/reset-password', [
            'title' => 'Şifre Sıfırla',
            'token' => $token,
            'valid' => (bool) $valid,
        ]);
    }

    public function resetPassword(): void
    {
        if (!verify_csrf()) {
            csrf_abort();
        }
        $token = (string) ($_POST['token'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $confirm  = (string) ($_POST['password_confirm'] ?? '');

        $resetModel = new PasswordReset();
        $reset = $token !== '' ? $resetModel->findValid($token) : null;
        if (!$reset) {
            flash('error', 'Bağlantı geçersiz veya süresi dolmuş.');
            redirect(base_url('?route=reset-password&token=' . urlencode($token)));
        }
        if (strlen($password) < 8) {
            flash('error', 'Şifre en az 8 karakter olmalı.');
            redirect(base_url('?route=reset-password&token=' . urlencode($token)));
        }
        if ($password !== $confirm) {
            flash('error', 'Şifreler eşleşmiyor.');
            redirect(base_url('?route=reset-password&token=' . urlencode($token)));
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        (new Customer())->updatePasswordById((int) $reset['customer_id'], $hash);
        $resetModel->markUsed((int) $reset['id']);

        flash('success', 'Şifreniz güncellendi. Yeni şifrenizle giriş yapabilirsiniz.');
        redirect(base_url('?auth=login'));
    }

    public function resendVerification(): void
    {
        if (!verify_csrf()) {
            csrf_abort();
        }
        if (!is_customer_logged_in()) {
            json_response(['success' => false, 'message' => 'Önce giriş yapın.'], 401);
        }
        $user = customer_user();
        if (!empty($user['email_verified_at'])) {
            json_response(['success' => true, 'message' => 'E-postanız zaten doğrulanmış.']);
        }
        if (!rate_limit_check('resend_verification')) {
            json_response(['success' => false, 'message' => 'Çok fazla istek. 1 dakika sonra tekrar deneyin.'], 429);
        }

        $model = new Customer();
        $token = $model->regenerateVerificationToken((int) $user['id']);
        try {
            (new NotificationService())->sendEmailVerification((int) $user['id'], $token);
        } catch (\Throwable $e) {
            error_log('Resend verification error: ' . $e->getMessage());
        }
        json_response(['success' => true, 'message' => 'Doğrulama bağlantısı yeniden gönderildi.']);
    }

    public function verify(): void
    {
        $customer = (new Customer())->findByToken($_GET['token'] ?? '');
        if (!$customer) {
            flash('error', 'Geçersiz doğrulama bağlantısı.');
            redirect(base_url('?auth=login'));
        }
        (new Customer())->verifyEmail((int) $customer['id']);
        if (is_customer_logged_in()) {
            $u = customer_user();
            $u['email_verified_at'] = date('Y-m-d H:i:s');
            login_customer(array_merge($customer, $u));
        }
        flash('success', 'E-posta adresiniz doğrulandı.');
        redirect(base_url());
    }

    public function authStatus(): void
    {
        if (!is_customer_logged_in()) {
            json_response(['logged_in' => false]);
        }
        $u = customer_user();
        json_response([
            'logged_in' => true,
            'verified'  => !empty($u['email_verified_at']),
            'user' => [
                'first_name' => $u['first_name'] ?? '',
                'last_name'  => $u['last_name'] ?? '',
                'email'      => $u['email'] ?? '',
            ]
        ]);
    }

    public function logout(): void
    {
        logout_customer();
        if ($this->isAjax()) {
            json_response(['success' => true]);
        }
        redirect(base_url());
    }
}
