<?php

declare(strict_types=1);

function config(string $file): array
{
    static $cache = [];
    if (!isset($cache[$file])) {
        $path = CONFIG_PATH . '/' . $file . '.php';
        $cache[$file] = is_file($path) ? require $path : [];
    }
    return $cache[$file];
}

function base_url(string $path = ''): string
{
    $base = rtrim(config('app')['url'] ?? '', '/');
    return $path ? $base . '/' . ltrim($path, '/') : $base;
}

function admin_url(string $path = ''): string
{
    $base = rtrim(config('app')['admin_url'] ?? '', '/');
    return $path ? $base . '/' . ltrim($path, '/') : $base;
}

function customer_url(string $path = ''): string
{
    $base = rtrim(config('app')['customer_url'] ?? '', '/');
    return $path ? $base . '/' . ltrim($path, '/') : $base;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $url, int $code = 302): void
{
    header('Location: ' . $url, true, $code);
    exit;
}

function view(string $name, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $file = APP_PATH . '/Views/' . str_replace('.', '/', $name) . '.php';
    if (!is_file($file)) {
        http_response_code(500);
        echo 'View not found: ' . e($name);
        return;
    }
    require $file;
}

function json_response(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function old(string $key, $default = '')
{
    return $_SESSION['_old'][$key] ?? $default;
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }
    $msg = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $msg;
}

function set_old(array $input): void
{
    $_SESSION['_old'] = $input;
}

function slugify(string $text): string
{
    $text = mb_strtolower($text, 'UTF-8');
    $map = ['ş' => 's', 'ğ' => 'g', 'ü' => 'u', 'ö' => 'o', 'ç' => 'c', 'ı' => 'i'];
    $text = strtr($text, $map);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-') ?: 'item';
}

function format_money(float $amount): string
{
    return number_format($amount, 2, ',', '.') . ' ₺';
}

function format_date(?string $date): string
{
    if (!$date) {
        return '';
    }
    return date('d.m.Y', strtotime($date));
}

function format_time(?string $time): string
{
    if (!$time) {
        return '';
    }
    return date('H:i', strtotime($time));
}

function log_system(string $action, string $description = '', string $userType = 'system', ?int $userId = null): void
{
    try {
        $pdo = db();
        $stmt = $pdo->prepare(
            'INSERT INTO system_logs (user_type, user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $userType,
            $userId,
            $action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);
    } catch (Throwable $e) {
        // silent
    }
}

function replace_template_vars(string $body, array $vars): string
{
    foreach ($vars as $key => $value) {
        $body = str_replace('{' . $key . '}', (string) $value, $body);
    }
    return $body;
}
