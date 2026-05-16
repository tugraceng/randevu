<?php
$loggedIn = is_customer_logged_in();
$verified = $loggedIn && !empty(customer_user()['email_verified_at']);
?>
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content appointment-modal">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title mb-0" id="appointmentModalLabel">Online Randevu</h5>
                    <small class="text-muted">5 adımda randevunuzu oluşturun</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <?php if (!$loggedIn): ?>
                <div class="alert alert-info border-0 bg-light">
                    <h6 class="mb-2"><i class="bi bi-person-circle me-1"></i> Randevu almak için giriş yapın</h6>
                    <p class="mb-3 text-muted small">Randevularınızı, paketlerinizi ve ödemelerinizi takip edebilmek için üyeliğiniz gerekiyor.</p>
                    <a href="<?= customer_url('?route=login') ?>" class="btn btn-cta btn-sm me-2">Giriş Yap</a>
                    <a href="<?= customer_url('?route=register') ?>" class="btn btn-outline-primary btn-sm">Kayıt Ol</a>
                </div>
                <?php elseif (!$verified): ?>
                <div class="alert alert-warning border-0">
                    <h6 class="mb-2"><i class="bi bi-shield-exclamation me-1"></i> E-posta doğrulaması gerekli</h6>
                    <p class="mb-2 text-muted small">Randevu oluşturabilmek için lütfen e-posta adresinizi doğrulayın.</p>
                    <a href="<?= customer_url('?route=verify-email') ?>" class="btn btn-warning btn-sm">Doğrula</a>
                </div>
                <?php else: ?>
                <form id="appointment-form" data-step="1">
                    <?= csrf_field() ?>
                    <div class="stepper-bar">
                        <span class="step active" data-step="1"><span class="step-num">1</span>Hizmet</span>
                        <span class="step" data-step="2"><span class="step-num">2</span>Personel</span>
                        <span class="step" data-step="3"><span class="step-num">3</span>Tarih &amp; Saat</span>
                        <span class="step" data-step="4"><span class="step-num">4</span>Onay</span>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="step-panel" data-panel="1">
                                <label class="form-label">Hizmet seçin</label>
                                <select name="service_id" id="service_id" class="form-select form-select-lg" required>
                                    <option value="">— Hizmet seçin —</option>
                                    <?php foreach ($services as $svc): ?>
                                    <option value="<?= (int)$svc['id'] ?>"
                                            data-price="<?= e((string)$svc['price']) ?>"
                                            data-duration="<?= (int)($svc['duration_minutes'] ?? 30) ?>">
                                        <?= e($svc['name']) ?> — <?= format_money((float)$svc['price']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!empty($customer_packages)): ?>
                                <label class="form-label mt-3">Paket kullan (opsiyonel)</label>
                                <select name="customer_package_id" id="customer_package_id" class="form-select">
                                    <option value="">Paket kullanma</option>
                                    <?php foreach ($customer_packages as $cp): ?>
                                    <option value="<?= (int)$cp['id'] ?>" data-service-id="<?= (int)$cp['service_id'] ?>" data-remaining-sessions="<?= (int)$cp['remaining_sessions'] ?>">
                                        <?= e($cp['package_name']) ?> (<?= (int)$cp['remaining_sessions'] ?> seans kaldı)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php endif; ?>
                            </div>

                            <div class="step-panel d-none" data-panel="2">
                                <label class="form-label">Personel</label>
                                <select name="staff_id" id="staff_id" class="form-select form-select-lg">
                                    <option value="">Farketmez (Sistem önersin)</option>
                                    <?php foreach ($staff as $m): ?>
                                    <option value="<?= (int)$m['id'] ?>"><?= e($m['name']) ?> — <?= e($m['title'] ?? '') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="step-panel d-none" data-panel="3">
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label class="form-label">Tarih</label>
                                        <input type="date" name="appointment_date" id="appointment_date" class="form-control form-control-lg" min="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="col-md-7">
                                        <label class="form-label">Uygun Saatler</label>
                                        <input type="hidden" name="start_time" id="start_time">
                                        <div id="slots-container" class="slots-grid"></div>
                                    </div>
                                </div>
                                <label class="form-label mt-3">Not (opsiyonel)</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Eklemek istediğiniz bir not?"></textarea>
                            </div>

                            <div class="step-panel d-none" data-panel="4">
                                <div class="alert alert-info border-0 bg-light">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Bilgilerinizi kontrol edin. <strong>Randevu Oluştur</strong> butonuna bastığınızda işlem onaylanır.
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="summary-card sticky-top" id="appointment-summary" style="top: 1rem;">
                                <h6>Randevu Özeti</h6>
                                <p class="text-muted small mb-0">Adımları doldurdukça özet otomatik güncellenir.</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary" id="btn-prev" disabled>
                            <i class="bi bi-arrow-left me-1"></i> Geri
                        </button>
                        <div>
                            <button type="button" class="btn btn-cta" id="btn-next">
                                İleri <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                            <button type="submit" class="btn btn-cta d-none" id="btn-submit">
                                <i class="bi bi-check-circle me-1"></i> Randevu Oluştur
                            </button>
                        </div>
                    </div>
                </form>
                <div id="appointment-loading" class="text-center py-4 d-none">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Yükleniyor</span></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
