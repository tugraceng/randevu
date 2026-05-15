<?php require APP_PATH . '/Views/admin/partials/header.php'; $a = $appointment; ?>
<div class="table-card p-4 col-lg-10">
<form method="post" action="<?= admin_url('?route=appointments/update') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $a['id'] ?>">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Müşteri</label>
            <select name="customer_id" class="form-select" required>
                <?php foreach ($customers as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $c['id'] == $a['customer_id'] ? 'selected' : '' ?>><?= e($c['first_name'].' '.$c['last_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Hizmet</label>
            <select name="service_id" id="service_id" class="form-select" required>
                <?php foreach ($services as $s): ?>
                <option value="<?= $s['id'] ?>" <?= $s['id'] == $a['service_id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Personel</label>
            <select name="staff_id" id="staff_id" class="form-select">
                <option value="">Farketmez</option>
                <?php foreach ($staff as $st): ?>
                <option value="<?= $st['id'] ?>" <?= $st['id'] == $a['staff_id'] ? 'selected' : '' ?>><?= e($st['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3"><label class="form-label">Tarih</label>
            <input type="date" name="appointment_date" id="appointment_date" class="form-control" value="<?= e($a['appointment_date']) ?>" required></div>
        <div class="col-md-3"><label class="form-label">Saat</label>
            <input type="time" name="start_time" id="start_time" class="form-control" value="<?= e(substr($a['start_time'], 0, 5)) ?>" required>
            <div id="slots-container" class="border rounded p-1 mt-1 small"></div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Durum</label>
            <select name="status" class="form-select">
                <?php foreach (['pending','approved','completed','cancelled','no_show'] as $st): ?>
                <option value="<?= $st ?>" <?= $a['status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12"><label class="form-label">Not</label><textarea name="notes" class="form-control" rows="2"><?= e($a['notes'] ?? '') ?></textarea></div>
        <div class="col-12"><button class="btn btn-primary">Güncelle</button></div>
    </div>
</form>
</div>
<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
