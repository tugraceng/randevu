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
$router->get('/ajax/slots', [AppointmentController::class, 'slots']);
$router->post('/api/appointment', [AppointmentController::class, 'store']);

/* Auth drawer (AJAX) */
$router->post('/register', [AuthController::class, 'register']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/forgot-password', [AuthController::class, 'forgotPassword']);
$router->get('/reset-password', [AuthController::class, 'resetPasswordForm']);
$router->post('/reset-password', [AuthController::class, 'resetPassword']);
$router->post('/resend-verification', [AuthController::class, 'resendVerification']);
$router->get('/verify', [AuthController::class, 'verify']);
$router->get('/auth-status', [AuthController::class, 'authStatus']);
$router->get('/logout', [AuthController::class, 'logout']);

$router->post('/payment/package', [PaymentController::class, 'buyPackage']);

return $router;
