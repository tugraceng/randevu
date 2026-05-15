<?php require APP_PATH . '/Views/customer/partials/header.php'; ?>
<h6>Aktif Paketlerim</h6>
<?php foreach ($my_packages as $p): ?><div class="card p-3 mb-2"><strong><?= e($p['package_name']) ?></strong> — <?= (int)$p['remaining_sessions'] ?> seans kaldı</div><?php endforeach; ?>
<h6 class="mt-4">Satın Alınabilir Paketler</h6>
<?php foreach ($available as $pkg): ?>
<form method="post" action="<?= customer_url('?route=packages/buy') ?>" class="card p-3 mb-2 d-flex flex-row justify-content-between align-items-center">
<?= csrf_field() ?><input type="hidden" name="package_id" value="<?= $pkg['id'] ?>">
<div><strong><?= e($pkg['name']) ?></strong><br><small><?= (int)$pkg['session_count'] ?> seans</small></div>
<div><span class="me-3"><?= format_money((float)$pkg['price']) ?></span><button class="btn btn-primary btn-sm">Satın Al</button></div>
</form>
<?php endforeach; ?>
<?php require APP_PATH . '/Views/customer/partials/footer.php'; ?>
