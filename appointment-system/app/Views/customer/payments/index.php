<?php require APP_PATH . '/Views/customer/partials/header.php'; ?>
<table class="table card"><thead><tr><th>Tarih</th><th>Açıklama</th><th>Tutar</th><th>Durum</th></tr></thead>
<tbody><?php foreach ($payments as $p): ?><tr>
<td><?= format_date($p['paid_at'] ?? $p['created_at']) ?></td>
<td><?= e($p['package_name'] ?? $p['service_name'] ?? $p['payment_type']) ?></td>
<td><?= format_money((float)$p['amount']) ?></td>
<td><?= e($p['status']) ?></td></tr><?php endforeach; ?></tbody></table>
<?php require APP_PATH . '/Views/customer/partials/footer.php'; ?>
