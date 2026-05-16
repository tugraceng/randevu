<?php require APP_PATH . '/Views/customer/partials/header.php'; $c = $customer; ?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="c-card">
            <div class="c-card-head"><h6><i class="bi bi-person me-1"></i> Profil Bilgileri</h6></div>
            <form method="post" action="<?= customer_url('?route=profile') ?>" class="row g-3">
                <?= csrf_field() ?>
                <div class="col-md-6"><label class="form-label">Ad</label><input class="form-control" name="first_name" value="<?= e($c['first_name']) ?>" required></div>
                <div class="col-md-6"><label class="form-label">Soyad</label><input class="form-control" name="last_name" value="<?= e($c['last_name']) ?>" required></div>
                <div class="col-md-6"><label class="form-label">Telefon</label><input class="form-control" name="phone" value="<?= e($c['phone'] ?? '') ?>"></div>
                <div class="col-md-6"><label class="form-label">E-posta</label><input class="form-control" value="<?= e($c['email']) ?>" disabled></div>
                <div class="col-12">
                    <label class="form-label">İletişim İzinleri</label>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="form-check"><input type="checkbox" name="sms_permission" value="1" class="form-check-input" id="prSms" <?= !empty($c['sms_permission']) ? 'checked' : '' ?>><label class="form-check-label" for="prSms">SMS izni</label></div>
                        <div class="form-check"><input type="checkbox" name="whatsapp_permission" value="1" class="form-check-input" id="prWa" <?= !empty($c['whatsapp_permission']) ? 'checked' : '' ?>><label class="form-check-label" for="prWa">WhatsApp izni</label></div>
                        <div class="form-check"><input type="checkbox" name="marketing_permission" value="1" class="form-check-input" id="prMkt" <?= !empty($c['marketing_permission']) ? 'checked' : '' ?>><label class="form-check-label" for="prMkt">Pazarlama izni</label></div>
                    </div>
                </div>
                <div class="col-12"><button class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Kaydet</button></div>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="c-card">
            <div class="c-card-head"><h6><i class="bi bi-shield-lock me-1"></i> Şifre Değiştir</h6></div>
            <form method="post" action="<?= customer_url('?route=profile/password') ?>">
                <?= csrf_field() ?>
                <div class="mb-2"><label class="form-label">Mevcut şifre</label><input type="password" class="form-control" name="current_password" required></div>
                <div class="mb-2"><label class="form-label">Yeni şifre</label><input type="password" class="form-control" name="password" minlength="8" required></div>
                <div class="mb-3"><label class="form-label">Yeni şifre (tekrar)</label><input type="password" class="form-control" name="password_confirm" minlength="8" required></div>
                <button class="btn btn-outline-primary w-100"><i class="bi bi-key me-1"></i> Şifreyi Güncelle</button>
            </form>
        </div>
    </div>
</div>

<?php require APP_PATH . '/Views/customer/partials/footer.php'; ?>
