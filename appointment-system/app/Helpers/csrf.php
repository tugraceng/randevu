<?php

declare(strict_types=1);

function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(?string $token = null): bool
{
    $token = $token ?? ($_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    return is_string($token) && hash_equals(csrf_token(), $token);
}

function csrf_abort(): void
{
    http_response_code(419);
    if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
        json_response(['success' => false, 'message' => 'CSRF doğrulaması başarısız.'], 419);
    }
    flash('error', 'Güvenlik doğrulaması başarısız. Lütfen tekrar deneyin.');
    redirect($_SERVER['HTTP_REFERER'] ?? base_url());
}
