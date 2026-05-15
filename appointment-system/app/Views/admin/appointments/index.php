<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>
<div class="table-card p-3 mb-3">
<table class="table"><thead><tr><th>Müşteri</th><th>Hizmet</th><th>Tarih</th><th>Durum</th><th>İşlem</th></tr></thead>
<tbody><?php foreach ($appointments['data'] as $a): ?>
<tr><td><?= e($a['customer_name']) ?></td><td><?= e($a['service_name']) ?></td>
<td><?= format_date($a['appointment_date']) ?> <?= format_time($a['start_time']) ?></td>
<td><?= e($a['status']) ?></td>
<td><form method="post" action="<?= admin_url('?route=appointments/status') ?>" class="d-inline"><?= csrf_field() ?>
<input type="hidden" name="id" value="<?= $a['id'] ?>">
<select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
<?php foreach (['pending','approved','cancelled','completed','no_show'] as $st): ?>
<option value="<?= $st ?>" <?= $a['status']===$st?'selected':'' ?>><?= $st ?></option>
<?php endforeach; ?></select></form></td></tr>
<?php endforeach; ?></tbody></table></div>
<div class="card p-3"><h6>Yeni Randevu (Admin)</h6>
<form method="post" action="<?= admin_url('?route=appointments/save') ?>"><?= csrf_field() ?>
<div class="row g-2">
<div class="col-md-3"><select name="customer_id" class="form-select" required><?php foreach ($customers as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['first_name'].' '.$c['last_name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><select name="service_id" class="form-select" required><?php foreach ($services as $s): ?><option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><select name="staff_id" class="form-select"><option value="">Personel</option><?php foreach ($staff as $m): ?><option value="<?= $m['id'] ?>"><?= e($m['name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><input type="date" name="appointment_date" class="form-control" required></div>
<div class="col-md-2"><input type="time" name="start_time" class="form-control" required></div>
<div class="col-12"><button class="btn btn-primary">Oluştur</button></div></div></form></div>
<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
