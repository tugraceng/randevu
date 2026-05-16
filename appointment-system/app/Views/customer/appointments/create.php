<?php require APP_PATH . '/Views/customer/partials/header.php'; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="c-card">
            <div class="c-card-head">
                <h6><i class="bi bi-calendar-plus me-1"></i> Yeni Randevu</h6>
            </div>
            <form method="post" action="<?= customer_url('?route=appointments/store') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="service_id" id="service_id">
                <p class="text-muted small">Aşağıdan hizmet seçin, tarih ve saatinizi belirleyin.</p>

                <h6 class="mt-3 mb-2"><i class="bi bi-1-circle me-1"></i> Hizmet Seçin</h6>
                <div class="row g-3 mb-4">
                    <?php foreach ($services as $svc): ?>
                    <div class="col-md-6">
                        <div class="service-select-card" data-id="<?= (int)$svc['id'] ?>">
                            <h6><?= e($svc['name']) ?></h6>
                            <small class="text-muted d-block mb-1"><?= e($svc['description'] ?? '') ?></small>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted"><i class="bi bi-clock me-1"></i><?= (int)$svc['duration_minutes'] ?> dk</small>
                                <span class="price"><?= format_money((float)$svc['price']) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <h6 class="mt-3 mb-2"><i class="bi bi-2-circle me-1"></i> Tarih &amp; Personel</h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Tarih *</label>
                        <input type="date" name="appointment_date" id="appointment_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Personel</label>
                        <select name="staff_id" id="staff_id" class="form-select">
                            <option value="">Farketmez</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Saat *</label>
                        <input type="hidden" name="start_time" id="start_time">
                        <div id="slots-container" class="border rounded p-2" style="min-height:48px;">
                            <small class="text-muted">Önce hizmet ve tarih seçin.</small>
                        </div>
                    </div>
                </div>

                <?php if ($packages): ?>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-box me-1"></i> Paket Kullan (opsiyonel)</label>
                    <select name="customer_package_id" class="form-select">
                        <option value="">Paket kullanma</option>
                        <?php foreach ($packages as $p): ?>
                        <option value="<?= (int)$p['id'] ?>"><?= e($p['package_name']) ?> · <?= (int)$p['remaining_sessions'] ?> seans kaldı</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">Not (opsiyonel)</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Eklemek istediğiniz bir not?"></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= customer_url() ?>" class="btn btn-outline-secondary">İptal</a>
                    <button class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Randevuyu Oluştur</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="c-card">
            <div class="c-card-head"><h6><i class="bi bi-info-circle me-1"></i> Randevu Adımları</h6></div>
            <div class="stepper">
                <div class="step active"><span class="step-num">1</span> Hizmet Seçimi</div>
                <div class="step"><span class="step-num">2</span> Tarih &amp; Personel</div>
                <div class="step"><span class="step-num">3</span> Saat Seçimi</div>
                <div class="step"><span class="step-num">4</span> Onay</div>
            </div>
            <hr>
            <p class="small text-muted mb-0">
                <i class="bi bi-shield-check text-success me-1"></i> Randevunuz onaylandığında size SMS / e-posta ile bildirim gönderilecektir.
            </p>
        </div>
    </div>
</div>

<?php require APP_PATH . '/Views/customer/partials/footer.php'; ?>
