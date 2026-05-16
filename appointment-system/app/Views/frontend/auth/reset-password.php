<?php
$siteTitle = $settings['site_title'] ?? 'RandevuTakip';
$primary   = $settings['theme_primary']   ?? '#4f46e5';
$secondary = $settings['theme_secondary'] ?? '#0ea5e9';
$flashErr  = flash('error');
$flashOk   = flash('success');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Şifre Sıfırla') ?> · <?= e($siteTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/frontend.css') ?>" rel="stylesheet">
    <style>
        :root {
            --primary: <?= e($primary) ?>;
            --primary-dark: <?= e($primary) ?>;
            --secondary: <?= e($secondary) ?>;
            --primary-soft: <?= e($primary) ?>1f;
        }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(circle at 15% 20%, var(--primary-soft), transparent 55%),
                radial-gradient(circle at 85% 80%, color-mix(in srgb, var(--secondary) 22%, transparent), transparent 60%),
                #f8fafc;
            padding: 2rem 1rem;
        }
        .reset-card {
            width: 100%;
            max-width: 460px;
            background: #fff;
            border-radius: 24px;
            padding: 2.5rem 2rem;
            box-shadow: 0 30px 60px -20px rgba(15, 23, 42, .25);
            border: 1px solid rgba(15, 23, 42, .04);
        }
        .reset-brand {
            display: inline-flex;
            align-items: center;
            gap: .65rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }
        .reset-brand span:first-child {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            border-radius: 12px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

    <div class="reset-card">
        <div class="reset-brand">
            <span><i class="bi bi-calendar2-check"></i></span>
            <span><?= e($siteTitle) ?></span>
        </div>

        <?php if (!$valid): ?>
            <div class="text-center">
                <div class="auth-verify__icon mb-3" style="background:rgba(239,68,68,.12);color:#ef4444;"><i class="bi bi-shield-exclamation"></i></div>
                <h4 class="fw-bold mb-2">Bağlantı geçersiz</h4>
                <p class="text-muted">Şifre sıfırlama bağlantınız geçersiz veya süresi dolmuş.<br>Yeni bir bağlantı talep edin.</p>
                <a href="<?= base_url('?auth=forgot') ?>" class="btn btn-cta mt-3 w-100">
                    <i class="bi bi-arrow-clockwise me-1"></i> Yeniden Talep Et
                </a>
                <a href="<?= base_url() ?>" class="btn btn-link mt-2 w-100">Ana Sayfaya Dön</a>
            </div>
        <?php else: ?>
            <h4 class="fw-bold mb-1">Yeni şifre belirleyin</h4>
            <p class="text-muted mb-3">Hesabınız için yeni bir şifre oluşturun.</p>

            <?php if ($flashErr): ?>
                <div class="alert alert-danger py-2"><i class="bi bi-exclamation-circle me-1"></i><?= e($flashErr) ?></div>
            <?php endif; ?>
            <?php if ($flashOk): ?>
                <div class="alert alert-success py-2"><i class="bi bi-check-circle me-1"></i><?= e($flashOk) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('?route=reset-password') ?>" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= e($token) ?>">

                <div class="auth-field">
                    <label class="auth-label">Yeni Şifre <span class="auth-hint">(en az 8 karakter)</span></label>
                    <div class="auth-input">
                        <i class="bi bi-lock auth-input__icon"></i>
                        <input type="password" name="password" minlength="8" required autocomplete="new-password" autofocus>
                        <button type="button" class="auth-input__suffix" data-pwd-toggle aria-label="Göster"><i class="bi bi-eye"></i></button>
                    </div>
                </div>

                <div class="auth-field">
                    <label class="auth-label">Yeni Şifre (Tekrar)</label>
                    <div class="auth-input">
                        <i class="bi bi-lock-fill auth-input__icon"></i>
                        <input type="password" name="password_confirm" minlength="8" required autocomplete="new-password">
                        <button type="button" class="auth-input__suffix" data-pwd-toggle aria-label="Göster"><i class="bi bi-eye"></i></button>
                    </div>
                </div>

                <button type="submit" class="auth-cta mt-3">
                    <span class="lbl">Şifreyi Güncelle</span>
                    <i class="bi bi-check2 ms-1"></i>
                </button>

                <a href="<?= base_url() ?>" class="auth-link d-block text-center mt-3">
                    <i class="bi bi-arrow-left me-1"></i> Ana Sayfaya Dön
                </a>
            </form>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener('click', (e) => {
        const tog = e.target.closest('[data-pwd-toggle]');
        if (!tog) return;
        const input = tog.parentElement.querySelector('input');
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
        const icon = tog.querySelector('i');
        if (icon) icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
    });
    </script>
</body>
</html>
