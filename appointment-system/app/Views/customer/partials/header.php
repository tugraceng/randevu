<?php $route = $_GET['route'] ?? ''; $user = customer_user(); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Panel') ?> - RandevuTakip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/customer.css') ?>" rel="stylesheet">
</head>
<body>
<div class="customer-layout d-flex">
<aside class="customer-sidebar p-3">
    <h5 class="fw-bold text-primary">RandevuTakip</h5>
    <small class="text-muted d-block mb-3">Müşteri Paneli</small>
    <nav class="nav flex-column">
        <?php $links = [''=>'Panel','appointments'=>'Randevularım','appointments/create'=>'Yeni Randevu','packages'=>'Paketlerim','payments'=>'Ödemeler','profile'=>'Profilim'];
        foreach ($links as $r => $label): ?>
        <a class="nav-link <?= $route===$r?'active':'' ?>" href="<?= customer_url('?route='.$r) ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
    </nav>
    <a href="<?= customer_url('?route=logout') ?>" class="btn btn-outline-secondary btn-sm mt-4 w-100">Çıkış</a>
</aside>
<main class="flex-grow-1 p-4">
    <h4 class="mb-3"><?= e($title ?? '') ?></h4>
    <?php if ($m = flash('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <?php if ($m = flash('error')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
    <?php if ($m = flash('warning')): ?><div class="alert alert-warning"><?= e($m) ?></div><?php endif; ?>
