<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>
<div class="table-card p-4 col-lg-10">
<form method="post" action="<?= admin_url('?route=appointments/save') ?>">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Müşteri</label>
            <select name="customer_id" id="customer_id" class="form-select" required data-packages-url="<?= admin_url('?route=appointments/customer-packages') ?>">
                <option value="">Seçin</option>
                <?php foreach ($customers as $c): ?>
                <option value="<?= $c['id'] ?>"><?= e($c['first_name'] . ' ' . $c['last_name']) ?> — <?= e($c['phone'] ?? '') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Aktif Paket</label>
            <select name="customer_package_id" id="customer_package_id" class="form-select"><option value="">Paket kullanma</option></select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Hizmet</label>
            <select name="service_id" id="service_id" class="form-select" required>
                <?php foreach ($services as $s): ?><option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Personel</label>
            <select name="staff_id" id="staff_id" class="form-select">
                <option value="">Farketmez</option>
                <?php foreach ($staff as $st): ?><option value="<?= $st['id'] ?>"><?= e($st['name']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4"><label class="form-label">Tarih</label><input type="date" name="appointment_date" id="appointment_date" class="form-control" min="<?= date('Y-m-d') ?>" required></div>
        <div class="col-md-8">
            <label class="form-label">Saat</label>
            <input type="hidden" name="start_time" id="start_time">
            <div id="slots-container" class="border rounded p-2"></div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Durum</label>
            <select name="status" class="form-select"><option value="approved">Onaylı</option><option value="pending">Bekliyor</option></select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Kapora</label>
            <input type="number" step="0.01" name="deposit_amount" class="form-control" value="0">
        </div>
        <div class="col-md-4 form-check mt-4">
            <input type="checkbox" name="payment_required" value="1" class="form-check-input" id="payReq">
            <label class="form-check-label" for="payReq">Ödeme gerekli</label>
        </div>
        <div class="col-12"><label class="form-label">Not</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
        <div class="col-12">
            <div class="form-check form-check-inline"><input type="checkbox" name="notify_mail" value="1" class="form-check-input" checked><label class="form-check-label">Mail</label></div>
            <div class="form-check form-check-inline"><input type="checkbox" name="notify_sms" value="1" class="form-check-input" checked><label class="form-check-label">SMS</label></div>
            <div class="form-check form-check-inline"><input type="checkbox" name="notify_whatsapp" value="1" class="form-check-input"><label class="form-check-label">WhatsApp</label></div>
        </div>
        <div class="col-12"><button class="btn btn-primary">Randevu Oluştur</button></div>
    </div>
</form>
</div>
<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
