<?php require APP_PATH . '/Views/admin/partials/header.php'; $s = $settings; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <form method="post" action="<?= admin_url('?route=settings/save') ?>">
            <?= csrf_field() ?>
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#general">Genel</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#whatsapp">WhatsApp</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#netgsm">NetGSM</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#payment">Ödeme</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#mail">E-posta</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="general">
                    <div class="card p-3 mb-3">
                        <?php foreach (['site_title','business_name','site_phone','site_email','site_address'] as $key): ?>
                        <div class="mb-2">
                            <label class="form-label"><?= e($key) ?></label>
                            <input class="form-control" name="settings[<?= e($key) ?>]" value="<?= e($s[$key] ?? '') ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="tab-pane fade" id="whatsapp">
                    <div class="card integration-card p-3">
                        <span class="status-dot <?= ($s['whatsapp_status']??0)?'connected':'warning' ?>"></span>
                        <?= ($s['whatsapp_status']??0) ? 'Bağlı' : 'Yapılandırma Gerekli' ?>
                        <?php foreach (['whatsapp_business_account_id','whatsapp_phone_number_id','whatsapp_access_token','whatsapp_verify_token','whatsapp_api_version','whatsapp_default_language','whatsapp_status'] as $key): ?>
                        <div class="mb-2 mt-2"><label class="form-label small"><?= e($key) ?></label>
                        <input class="form-control form-control-sm" name="settings[<?= e($key) ?>]" value="<?= e($s[$key] ?? '') ?>"></div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="tab-pane fade" id="netgsm">
                    <div class="card integration-card p-3">
                        <?php foreach (['netgsm_usercode','netgsm_password','netgsm_header','netgsm_endpoint','netgsm_status'] as $key): ?>
                        <div class="mb-2"><label class="form-label small"><?= e($key) ?></label>
                        <input class="form-control form-control-sm" name="settings[<?= e($key) ?>]" value="<?= e($s[$key] ?? '') ?>" type="<?= str_contains($key,'password')?'password':'text' ?>"></div>
                        <?php endforeach; ?>
                        <input class="form-control form-control-sm mt-2" name="test_sms_phone" placeholder="Test SMS telefon">
                    </div>
                </div>
                <div class="tab-pane fade" id="payment">
                    <div class="card p-3">
                        <select class="form-select mb-2" name="settings[payment_provider]">
                            <option value="iyzico" <?= ($s['payment_provider']??'')==='iyzico'?'selected':'' ?>>iyzico</option>
                            <option value="paytr" <?= ($s['payment_provider']??'')==='paytr'?'selected':'' ?>>PayTR</option>
                        </select>
                        <?php foreach (['payment_status','iyzico_api_key','iyzico_secret_key','iyzico_base_url','paytr_merchant_id','paytr_merchant_key','paytr_merchant_salt'] as $key): ?>
                        <div class="mb-2"><input class="form-control form-control-sm" name="settings[<?= e($key) ?>]" value="<?= e($s[$key] ?? '') ?>" placeholder="<?= e($key) ?>"></div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="tab-pane fade" id="mail">
                    <div class="card p-3">
                        <?php foreach (['mail_host','mail_port','mail_username','mail_password','mail_encryption','mail_from_email','mail_from_name'] as $key): ?>
                        <div class="mb-2"><input class="form-control form-control-sm" name="settings[<?= e($key) ?>]" value="<?= e($s[$key] ?? '') ?>" placeholder="<?= e($key) ?>"></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Kaydet</button>
        </form>
    </div>
    <div class="col-lg-4">
        <div class="card p-3">
            <h6>Şablon Değişkenleri</h6>
            <p class="small text-muted">{name} {date} {time} {service} {staff} {package} {remaining_sessions} {business_name}</p>
            <a href="<?= admin_url('?route=messages') ?>" class="btn btn-outline-primary btn-sm">Şablonları Düzenle</a>
        </div>
    </div>
</div>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
