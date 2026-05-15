<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

$route = $_GET['route'] ?? '/';
$route = '/' . trim($route, '/');
if ($route === '/') {
    $route = '/';
}

$_SERVER['REQUEST_URI'] = $route;
$router = require APP_PATH . '/routes/admin.php';
$router->dispatch($_SERVER['REQUEST_METHOD'], $route);
