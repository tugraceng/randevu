<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>
<div class="row g-4">
<div class="col-md-6"><div class="card p-3"><h6>Aylık Gelir</h6><ul class="list-group list-group-flush"><?php foreach ($revenue as $r): ?>
<li class="list-group-item d-flex justify-content-between"><span><?= e($r['month']) ?></span><strong><?= format_money((float)$r['total']) ?></strong></li>
<?php endforeach; ?></ul></div></div>
<div class="col-md-6"><div class="card p-3"><h6>Randevu Durumları</h6>
<?php foreach ($appointments as $a): ?><p class="mb-1"><?= e($a['status']) ?>: <strong><?= (int)$a['cnt'] ?></strong></p><?php endforeach; ?>
</div></div>
<div class="col-12"><div class="card p-3"><h6>Sistem Logları</h6><table class="table table-sm"><tbody>
<?php foreach ($logs as $log): ?><tr><td><?= e($log['created_at']) ?></td><td><?= e($log['action']) ?></td><td><?= e($log['description'] ?? '') ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
