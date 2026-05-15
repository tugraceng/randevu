<?php

declare(strict_types=1);

use App\Controllers\Customer\CustomerAppointmentController;
use App\Controllers\Customer\CustomerAuthController;
use App\Controllers\Customer\CustomerDashboardController;
use App\Controllers\Customer\CustomerPackageController;
use App\Controllers\Customer\CustomerPaymentController;
use App\Controllers\Customer\CustomerProfileController;
use App\Core\Router;

$router = new Router();

$router->get('/login', [CustomerAuthController::class, 'loginForm']);
$router->post('/login', [CustomerAuthController::class, 'login']);
$router->get('/register', [CustomerAuthController::class, 'registerForm']);
$router->post('/register', [CustomerAuthController::class, 'register']);
$router->get('/logout', [CustomerAuthController::class, 'logout']);
$router->get('/verify-email', [CustomerAuthController::class, 'verifyPrompt']);
$router->get('/verify', [CustomerAuthController::class, 'verify']);

$router->get('/', [CustomerDashboardController::class, 'index']);
$router->get('/appointments', [CustomerAppointmentController::class, 'index']);
$router->get('/appointments/create', [CustomerAppointmentController::class, 'create']);
$router->post('/appointments/store', [CustomerAppointmentController::class, 'store']);
$router->get('/packages', [CustomerPackageController::class, 'index']);
$router->post('/packages/buy', [CustomerPackageController::class, 'buy']);
$router->get('/payments', [CustomerPaymentController::class, 'index']);
$router->get('/profile', [CustomerProfileController::class, 'index']);
$router->post('/profile', [CustomerProfileController::class, 'update']);
$router->post('/profile/password', [CustomerProfileController::class, 'password']);

return $router;
