<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteri Girişi · RandevuTakip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/customer.css') ?>" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(circle at 20% 20%, rgba(79,70,229,.18), transparent 55%),
                radial-gradient(circle at 80% 80%, rgba(14,165,233,.18), transparent 55%),
                var(--c-bg);
            padding: 2rem 1rem;
        }
        .auth-card {
            background: #fff;
            border-radius: 20px;
            padding: 2.25rem;
            box-shadow: 0 18px 40px rgba(15, 23, 42, .1);
            max-width: 420px;
            width: 100%;
        }
    </style>
</head>
<body>
<div class="auth-card">
    <div class="text-center mb-4">
        <span class="brand-icon mb-3 d-inline-flex" style="width:52px;height:52px;font-size:1.4rem;"><i class="bi bi-person"></i></span>
        <h4 class="fw-bold mb-1">Müşteri Girişi</h4>
        <p class="text-muted small mb-0">Hesabınıza giriş yaparak randevularınızı yönetin</p>
    </div>
    <?php if ($m = flash('error')): ?><div class="alert alert-danger small"><?= e($m) ?></div><?php endif; ?>
    <?php if ($m = flash('success')): ?><div class="alert alert-success small"><?= e($m) ?></div><?php endif; ?>
    <form method="post" action="<?= customer_url('?route=login') ?>">
        <?= csrf_field() ?>
        <div class="mb-3"><label class="form-label">E-posta</label><input class="form-control" type="email" name="email" required></div>
        <div class="mb-3"><label class="form-label">Şifre</label><input class="form-control" type="password" name="password" required></div>
        <button class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right me-1"></i> Giriş Yap</button>
    </form>
    <hr class="my-4">
    <div class="text-center small">
        Hesabınız yok mu? <a href="<?= customer_url('?route=register') ?>" class="fw-semibold">Hemen kayıt olun</a>
    </div>
    <p class="text-center text-muted small mt-3 mb-0">
        <a href="<?= base_url() ?>" class="text-decoration-none">← Ana sayfa</a>
    </p>
</div>
</body>
</html>
