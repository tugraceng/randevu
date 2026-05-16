<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Giriş · RandevuTakip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/admin.css') ?>" rel="stylesheet">
</head>
<body class="admin-body">
<div class="auth-shell">
    <div class="auth-card">
        <div class="text-center mb-4">
            <span class="brand-icon mb-3 d-inline-flex" style="width:56px;height:56px;font-size:1.6rem;"><i class="bi bi-calendar2-check"></i></span>
            <h4 class="fw-bold mb-1">RandevuTakip</h4>
            <p class="text-muted small mb-0">Yönetim paneline hoş geldiniz</p>
        </div>
        <?php if ($m = flash('error')): ?>
            <div class="alert alert-danger small"><i class="bi bi-exclamation-circle me-1"></i> <?= e($m) ?></div>
        <?php endif; ?>
        <?php if ($m = flash('success')): ?>
            <div class="alert alert-success small"><?= e($m) ?></div>
        <?php endif; ?>
        <form method="post" action="<?= admin_url('?route=login') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">E-posta</label>
                <input type="email" name="email" class="form-control form-control-lg" required value="admin@randevu.local">
            </div>
            <div class="mb-3">
                <label class="form-label">Şifre</label>
                <input type="password" name="password" class="form-control form-control-lg" required placeholder="••••••••">
                <small class="form-help">Varsayılan: password</small>
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> Giriş Yap
            </button>
        </form>
        <p class="text-center text-muted small mt-4 mb-0">
            <a href="<?= base_url() ?>" class="text-decoration-none">← Ana sayfa</a>
        </p>
    </div>
</div>
</body>
</html>
