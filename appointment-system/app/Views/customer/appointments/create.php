<?php require APP_PATH . '/Views/customer/partials/header.php'; ?>
<div class="row g-4">
<div class="col-lg-8">
<p class="text-muted">İhtiyacınıza uygun hizmeti seçin ve randevu oluşturun.</p>
<form method="post" action="<?= customer_url('?route=appointments/store') ?>"><?= csrf_field() ?>
<input type="hidden" name="service_id" id="service_id">
<div class="row g-3 mb-4"><?php foreach ($services as $svc): ?>
<div class="col-md-6"><div class="card service-select-card p-3" data-id="<?= $svc['id'] ?>">
<h6><?= e($svc['name']) ?></h6><p class="text-primary fw-bold mb-0"><?= format_money((float)$svc['price']) ?></p>
<small class="text-muted"><?= (int)$svc['duration_minutes'] ?> dk</small></div></div>
<?php endforeach; ?></div>
<div class="row g-3">
<div class="col-md-4"><label>Tarih</label><input type="date" name="appointment_date" id="appointment_date" class="form-control" min="<?= date('Y-m-d') ?>" required></div>
<div class="col-md-4"><label>Personel</label><select name="staff_id" id="staff_id" class="form-select"><option value="">Farketmez</option></select></div>
<div class="col-md-4"><label>Saat</label><input type="hidden" name="start_time" id="start_time"><div id="slots-container" class="border rounded p-2"></div>
<?php if ($packages): ?><div class="col-12"><label>Paket (opsiyonel)</label><select name="customer_package_id" class="form-select">
<option value="">Paket kullanma</option><?php foreach ($packages as $p): ?><option value="<?= $p['id'] ?>"><?= e($p['package_name']) ?> (<?= (int)$p['remaining_sessions'] ?> seans)</option><?php endforeach; ?>
</select></div><?php endif; ?>
<div class="col-12"><textarea name="notes" class="form-control" placeholder="Not"></textarea></div>
<div class="col-12 text-end"><a href="<?= customer_url() ?>" class="btn btn-outline-secondary">İptal</a> <button class="btn btn-primary">İlerle / Kaydet</button></div>
</div></form>
</div>
<div class="col-lg-4"><div class="card p-3"><h6>Randevu Adımları</h6>
<div class="stepper"><div class="step active"><span class="step-num">1</span> Servis Seçimi</div>
<div class="step"><span class="step-num">2</span> Personel & Tarih</div>
<div class="step"><span class="step-num">3</span> Onay</div></div></div></div>
</div>
<?php require APP_PATH . '/Views/customer/partials/footer.php'; ?>
