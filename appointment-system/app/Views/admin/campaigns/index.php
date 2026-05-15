<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>
<?php foreach ($campaigns as $c): ?><div class="card p-3 mb-2"><strong><?= e($c['title']) ?></strong><p class="small mb-0"><?= e($c['description'] ?? '') ?></p></div><?php endforeach; ?>
<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
