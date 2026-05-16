<?php
$route = trim($_GET['route'] ?? '', '/');
$user  = customer_user();
$initial = strtoupper(mb_substr($user['first_name'] ?? '?', 0, 1));
$links = [
    ''                    => ['Panel',         'bi-house'],
    'appointments'        => ['Randevularım',  'bi-calendar-check'],
    'appointments/create' => ['Yeni Randevu',  'bi-calendar-plus'],
    'packages'            => ['Paketlerim',    'bi-box'],
    'payments'            => ['Ödemelerim',    'bi-credit-card'],
    'profile'             => ['Profilim',      'bi-person'],
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title ?? 'Panelim') ?> · RandevuTakip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/tokens.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/customer.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/polish.css') ?>" rel="stylesheet">
</head>
<body class="ps-context">
<div class="customer-layout">
    <aside class="customer-sidebar">
        <div class="customer-brand">
            <span class="brand-icon"><i class="bi bi-calendar2-check"></i></span>
            <div>
                <h6>RandevuTakip</h6>
                <small>Müşteri Paneli</small>
            </div>
        </div>
        <nav class="nav flex-column">
            <?php foreach ($links as $r => [$label, $icon]): ?>
            <a class="nav-link <?= $route === $r ? 'active' : '' ?>" href="<?= customer_url('?route=' . $r) ?>">
                <i class="bi <?= e($icon) ?>"></i> <span><?= e($label) ?></span>
            </a>
            <?php endforeach; ?>
        </nav>
        <div class="foot d-none d-lg-block mt-3">
            <a href="<?= customer_url('?route=logout') ?>" class="btn btn-outline-secondary btn-sm w-100">
                <i class="bi bi-box-arrow-right me-1"></i> Çıkış
            </a>
        </div>
    </aside>

    <main class="customer-main">
        <header class="customer-topbar">
            <h4><?= e($title ?? 'Panel') ?></h4>
            <div class="user-chip">
                <span class="avatar"><?= e($initial) ?></span>
                <span><?= e(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></span>
                <a href="<?= customer_url('?route=logout') ?>" class="text-muted ms-1" title="Çıkış"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </header>

        <div class="customer-content">
            <?php foreach (['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'] as $key => $cls): ?>
                <?php if ($msg = flash($key)): ?>
                    <div class="alert alert-<?= $cls ?> alert-dismissible fade show" role="alert">
                        <?= e($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
