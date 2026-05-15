<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Admin;

class AdminAuthController
{
    public function loginForm(): void
    {
        if (is_admin_logged_in()) {
            redirect(admin_url());
        }
        view('admin/auth/login', ['title' => 'Admin Giriş']);
    }

    public function login(): void
    {
        if (!verify_csrf()) {
            csrf_abort();
        }
        if (!rate_limit_check('admin_login')) {
            flash('error', 'Çok fazla deneme.');
            redirect(admin_url('?route=login'));
        }
        $admin = (new Admin())->findByEmail(trim($_POST['email'] ?? ''));
        if (!$admin || !password_verify($_POST['password'] ?? '', $admin['password'])) {
            flash('error', 'Geçersiz giriş bilgileri.');
            redirect(admin_url('?route=login'));
        }
        login_admin($admin);
        log_system('admin_login', 'Admin giriş', 'admin', (int) $admin['id']);
        redirect(admin_url());
    }

    public function logout(): void
    {
        logout_admin();
        redirect(admin_url('?route=login'));
    }
}
