<?php

declare(strict_types=1);

use App\Controllers\AppointmentController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\PaymentController;
use App\Core\Router;

$router = new Router();

$router->get('/', [HomeController::class, 'index']);
$router->get('/api/slots', [AppointmentController::class, 'slots']);
$router->post('/api/appointment', [AppointmentController::class, 'store']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/verify', [AuthController::class, 'verify']);
$router->post('/payment/package', [PaymentController::class, 'buyPackage']);

return $router;
