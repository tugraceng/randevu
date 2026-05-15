<?php
$loggedIn = is_customer_logged_in();
$verified = $loggedIn && !empty(customer_user()['email_verified_at']);
?>
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content appointment-modal">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="appointmentModalLabel">Online Randevu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <?php if (!$loggedIn): ?>
                <div class="alert alert-info mb-0">
                    Randevu almak için <a href="<?= customer_url('?route=login') ?>">giriş yapın</a> veya
                    <a href="<?= customer_url('?route=register') ?>">kayıt olun</a>.
                </div>
                <?php elseif (!$verified): ?>
                <div class="alert alert-warning mb-0">
                    E-posta doğrulaması gerekli. <a href="<?= customer_url('?route=verify-email') ?>">Doğrula</a>
                </div>
                <?php else: ?>
                <form id="appointment-form" data-step="1">
                    <?= csrf_field() ?>
                    <div class="step-indicator mb-4">
                        <span class="step active" data-step="1">1. Hizmet</span>
                        <span class="step" data-step="2">2. Personel</span>
                        <span class="step" data-step="3">3. Tarih</span>
                        <span class="step" data-step="4">4. Onay</span>
                    </div>
                    <div class="step-panel" data-panel="1">
                        <label class="form-label">Hizmet seçin</label>
                        <select name="service_id" id="service_id" class="form-select form-select-lg" required>
                            <option value="">Seçiniz</option>
                            <?php foreach ($services as $svc): ?>
                            <option value="<?= (int)$svc['id'] ?>" data-price="<?= e((string)$svc['price']) ?>">
                                <?= e($svc['name']) ?> — <?= format_money((float)$svc['price']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($customer_packages)): ?>
                        <label class="form-label mt-3">Paket kullan (opsiyonel)</label>
                        <select name="customer_package_id" id="customer_package_id" class="form-select">
                            <option value="">Paket kullanma</option>
                            <?php foreach ($customer_packages as $cp): ?>
                            <option value="<?= (int)$cp['id'] ?>">
                                <?= e($cp['package_name']) ?> (<?= (int)$cp['remaining_sessions'] ?> seans kaldı)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php endif; ?>
                    </div>
                    <div class="step-panel d-none" data-panel="2">
                        <label class="form-label">Personel</label>
                        <select name="staff_id" id="staff_id" class="form-select form-select-lg">
                            <option value="">Farketmez</option>
                            <?php foreach ($staff as $m): ?>
                            <option value="<?= (int)$m['id'] ?>"><?= e($m['name']) ?> — <?= e($m['title'] ?? '') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="step-panel d-none" data-panel="3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tarih</label>
                                <input type="date" name="appointment_date" id="appointment_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Saat</label>
                                <input type="hidden" name="start_time" id="start_time">
                                <div id="slots-container" class="slots-grid border rounded p-2 min-h-80"></div>
                            </div>
                        </div>
                        <label class="form-label mt-3">Not (opsiyonel)</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="step-panel d-none" data-panel="4">
                        <div id="appointment-summary" class="summary-box p-3 rounded"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary" id="btn-prev" disabled>Geri</button>
                        <button type="button" class="btn btn-cta" id="btn-next">İleri</button>
                        <button type="submit" class="btn btn-cta d-none" id="btn-submit">Randevu Oluştur</button>
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
