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
$router->get('/api/charts', [AdminDashboardController::class, 'chartData']);

$router->get('/services', [AdminServiceController::class, 'index']);
$router->post('/services/save', [AdminServiceController::class, 'save']);
$router->get('/staff', [AdminStaffController::class, 'index']);
$router->post('/staff/save', [AdminStaffController::class, 'save']);

$router->get('/customers', [AdminCustomerController::class, 'index']);
$router->get('/customers/show', [AdminCustomerController::class, 'show']);
$router->post('/customers/create', [AdminCustomerController::class, 'create']);
$router->post('/customers/save', [AdminCustomerController::class, 'save']);
$router->post('/customers/package', [AdminCustomerController::class, 'assignPackage']);
$router->post('/customers/note', [AdminCustomerController::class, 'addNote']);
$router->post('/customers/blacklist', [AdminCustomerController::class, 'blacklist']);

$router->get('/appointments', [AdminAppointmentController::class, 'index']);
$router->get('/appointments/create', [AdminAppointmentController::class, 'create']);
$router->get('/appointments/show', [AdminAppointmentController::class, 'show']);
$router->get('/appointments/edit', [AdminAppointmentController::class, 'edit']);
$router->get('/appointments/calendar', [AdminAppointmentController::class, 'calendar']);
$router->get('/appointments/customer-packages', [AdminAppointmentController::class, 'customerPackages']);
$router->post('/appointments/save', [AdminAppointmentController::class, 'save']);
$router->post('/appointments/update', [AdminAppointmentController::class, 'update']);
$router->post('/appointments/status', [AdminAppointmentController::class, 'updateStatus']);
$router->post('/appointments/note', [AdminAppointmentController::class, 'updateNote']);
$router->post('/appointments/paid', [AdminAppointmentController::class, 'markPaid']);
$router->post('/appointments/message', [AdminAppointmentController::class, 'sendMessage']);

$router->get('/packages', [AdminPackageController::class, 'index']);
$router->get('/packages/show', [AdminPackageController::class, 'show']);
$router->post('/packages/save', [AdminPackageController::class, 'save']);
$router->post('/packages/session', [AdminPackageController::class, 'adjustSession']);

$router->get('/campaigns', [AdminCampaignController::class, 'index']);
$router->post('/campaigns/send', [AdminCampaignController::class, 'send']);
$router->get('/messages', [AdminMessageController::class, 'index']);
$router->post('/messages/save', [AdminMessageController::class, 'save']);
$router->get('/payments', [AdminPaymentController::class, 'index']);
$router->get('/payments/show', [AdminPaymentController::class, 'show']);
$router->post('/payments/manual', [AdminPaymentController::class, 'manual']);
$router->post('/payments/status', [AdminPaymentController::class, 'updateStatus']);
$router->get('/content', [AdminContentController::class, 'index']);
$router->post('/content/save', [AdminContentController::class, 'save']);
$router->post('/content/faq', [AdminContentController::class, 'saveFaq']);
$router->post('/content/gallery', [AdminContentController::class, 'saveGallery']);
$router->post('/content/review', [AdminContentController::class, 'saveReview']);
$router->get('/settings', [AdminSettingsController::class, 'index']);
$router->post('/settings/save', [AdminSettingsController::class, 'save']);
$router->get('/reports', [AdminReportController::class, 'index']);
$router->get('/reports/export', [AdminReportController::class, 'export']);

return $router;
