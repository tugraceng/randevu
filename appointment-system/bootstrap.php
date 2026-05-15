<?php

declare(strict_types=1);

define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('PUBLIC_PATH', BASE_PATH . '/public');

$vendorAutoload = BASE_PATH . '/vendor/autoload.php';
if (is_file($vendorAutoload)) {
    require_once $vendorAutoload;
}

require_once APP_PATH . '/Helpers/polyfills.php';
require_once APP_PATH . '/Helpers/functions.php';
require_once APP_PATH . '/Helpers/database.php';
require_once APP_PATH . '/Helpers/csrf.php';
require_once APP_PATH . '/Helpers/auth.php';
require_once APP_PATH . '/Helpers/response.php';
require_once APP_PATH . '/Helpers/upload.php';

$config = require CONFIG_PATH . '/app.php';
date_default_timezone_set($config['timezone'] ?? 'Europe/Istanbul');

if (session_status() === PHP_SESSION_NONE) {
    session_name($config['session_name'] ?? 'RANDEVU_SESSION');
    if (PHP_VERSION_ID >= 70300) {
        session_start([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
            'use_strict_mode' => true,
        ]);
    } else {
        ini_set('session.cookie_httponly', '1');
        session_start();
    }
}

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = APP_PATH . '/' . $relative . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});
