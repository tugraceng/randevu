<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>
<div class="row g-4">
<div class="col-lg-7">
<?php foreach ($templates as $tpl): if (($channel ?? '') && $tpl['channel'] !== ($_GET['channel'] ?? 'sms') && !isset($_GET['all'])) continue; ?>
<div class="card p-3 mb-3">
<form method="post" action="<?= admin_url('?route=messages/save') ?>"><?= csrf_field() ?>
<input type="hidden" name="id" value="<?= $tpl['id'] ?>">
<span class="badge bg-secondary"><?= e($tpl['channel']) ?></span> <strong><?= e($tpl['title']) ?></strong>
<textarea id="template_body" name="body" class="form-control mt-2" rows="3"><?= e($tpl['body']) ?></textarea>
<div class="mt-2">
<?php foreach (['{name}','{date}','{time}','{service}','{staff}','{link}'] as $v): ?>
<button type="button" class="btn btn-sm btn-outline-secondary" data-template-var="<?= e($v) ?>"><?= e($v) ?></button>
<?php endforeach; ?>
</div>
<button class="btn btn-primary btn-sm mt-2">Şablonu Kaydet</button>
</form></div>
<?php endforeach; ?>
</div>
<div class="col-lg-5"><div class="card p-3 bg-dark text-white"><h6>Önizleme</h6>
<p class="small">Merhaba Ahmet Bey, 15.05.2026 tarihinde saat 10:00 için olan randevunuz onaylanmıştır.</p></div></div>
</div>
<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
