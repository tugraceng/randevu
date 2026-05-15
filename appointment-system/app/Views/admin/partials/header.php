<?php
$currentRoute = trim($_GET['route'] ?? '/', '/');
$breadcrumb = $breadcrumb ?? [['label' => $title ?? 'Admin']];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title ?? 'Admin') ?> - RandevuTakip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/admin.css') ?>" rel="stylesheet">
</head>
<body class="admin-body">
<div class="admin-overlay" id="sidebarOverlay"></div>
<div class="d-flex min-vh-100">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="brand p-3 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="text-white mb-0 fw-bold">RandevuTakip</h5>
                <small class="text-white-50">Kurumsal Panel</small>
            </div>
            <button class="btn btn-sm text-white d-lg-none" id="sidebarClose" type="button"><i class="bi bi-x-lg"></i></button>
        </div>
        <nav class="nav flex-column px-2 py-3 flex-grow-1">
            <?php
            $links = [
                '' => ['Genel Bakış', 'bi-grid'],
                'appointments' => ['Randevular', 'bi-calendar-check'],
                'customers' => ['Müşteriler', 'bi-person-badge'],
                'services' => ['Hizmetler', 'bi-briefcase'],
                'staff' => ['Personel', 'bi-people'],
                'packages' => ['Paketler', 'bi-box'],
                'payments' => ['Ödemeler', 'bi-credit-card'],
                'messages' => ['Bildirimler', 'bi-chat-dots'],
                'campaigns' => ['Kampanyalar', 'bi-megaphone'],
                'content' => ['İçerikler', 'bi-file-text'],
                'reports' => ['Raporlar', 'bi-bar-chart'],
                'settings' => ['Ayarlar', 'bi-gear'],
            ];
            foreach ($links as $route => [$label, $icon]):
                $active = $currentRoute === $route || str_starts_with($currentRoute, $route . '/');
            ?>
            <a class="nav-link <?= $active ? 'active' : '' ?>" href="<?= admin_url('?route=' . $route) ?>">
                <i class="bi <?= $icon ?> me-2"></i><?= e($label) ?>
            </a>
            <?php endforeach; ?>
        </nav>
        <div class="p-3 border-top border-white border-opacity-10">
            <a href="<?= admin_url('?route=appointments/create') ?>" class="btn btn-light w-100 mb-2"><i class="bi bi-plus-lg"></i> Yeni Randevu</a>
            <a href="<?= admin_url('?route=logout') ?>" class="text-white-50 small text-decoration-none"><i class="bi bi-box-arrow-right"></i> Çıkış</a>
        </div>
    </aside>
    <main class="admin-main flex-grow-1">
        <header class="admin-topbar px-3 px-lg-4 py-3 bg-white border-bottom d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary d-lg-none" id="sidebarOpen" type="button"><i class="bi bi-list"></i></button>
                <div>
                    <?= admin_breadcrumb($breadcrumb) ?>
                    <h4 class="mb-0 fw-semibold mt-1"><?= e($title ?? '') ?></h4>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark d-none d-md-inline"><i class="bi bi-bell"></i></span>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        <?= e(admin_user()['name'] ?? 'Admin') ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= admin_url('?route=settings') ?>">Ayarlar</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= admin_url('?route=logout') ?>">Çıkış</a></li>
                    </ul>
                </div>
            </div>
        </header>
        <div class="admin-content px-3 px-lg-4 py-3">
            <div id="toast-area" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1090"></div>
            <?php if ($m = flash('success')): ?><div class="alert alert-success alert-dismissible fade show"><?= e($m) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
            <?php if ($m = flash('error')): ?><div class="alert alert-danger alert-dismissible fade show"><?= e($m) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
