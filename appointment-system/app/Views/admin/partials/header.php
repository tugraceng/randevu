<?php $currentRoute = $_GET['route'] ?? '/'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin') ?> - RandevuTakip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/admin.css') ?>" rel="stylesheet">
</head>
<body class="admin-body">
<div class="d-flex min-vh-100">
    <aside class="admin-sidebar d-flex flex-column">
        <div class="brand p-3 border-bottom border-white border-opacity-10">
            <h5 class="text-white mb-0 fw-bold">RandevuTakip</h5>
            <small class="text-white-50">Kurumsal Panel</small>
        </div>
        <nav class="nav flex-column px-2 py-3 flex-grow-1">
            <?php
            $links = [
                '' => ['Genel Bakış', 'bi-grid'],
                'services' => ['Hizmet Kataloğu', 'bi-briefcase'],
                'staff' => ['Ekip Yönetimi', 'bi-people'],
                'customers' => ['Müşteriler', 'bi-person-badge'],
                'appointments' => ['Randevular', 'bi-calendar-check'],
                'packages' => ['Paketler', 'bi-box'],
                'campaigns' => ['Kampanyalar', 'bi-megaphone'],
                'messages' => ['Bildirimler', 'bi-chat-dots'],
                'payments' => ['Ödemeler', 'bi-credit-card'],
                'content' => ['İçerikler', 'bi-file-text'],
                'reports' => ['Raporlar', 'bi-bar-chart'],
                'settings' => ['Ayarlar', 'bi-gear'],
            ];
            foreach ($links as $route => [$label, $icon]):
                $active = $currentRoute === $route || ($route === '' && in_array($currentRoute, ['', '/'], true));
            ?>
            <a class="nav-link <?= $active ? 'active' : '' ?>" href="<?= admin_url('?route=' . $route) ?>">
                <i class="bi <?= $icon ?> me-2"></i><?= e($label) ?>
            </a>
            <?php endforeach; ?>
        </nav>
        <div class="p-3">
            <a href="<?= admin_url('?route=appointments') ?>" class="btn btn-primary w-100 mb-2"><i class="bi bi-plus-lg"></i> Yeni Randevu</a>
            <a href="<?= admin_url('?route=logout') ?>" class="text-white-50 small text-decoration-none"><i class="bi bi-box-arrow-right"></i> Çıkış</a>
        </div>
    </aside>
    <main class="admin-main flex-grow-1">
        <header class="admin-header d-flex justify-content-between align-items-center px-4 py-3 bg-white border-bottom">
            <h4 class="mb-0 fw-semibold"><?= e($title ?? '') ?></h4>
            <span class="badge bg-light text-dark"><?= e(admin_user()['name'] ?? '') ?></span>
        </header>
        <div class="px-4 py-3">
            <?php if ($m = flash('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
            <?php if ($m = flash('error')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
