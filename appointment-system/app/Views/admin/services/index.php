<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>
<div class="d-flex justify-content-between mb-3">
    <p class="text-muted mb-0">Aktif hizmetleri yönetin</p>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#serviceModal">+ Yeni Hizmet</button>
</div>
<div class="table-card"><table class="table mb-0"><thead><tr><th>Hizmet</th><th>Süre</th><th>Fiyat</th><th>Durum</th></tr></thead>
<tbody><?php foreach ($services as $s): ?><tr>
<td><?= e($s['name']) ?></td><td><?= (int)$s['duration_minutes'] ?> dk</td><td><?= format_money((float)$s['price']) ?></td>
<td><span class="badge bg-<?= $s['status']?'success':'secondary' ?>"><?= $s['status']?'Aktif':'Pasif' ?></span></td>
</tr><?php endforeach; ?></tbody></table></div>
<div class="modal fade" id="serviceModal"><div class="modal-dialog"><form method="post" action="<?= admin_url('?route=services/save') ?>" class="modal-content">
<?= csrf_field() ?><div class="modal-header"><h5>Yeni Hizmet</h5></div><div class="modal-body">
<input class="form-control mb-2" name="name" placeholder="Ad" required>
<textarea class="form-control mb-2" name="description" placeholder="Açıklama"></textarea>
<input class="form-control mb-2" name="duration_minutes" type="number" value="30" placeholder="Süre (dk)">
<input class="form-control mb-2" name="price" type="number" step="0.01" placeholder="Fiyat">
<input class="form-control mb-2" name="deposit_price" type="number" step="0.01" placeholder="Kapora">
</div><div class="modal-footer"><button class="btn btn-primary">Kaydet</button></div></form></div></div>
<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
