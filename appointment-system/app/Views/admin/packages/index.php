<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>
<div class="table-card p-3 mb-3"><table class="table"><thead><tr><th>Paket</th><th>Hizmet</th><th>Seans</th><th>Fiyat</th><th>Süre</th></tr></thead>
<tbody><?php foreach ($packages as $p): ?><tr><td><?= e($p['name']) ?></td><td><?= e($p['service_name']) ?></td><td><?= (int)$p['session_count'] ?></td><td><?= format_money((float)$p['price']) ?></td><td><?= (int)$p['validity_days'] ?> gün</td></tr><?php endforeach; ?></tbody></table></div>
<div class="card p-3"><form method="post" action="<?= admin_url('?route=packages/save') ?>"><?= csrf_field() ?>
<div class="row g-2"><div class="col-md-3"><select name="service_id" class="form-select"><?php foreach ($services as $s): ?><option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><input name="name" class="form-control" placeholder="Paket adı" required></div>
<div class="col-md-2"><input name="session_count" type="number" class="form-control" placeholder="Seans" required></div>
<div class="col-md-2"><input name="price" type="number" step="0.01" class="form-control" placeholder="Fiyat" required></div>
<div class="col-md-2"><button class="btn btn-primary w-100">Ekle</button></div></div></form></div>
<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
