<?php
$currentRoute = trim($_GET['route'] ?? '', '/');
$breadcrumb = $breadcrumb ?? [['label' => $title ?? 'Yönetim Paneli']];
$pageTitle  = $title ?? 'Yönetim Paneli';
$adminUser  = admin_user();
$adminName  = $adminUser['name'] ?? $adminUser['username'] ?? 'Admin';
$adminInitial = strtoupper(mb_substr($adminName, 0, 1));

$sidebarLinks = [
    'overview' => [
        ['route' => '',             'icon' => 'bi-grid-1x2',         'label' => 'Dashboard'],
        ['route' => 'appointments', 'icon' => 'bi-calendar-check',   'label' => 'Randevular'],
        ['route' => 'appointments/calendar', 'icon' => 'bi-calendar3', 'label' => 'Takvim'],
    ],
    'management' => [
        ['route' => 'customers', 'icon' => 'bi-person-badge', 'label' => 'Müşteriler'],
        ['route' => 'services',  'icon' => 'bi-briefcase',    'label' => 'Hizmetler'],
        ['route' => 'staff',     'icon' => 'bi-people',       'label' => 'Personel'],
        ['route' => 'packages',  'icon' => 'bi-box',          'label' => 'Paketler'],
    ],
    'finance' => [
        ['route' => 'payments',  'icon' => 'bi-credit-card-2-front', 'label' => 'Ödemeler'],
        ['route' => 'messages',  'icon' => 'bi-chat-dots',           'label' => 'Bildirimler'],
        ['route' => 'campaigns', 'icon' => 'bi-megaphone',           'label' => 'Kampanyalar'],
    ],
    'system' => [
        ['route' => 'content',  'icon' => 'bi-file-text', 'label' => 'İçerikler'],
        ['route' => 'reports',  'icon' => 'bi-bar-chart', 'label' => 'Raporlar'],
        ['route' => 'settings', 'icon' => 'bi-gear',      'label' => 'Ayarlar'],
    ],
];

$sectionLabels = [
    'overview'   => 'Genel Bakış',
    'management' => 'Operasyon',
    'finance'    => 'Finans &amp; İletişim',
    'system'     => 'Sistem',
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($pageTitle) ?> &middot; RandevuTakip Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/admin.css') ?>" rel="stylesheet">
</head>
<body class="admin-body">
<div class="admin-overlay" id="sidebarOverlay"></div>
<div class="admin-shell">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <span class="brand-icon"><i class="bi bi-calendar2-check"></i></span>
            <div class="flex-grow-1">
                <h6>RandevuTakip</h6>
                <small>Admin</small>
            </div>
            <button class="btn btn-sm text-white d-lg-none p-1" id="sidebarClose" type="button" aria-label="Kapat">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <ul class="sidebar-nav">
            <?php foreach ($sidebarLinks as $group => $items): ?>
                <li class="sidebar-section"><?= $sectionLabels[$group] ?? '' ?></li>
                <?php foreach ($items as $link):
                    $isActive = $currentRoute === $link['route']
                        || ($link['route'] !== '' && str_starts_with($currentRoute, $link['route'] . '/'));
                ?>
                <li class="nav-item">
                    <a href="<?= admin_url('?route=' . $link['route']) ?>" class="nav-link <?= $isActive ? 'active' : '' ?>">
                        <i class="bi <?= e($link['icon']) ?>"></i>
                        <span><?= e($link['label']) ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>

        <div class="sidebar-foot">
            <a href="<?= admin_url('?route=appointments/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Yeni Randevu
            </a>
            <a href="<?= admin_url('?route=logout') ?>" class="logout-link" data-confirm="Çıkış yapılsın mı?">
                <i class="bi bi-box-arrow-right"></i> Çıkış Yap
            </a>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="sidebarOpen" type="button" aria-label="Menü">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="topbar-title">
                    <?= admin_breadcrumb($breadcrumb) ?>
                    <h4><?= e($pageTitle) ?></h4>
                </div>
            </div>
            <div class="topbar-right">
                <a href="<?= admin_url('?route=appointments/calendar') ?>" class="topbar-icon-btn d-none d-md-inline-flex" title="Takvim">
                    <i class="bi bi-calendar3"></i>
                </a>
                <a href="<?= admin_url('?route=messages') ?>" class="topbar-icon-btn d-none d-md-inline-flex" title="Bildirimler">
                    <i class="bi bi-bell"></i>
                    <span class="dot"></span>
                </a>
                <div class="dropdown">
                    <button class="topbar-user dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar"><?= e($adminInitial) ?></span>
                        <span class="user-name"><?= e($adminName) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item" href="<?= admin_url('?route=settings') ?>"><i class="bi bi-gear me-2"></i>Ayarlar</a></li>
                        <li><a class="dropdown-item" href="<?= admin_url('?route=reports') ?>"><i class="bi bi-bar-chart me-2"></i>Raporlar</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= admin_url('?route=logout') ?>" data-confirm="Çıkış yapılsın mı?"><i class="bi bi-box-arrow-right me-2"></i>Çıkış</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="admin-content">
            <div id="toast-area" class="toast-area"></div>
            <?php foreach (['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'] as $flashKey => $cls): ?>
                <?php if ($msg = flash($flashKey)): ?>
                    <div class="alert alert-<?= $cls ?> alert-dismissible fade show" role="alert">
                        <?= e($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
