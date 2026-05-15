<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>
<div class="row g-4">
<?php foreach ($staff as $m): ?>
<div class="col-md-4"><div class="card p-3"><h6><?= e($m['name']) ?></h6><p class="text-muted small mb-0"><?= e($m['title'] ?? '') ?></p></div></div>
<?php endforeach; ?>
</div>
<div class="card p-3 mt-3"><form method="post" action="<?= admin_url('?route=staff/save') ?>"><?= csrf_field() ?>
<input class="form-control mb-2" name="name" placeholder="Ad" required>
<input class="form-control mb-2" name="title" placeholder="Ünvan">
<button class="btn btn-primary">Personel Ekle</button></form></div>
<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
