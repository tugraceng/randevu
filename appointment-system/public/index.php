<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

/*
 * Public site routing supports BOTH:
 *   - clean URLs   (/login, /register, /reset-password, ...)
 *   - query routes (?route=login, ?route=register, ...)
 * The query-route form keeps the experience consistent with the
 * /admin and /customer entry points, and lets emailed links work
 * even when rewrites are not enabled.
 */
if (isset($_GET['route']) && $_GET['route'] !== '') {
    $uri = '/' . trim((string) $_GET['route'], '/');
} else {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $base = dirname($_SERVER['SCRIPT_NAME']);
    if ($base !== '/' && str_starts_with($uri, $base)) {
        $uri = substr($uri, strlen($base)) ?: '/';
    }
    $uri = strtok($uri, '?') ?: '/';
}

$router = require APP_PATH . '/routes/web.php';
$router->dispatch($_SERVER['REQUEST_METHOD'], $uri);
