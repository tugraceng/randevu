<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>
<form method="post" action="<?= admin_url('?route=content/save') ?>"><?= csrf_field() ?>
<?php foreach ($sections as $sec): ?>
<div class="card p-3 mb-3"><h6><?= e($sec['section_key']) ?></h6>
<input type="hidden" name="sections[<?= e($sec['section_key']) ?>][sort_order]" value="<?= (int)$sec['sort_order'] ?>">
<input class="form-control mb-2" name="sections[<?= e($sec['section_key']) ?>][title]" value="<?= e($sec['title'] ?? '') ?>" placeholder="Başlık">
<input class="form-control mb-2" name="sections[<?= e($sec['section_key']) ?>][subtitle]" value="<?= e($sec['subtitle'] ?? '') ?>" placeholder="Alt başlık">
<textarea class="form-control" name="sections[<?= e($sec['section_key']) ?>][content]" rows="3"><?= e($sec['content'] ?? '') ?></textarea>
</div>
<?php endforeach; ?>
<button class="btn btn-primary">Kaydet</button></form>
<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
