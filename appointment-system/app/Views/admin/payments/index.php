<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>
<div class="table-card p-3"><table class="table"><thead><tr><th>#</th><th>Müşteri</th><th>Tutar</th><th>Sağlayıcı</th><th>Durum</th><th>Tarih</th></tr></thead>
<tbody><?php foreach ($payments as $p): ?><tr><td><?= (int)$p['id'] ?></td><td><?= e($p['customer_name']) ?></td><td><?= format_money((float)$p['amount']) ?></td><td><?= e($p['provider']) ?></td><td><?= e($p['status']) ?></td><td><?= e($p['created_at']) ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
