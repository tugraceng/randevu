<?php

declare(strict_types=1);

use App\Controllers\Admin\AdminAppointmentController;
use App\Controllers\Admin\AdminAuthController;
use App\Controllers\Admin\AdminCampaignController;
use App\Controllers\Admin\AdminContentController;
use App\Controllers\Admin\AdminCustomerController;
use App\Controllers\Admin\AdminDashboardController;
use App\Controllers\Admin\AdminMessageController;
use App\Controllers\Admin\AdminPackageController;
use App\Controllers\Admin\AdminPaymentController;
use App\Controllers\Admin\AdminReportController;
use App\Controllers\Admin\AdminServiceController;
use App\Controllers\Admin\AdminSettingsController;
use App\Controllers\Admin\AdminStaffController;
use App\Core\Router;

$router = new Router();

$router->get('/login', [AdminAuthController::class, 'loginForm']);
$router->post('/login', [AdminAuthController::class, 'login']);
$router->get('/logout', [AdminAuthController::class, 'logout']);

$router->get('/', [AdminDashboardController::class, 'index']);
$router->get('/services', [AdminServiceController::class, 'index']);
$router->post('/services/save', [AdminServiceController::class, 'save']);
$router->get('/staff', [AdminStaffController::class, 'index']);
$router->post('/staff/save', [AdminStaffController::class, 'save']);
$router->get('/customers', [AdminCustomerController::class, 'index']);
$router->post('/customers/package', [AdminCustomerController::class, 'assignPackage']);
$router->get('/appointments', [AdminAppointmentController::class, 'index']);
$router->post('/appointments/save', [AdminAppointmentController::class, 'save']);
$router->post('/appointments/status', [AdminAppointmentController::class, 'updateStatus']);
$router->get('/packages', [AdminPackageController::class, 'index']);
$router->post('/packages/save', [AdminPackageController::class, 'save']);
$router->get('/campaigns', [AdminCampaignController::class, 'index']);
$router->post('/campaigns/send', [AdminCampaignController::class, 'send']);
$router->get('/messages', [AdminMessageController::class, 'index']);
$router->post('/messages/save', [AdminMessageController::class, 'save']);
$router->get('/payments', [AdminPaymentController::class, 'index']);
$router->post('/payments/manual', [AdminPaymentController::class, 'manual']);
$router->get('/content', [AdminContentController::class, 'index']);
$router->post('/content/save', [AdminContentController::class, 'save']);
$router->get('/settings', [AdminSettingsController::class, 'index']);
$router->post('/settings/save', [AdminSettingsController::class, 'save']);
$router->get('/reports', [AdminReportController::class, 'index']);

return $router;
