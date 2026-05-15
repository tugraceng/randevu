<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$base = dirname($_SERVER['SCRIPT_NAME']);
if ($base !== '/' && str_starts_with($uri, $base)) {
    $uri = substr($uri, strlen($base)) ?: '/';
}
$uri = strtok($uri, '?') ?: '/';

$router = require APP_PATH . '/routes/web.php';
$router->dispatch($_SERVER['REQUEST_METHOD'], $uri);
