<!DOCTYPE html>
<html lang="tr"><head><meta charset="UTF-8"><title>Giriş</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= base_url('assets/css/customer.css') ?>" rel="stylesheet"></head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
<div class="card p-4 shadow" style="width:400px;border-radius:16px">
<h4 class="text-center fw-bold">Müşteri Girişi</h4>
<?php if ($m = flash('error')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
<?php if ($m = flash('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
<form method="post" action="<?= customer_url('?route=login') ?>"><?= csrf_field() ?>
<input class="form-control mb-2" type="email" name="email" placeholder="E-posta" required>
<input class="form-control mb-3" type="password" name="password" placeholder="Şifre" required>
<button class="btn btn-primary w-100">Giriş Yap</button>
</form>
<p class="text-center mt-3 small"><a href="<?= customer_url('?route=register') ?>">Kayıt ol</a> · <a href="<?= base_url() ?>">Ana sayfa</a></p>
</div></body></html>
