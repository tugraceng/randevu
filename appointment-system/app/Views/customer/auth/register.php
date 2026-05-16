<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üye Ol · RandevuTakip</title>
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
            max-width: 480px;
            width: 100%;
        }
    </style>
</head>
<body>
<div class="auth-card">
    <div class="text-center mb-4">
        <span class="brand-icon mb-3 d-inline-flex" style="width:52px;height:52px;font-size:1.4rem;"><i class="bi bi-person-plus"></i></span>
        <h4 class="fw-bold mb-1">Hesap Oluşturun</h4>
        <p class="text-muted small mb-0">Birkaç saniyede kayıt olun, randevu almaya başlayın.</p>
    </div>
    <?php if ($m = flash('error')): ?><div class="alert alert-danger small"><?= e($m) ?></div><?php endif; ?>
    <?php if ($m = flash('success')): ?><div class="alert alert-success small"><?= e($m) ?></div><?php endif; ?>
    <form method="post" action="<?= customer_url('?route=register') ?>">
        <?= csrf_field() ?>
        <div class="row g-2 mb-2">
            <div class="col-md-6"><label class="form-label">Ad *</label><input class="form-control" name="first_name" required></div>
            <div class="col-md-6"><label class="form-label">Soyad *</label><input class="form-control" name="last_name" required></div>
        </div>
        <div class="mb-2"><label class="form-label">E-posta *</label><input class="form-control" name="email" type="email" required></div>
        <div class="mb-2"><label class="form-label">Telefon</label><input class="form-control" name="phone" placeholder="+90 5xx xxx xx xx"></div>
        <div class="mb-3"><label class="form-label">Şifre *</label><input class="form-control" name="password" type="password" minlength="8" required></div>
        <div class="d-flex flex-wrap gap-3 mb-3">
            <div class="form-check"><input class="form-check-input" type="checkbox" name="sms_permission" value="1" id="rSms" checked><label class="form-check-label small" for="rSms">SMS izni</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="whatsapp_permission" value="1" id="rWa" checked><label class="form-check-label small" for="rWa">WhatsApp izni</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="marketing_permission" value="1" id="rMkt"><label class="form-check-label small" for="rMkt">Kampanya bildirimleri</label></div>
        </div>
        <button class="btn btn-primary w-100"><i class="bi bi-check2 me-1"></i> Kayıt Ol</button>
    </form>
    <hr class="my-4">
    <p class="text-center small mb-0">Zaten hesabınız var mı? <a href="<?= customer_url('?route=login') ?>" class="fw-semibold">Giriş yapın</a></p>
</div>
</body>
</html>
