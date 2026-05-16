<?php
$loggedIn = is_customer_logged_in();
$verified = $loggedIn && !empty(customer_user()['email_verified_at']);
$services = $services ?? [];
$staff    = $staff ?? [];
$customer_packages = $customer_packages ?? [];
?>
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0" id="appointmentModalLabel"><i class="bi bi-calendar2-week me-1"></i> Online Randevu Sihirbazı</h5>
                    <small class="text-white-50">6 adımda saniyeler içinde randevunuzu oluşturun</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <?php if (!$loggedIn): ?>
                    <div class="p-4 text-center">
                        <div class="brand-icon-inline mx-auto mb-3" style="width:56px;height:56px;font-size:1.4rem;"><i class="bi bi-person-circle"></i></div>
                        <h5>Randevu için giriş yapın</h5>
                        <p class="text-muted">Randevularınızı, paketlerinizi ve ödemelerinizi takip edebilmek için üyeliğiniz gerekiyor.</p>
                        <button type="button" class="btn btn-cta me-2" data-auth-open="login" data-bs-dismiss="modal">Giriş Yap</button>
                        <button type="button" class="btn btn-outline-primary" data-auth-open="register" data-bs-dismiss="modal">Üye Ol</button>
                    </div>
                <?php elseif (!$verified): ?>
                    <div class="p-4 text-center">
                        <div class="brand-icon-inline mx-auto mb-3" style="width:56px;height:56px;font-size:1.4rem;background:var(--warning);"><i class="bi bi-shield-exclamation"></i></div>
                        <h5>E-posta doğrulaması gerekli</h5>
                        <p class="text-muted">Randevu oluşturabilmek için lütfen e-posta adresinizi doğrulayın.</p>
                        <button type="button" class="btn btn-warning" data-auth-open="verify" data-bs-dismiss="modal">Doğrulamayı Aç</button>
                    </div>
                <?php else: ?>

                <div class="stepper-shell">
                    <!-- MAIN ============================================== -->
                    <div class="stepper-main">

                        <div class="stepper-progress">
                            <div class="step active"><span class="step-num">1</span><span>Hizmet</span></div>
                            <div class="step"><span class="step-num">2</span><span>Personel</span></div>
                            <div class="step"><span class="step-num">3</span><span>Tarih</span></div>
                            <div class="step"><span class="step-num">4</span><span>Saat</span></div>
                            <div class="step"><span class="step-num">5</span><span>Not</span></div>
                            <div class="step"><span class="step-num">6</span><span>Onay</span></div>
                        </div>

                        <form method="post" action="<?= customer_url('?route=appointments/store') ?>" data-appointment-form>
                            <?= csrf_field() ?>
                            <input type="hidden" data-bind="service_id"        name="service_id">
                            <input type="hidden" data-bind="staff_id"          name="staff_id">
                            <input type="hidden" data-bind="appointment_date"  name="appointment_date">
                            <input type="hidden" data-bind="start_time"        name="start_time">
                            <input type="hidden" data-bind="notes"             name="notes">

                            <!-- STEP 1 — Service ============================ -->
                            <div class="stepper-panel active" data-step="1">
                                <h6 class="text-muted mb-3" style="text-transform:none;letter-spacing:0;font-weight:500;">Hizmet seçin</h6>
                                <div class="choice-grid">
                                    <?php foreach ($services as $svc): ?>
                                    <div class="choice-tile"
                                         data-choose-service
                                         data-id="<?= (int)$svc['id'] ?>"
                                         data-name="<?= e($svc['name']) ?>"
                                         data-price="<?= format_money((float)$svc['price']) ?>"
                                         data-duration="<?= (int)($svc['duration_minutes'] ?? 30) ?>">
                                        <h6><?= e($svc['name']) ?></h6>
                                        <small><i class="bi bi-clock me-1"></i><?= (int)($svc['duration_minutes'] ?? 30) ?> dk</small><br>
                                        <strong class="text-primary"><?= format_money((float)$svc['price']) ?></strong>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- STEP 2 — Staff ============================ -->
                            <div class="stepper-panel" data-step="2">
                                <h6 class="text-muted mb-3" style="text-transform:none;letter-spacing:0;font-weight:500;">Personel seçin</h6>
                                <div class="choice-grid">
                                    <div class="choice-tile" data-choose-staff data-id="" data-name="Sistem önerir">
                                        <h6><i class="bi bi-shuffle me-1"></i> Farketmez</h6>
                                        <small>Sistem en uygun personeli atar</small>
                                    </div>
                                    <?php foreach ($staff as $m): ?>
                                    <div class="choice-tile" data-choose-staff
                                         data-id="<?= (int)$m['id'] ?>"
                                         data-name="<?= e($m['name']) ?>">
                                        <h6><?= e($m['name']) ?></h6>
                                        <small><?= e($m['title'] ?? '') ?></small>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- STEP 3 — Date ============================ -->
                            <div class="stepper-panel" data-step="3">
                                <h6 class="text-muted mb-3" style="text-transform:none;letter-spacing:0;font-weight:500;">Randevu tarihinizi seçin</h6>
                                <input type="date" class="form-control" data-step-date min="<?= date('Y-m-d') ?>">
                            </div>

                            <!-- STEP 4 — Time slots ============================ -->
                            <div class="stepper-panel" data-step="4">
                                <h6 class="text-muted mb-3" style="text-transform:none;letter-spacing:0;font-weight:500;">Uygun saatler</h6>
                                <div class="slot-grid" data-slot-grid>
                                    <small class="text-muted">Lütfen önce tarih seçin.</small>
                                </div>
                            </div>

                            <!-- STEP 5 — Note ============================ -->
                            <div class="stepper-panel" data-step="5">
                                <h6 class="text-muted mb-3" style="text-transform:none;letter-spacing:0;font-weight:500;">Eklemek istediğiniz bir not?</h6>
                                <textarea class="form-control" rows="4" data-step-note placeholder="Örn: Alerji bilgim..."></textarea>
                                <?php if (!empty($customer_packages)): ?>
                                <label class="form-label mt-3">Paket kullan (opsiyonel)</label>
                                <select name="customer_package_id" class="form-select">
                                    <option value="">Paket kullanma</option>
                                    <?php foreach ($customer_packages as $cp): ?>
                                    <option value="<?= (int)$cp['id'] ?>"><?= e($cp['package_name']) ?> (<?= (int)$cp['remaining_sessions'] ?> seans kaldı)</option>
                                    <?php endforeach; ?>
                                </select>
                                <?php endif; ?>
                            </div>

                            <!-- STEP 6 — Confirm ============================ -->
                            <div class="stepper-panel" data-step="6">
                                <div class="alert alert-info border-0" style="background:var(--primary-soft);color:var(--primary);">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Bilgilerinizi sağdaki özet kartında kontrol edin ve <strong>Randevu Oluştur</strong> butonuna basın.
                                </div>
                                <p class="text-muted small">Randevunuz oluşturulduktan sonra size SMS / e-posta üzerinden onay bilgisi gönderilecektir.</p>
                            </div>

                            <div class="stepper-actions">
                                <button type="button" class="btn btn-outline-secondary" data-step-prev>
                                    <i class="bi bi-arrow-left me-1"></i> Geri
                                </button>
                                <div>
                                    <button type="button" class="btn btn-cta" data-step-next>
                                        İleri <i class="bi bi-arrow-right ms-1"></i>
                                    </button>
                                    <button type="button" class="btn btn-cta" data-step-submit style="display:none;">
                                        <i class="bi bi-check-circle me-1"></i> Randevu Oluştur
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ASIDE ============================================== -->
                    <aside class="stepper-aside">
                        <div class="summary-card">
                            <h6>Randevu Özeti</h6>
                            <div class="row-line"><span class="label">Hizmet</span><span class="value muted" data-sum="service">—</span></div>
                            <div class="row-line"><span class="label">Personel</span><span class="value muted" data-sum="staff">—</span></div>
                            <div class="row-line"><span class="label">Tarih</span><span class="value muted" data-sum="date">—</span></div>
                            <div class="row-line"><span class="label">Saat</span><span class="value muted" data-sum="time">—</span></div>
                            <div class="total"><span>Tahmini Tutar</span><strong data-sum-total>—</strong></div>
                        </div>
                        <div class="mt-3 small text-muted">
                            <i class="bi bi-shield-check text-success me-1"></i> Güvenli online randevu sistemi.
                        </div>
                    </aside>
                </div>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
