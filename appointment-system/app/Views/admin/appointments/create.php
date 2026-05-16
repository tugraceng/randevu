<?php
require APP_PATH . '/Views/admin/partials/header.php';
$preselect = (int)($_GET['customer_id'] ?? 0);
?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="panel mb-4">
            <div class="panel-header">
                <h6><i class="bi bi-person-plus me-1"></i> Yeni Müşteri Hızlı Kayıt</h6>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#newCustomerPane" aria-expanded="false">
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>
            <div class="collapse" id="newCustomerPane">
                <div class="panel-body">
                    <p class="text-muted small mb-3">Üyeliği olmayan müşteri için hızlı kayıt. E-posta otomatik doğrulanır; randevu hemen oluşturulabilir.</p>
                    <form id="admin-customer-create-form" class="row g-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="ajax" value="1">
                        <input type="hidden" name="verify_email" value="1">
                        <div class="col-md-6"><label class="form-label">Ad *</label><input name="first_name" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Soyad *</label><input name="last_name" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Telefon *</label><input name="phone" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">E-posta *</label><input name="email" type="email" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Şifre</label><input name="password" type="text" class="form-control" placeholder="Boş bırakılırsa otomatik üretilir"></div>
                        <div class="col-md-6 d-flex align-items-end gap-3 pb-2">
                            <div class="form-check"><input type="checkbox" name="sms_permission" value="1" class="form-check-input" id="quickSms" checked><label class="form-check-label" for="quickSms">SMS</label></div>
                            <div class="form-check"><input type="checkbox" name="whatsapp_permission" value="1" class="form-check-input" id="quickWa" checked><label class="form-check-label" for="quickWa">WhatsApp</label></div>
                        </div>
                        <div class="col-12 d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-success" id="btn-create-customer">
                                <i class="bi bi-person-plus me-1"></i> Müşteri Oluştur ve Seç
                            </button>
                            <span id="customer-create-msg" class="small text-muted"></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header"><h6><i class="bi bi-calendar-plus me-1"></i> Randevu Bilgileri</h6></div>
            <div class="panel-body">
                <form method="post" action="<?= admin_url('?route=appointments/save') ?>" id="appointment-form">
                    <?= csrf_field() ?>
                    <fieldset class="form-fieldset">
                        <legend>1. Müşteri &amp; Paket</legend>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Müşteri *</label>
                                <select name="customer_id" id="customer_id" class="form-select" required
                                        data-packages-url="<?= admin_url('?route=appointments/customer-packages') ?>">
                                    <option value="">Müşteri seçin</option>
                                    <?php foreach ($customers as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= $preselect === (int)$c['id'] ? 'selected' : '' ?>>
                                        <?= e($c['first_name'] . ' ' . $c['last_name']) ?> — <?= e($c['phone'] ?? '-') ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-help">Listede yoksa yukarıdaki "Yeni Müşteri" panelinden ekleyin.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Aktif Paket</label>
                                <select name="customer_package_id" id="customer_package_id" class="form-select">
                                    <option value="">Paket kullanma</option>
                                </select>
                                <small class="form-help">Müşteri seçildiğinde paketler otomatik yüklenir.</small>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="form-fieldset">
                        <legend>2. Hizmet &amp; Personel</legend>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Hizmet *</label>
                                <select name="service_id" id="service_id" class="form-select" required>
                                    <?php foreach ($services as $s): ?>
                                    <option value="<?= $s['id'] ?>" data-price="<?= e((string)$s['price']) ?>"><?= e($s['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Personel</label>
                                <select name="staff_id" id="staff_id" class="form-select">
                                    <option value="">Farketmez (sistem önersin)</option>
                                    <?php foreach ($staff as $st): ?>
                                    <option value="<?= $st['id'] ?>"><?= e($st['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="form-fieldset">
                        <legend>3. Tarih &amp; Saat</legend>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Tarih *</label>
                                <input type="date" name="appointment_date" id="appointment_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Uygun Saatler *</label>
                                <input type="hidden" name="start_time" id="start_time">
                                <div id="slots-container" class="slots-grid" style="min-height: 60px;"></div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="form-fieldset">
                        <legend>4. Ödeme &amp; Durum</legend>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Durum</label>
                                <select name="status" class="form-select">
                                    <option value="approved">Onaylı</option>
                                    <option value="pending">Bekliyor</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kapora Tutarı</label>
                                <input type="number" step="0.01" name="deposit_amount" class="form-control" value="0">
                            </div>
                            <div class="col-md-4 d-flex align-items-end pb-2">
                                <div class="form-check">
                                    <input type="checkbox" name="payment_required" value="1" class="form-check-input" id="payReq">
                                    <label class="form-check-label" for="payReq">Ödeme gerekli</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Not</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Müşteri hakkında not"></textarea>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="form-fieldset">
                        <legend>5. Bildirim Seçenekleri</legend>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check"><input type="checkbox" name="notify_mail" value="1" class="form-check-input" id="notMail" checked><label class="form-check-label" for="notMail"><i class="bi bi-envelope me-1"></i> E-posta</label></div>
                            <div class="form-check"><input type="checkbox" name="notify_sms" value="1" class="form-check-input" id="notSms" checked><label class="form-check-label" for="notSms"><i class="bi bi-chat-dots me-1"></i> SMS</label></div>
                            <div class="form-check"><input type="checkbox" name="notify_whatsapp" value="1" class="form-check-input" id="notWa"><label class="form-check-label" for="notWa"><i class="bi bi-whatsapp me-1"></i> WhatsApp</label></div>
                        </div>
                    </fieldset>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= admin_url('?route=appointments') ?>" class="btn btn-outline-secondary">Vazgeç</a>
                        <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-circle me-1"></i> Randevu Oluştur</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="panel" style="position: sticky; top: 90px;">
            <div class="panel-header">
                <h6><i class="bi bi-receipt me-1"></i> Canlı Özet</h6>
            </div>
            <div class="panel-body">
                <div id="appointment-summary">
                    <h6 class="fw-semibold mb-3 text-muted small text-uppercase">Canlı Özet</h6>
                    <p class="text-muted small mb-0">Form alanlarını doldurdukça özet otomatik güncellenir.</p>
                </div>
            </div>
            <div class="panel-foot small text-muted">
                <i class="bi bi-info-circle me-1"></i> Müşterinin bildirimleri yalnızca seçili izinler doğrultusunda gönderilir.
            </div>
        </div>
    </div>
</div>

<script>
    window.ADMIN_CUSTOMER_CREATE_URL = <?= json_encode(admin_url('?route=customers/create')) ?>;
</script>
<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
