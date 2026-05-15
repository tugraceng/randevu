<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Giriş - RandevuTakip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/admin.css') ?>" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
<div class="card shadow p-4" style="width:400px;border-radius:16px">
    <h4 class="fw-bold text-center mb-1">RandevuTakip</h4>
    <p class="text-muted text-center small mb-4">Kurumsal Panel Girişi</p>
    <?php if ($m = flash('error')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
    <form method="post" action="<?= admin_url('?route=login') ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label">E-posta</label>
            <input type="email" name="email" class="form-control" required value="admin@randevu.local">
        </div>
        <div class="mb-3">
            <label class="form-label">Şifre</label>
            <input type="password" name="password" class="form-control" required placeholder="password">
            <small class="text-muted">Varsayılan: password</small>
        </div>
        <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
    </form>
</div>
</body>
</html>
