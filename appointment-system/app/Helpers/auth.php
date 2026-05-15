<?php

declare(strict_types=1);

function admin_session_key(): string
{
    return config('app')['admin_session_key'] ?? 'admin_user';
}

function customer_session_key(): string
{
    return config('app')['customer_session_key'] ?? 'customer_user';
}

function admin_user(): ?array
{
    return $_SESSION[admin_session_key()] ?? null;
}

function customer_user(): ?array
{
    return $_SESSION[customer_session_key()] ?? null;
}

function is_admin_logged_in(): bool
{
    return admin_user() !== null;
}

function is_customer_logged_in(): bool
{
    return customer_user() !== null;
}

function require_admin(): void
{
    if (!is_admin_logged_in()) {
        redirect(admin_url('?route=login'));
    }
}

function require_customer(): void
{
    if (!is_customer_logged_in()) {
        redirect(customer_url('?route=login'));
    }
}

function require_verified_customer(): void
{
    require_customer();
    $user = customer_user();
    if (empty($user['email_verified_at'])) {
        flash('warning', 'Randevu alabilmek için e-posta adresinizi doğrulamanız gerekir.');
        redirect(customer_url('?route=verify-email'));
    }
}

function login_admin(array $admin): void
{
    session_regenerate_id(true);
    $_SESSION[admin_session_key()] = [
        'id' => (int) $admin['id'],
        'name' => $admin['name'],
        'email' => $admin['email'],
        'role' => $admin['role'],
    ];
}

function login_customer(array $customer): void
{
    session_regenerate_id(true);
    $_SESSION[customer_session_key()] = [
        'id' => (int) $customer['id'],
        'first_name' => $customer['first_name'],
        'last_name' => $customer['last_name'],
        'email' => $customer['email'],
        'phone' => $customer['phone'] ?? '',
        'email_verified_at' => $customer['email_verified_at'] ?? null,
        'sms_permission' => (int) ($customer['sms_permission'] ?? 0),
        'whatsapp_permission' => (int) ($customer['whatsapp_permission'] ?? 0),
        'marketing_permission' => (int) ($customer['marketing_permission'] ?? 0),
    ];
}

function logout_admin(): void
{
    unset($_SESSION[admin_session_key()]);
}

function logout_customer(): void
{
    unset($_SESSION[customer_session_key()]);
}

function rate_limit_check(string $key): bool
{
    $cfg = config('app');
    $max = (int) ($cfg['rate_limit_attempts'] ?? 10);
    $window = (int) ($cfg['rate_limit_window'] ?? 300);
    $now = time();
    $bucket = $_SESSION['_rate'][$key] ?? ['count' => 0, 'start' => $now];
    if ($now - $bucket['start'] > $window) {
        $bucket = ['count' => 0, 'start' => $now];
    }
    $bucket['count']++;
    $_SESSION['_rate'][$key] = $bucket;
    return $bucket['count'] <= $max;
}
