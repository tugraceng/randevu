<?php require APP_PATH . '/Views/admin/partials/header.php'; $a = $appointment; ?>

<div class="section-title-bar">
    <div>
        <h5>Randevu Düzenle <span class="text-muted">#<?= (int)$a['id'] ?></span></h5>
        <small class="text-muted">Müşteri, hizmet, personel ve tarihi güncelleyin</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= admin_url('?route=appointments/show&id=' . (int)$a['id']) ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Detay
        </a>
        <a href="<?= admin_url('?route=appointments') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-list-ul me-1"></i> Liste
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-9">
        <div class="panel">
            <div class="panel-header"><h6><i class="bi bi-pencil-square me-1"></i> Randevu Bilgileri</h6></div>
            <form method="post" action="<?= admin_url('?route=appointments/update') ?>" class="panel-body">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">

                <fieldset class="form-fieldset">
                    <legend>1. Müşteri &amp; Hizmet</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Müşteri *</label>
                            <select name="customer_id" class="form-select" required>
                                <?php foreach ($customers as $c): ?>
                                <option value="<?= (int)$c['id'] ?>" <?= $c['id'] == $a['customer_id'] ? 'selected' : '' ?>>
                                    <?= e($c['first_name'].' '.$c['last_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hizmet *</label>
                            <select name="service_id" id="service_id" class="form-select" required>
                                <?php foreach ($services as $s): ?>
                                <option value="<?= (int)$s['id'] ?>" <?= $s['id'] == $a['service_id'] ? 'selected' : '' ?>>
                                    <?= e($s['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="form-fieldset">
                    <legend>2. Personel &amp; Zaman</legend>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Personel</label>
                            <select name="staff_id" id="staff_id" class="form-select">
                                <option value="">Farketmez</option>
                                <?php foreach ($staff as $st): ?>
                                <option value="<?= (int)$st['id'] ?>" <?= $st['id'] == $a['staff_id'] ? 'selected' : '' ?>>
                                    <?= e($st['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tarih *</label>
                            <input type="date" name="appointment_date" id="appointment_date" class="form-control" value="<?= e($a['appointment_date']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Saat *</label>
                            <input type="time" name="start_time" id="start_time" class="form-control" value="<?= e(substr($a['start_time'], 0, 5)) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-muted">Uygun Saatler</label>
                            <div id="slots-container" class="slots-grid" style="min-height:48px;"></div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="form-fieldset">
                    <legend>3. Durum &amp; Not</legend>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Durum</label>
                            <select name="status" class="form-select">
                                <?php foreach (['pending'=>'Bekliyor','approved'=>'Onaylı','completed'=>'Tamamlandı','cancelled'=>'İptal','no_show'=>'Gelmedi'] as $st => $label): ?>
                                <option value="<?= e($st) ?>" <?= $a['status'] === $st ? 'selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Not</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Bu randevuyla ilgili not"><?= e($a['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </fieldset>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= admin_url('?route=appointments/show&id=' . (int)$a['id']) ?>" class="btn btn-outline-secondary">Vazgeç</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Değişiklikleri Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="panel">
            <div class="panel-header"><h6><i class="bi bi-info-circle me-1"></i> Hatırlatma</h6></div>
            <div class="panel-body small text-muted">
                <p class="mb-2"><i class="bi bi-shield-check text-success me-1"></i> Tarih veya saat değiştirirseniz müşteriye otomatik bildirim gönderilmez. Detay sayfasından bildirim gönderebilirsiniz.</p>
                <p class="mb-0"><i class="bi bi-clock-history text-primary me-1"></i> Tüm değişiklikler işlem geçmişine kaydedilir.</p>
            </div>
        </div>
    </div>
</div>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
