<?php require APP_PATH . '/Views/customer/partials/header.php'; ?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-3 mb-3"><h6>Ödeme Geçmişi</h6>
        <table class="table table-sm"><thead><tr><th>Tarih</th><th>Hizmet</th><th>Tutar</th><th>Durum</th></tr></thead>
        <tbody><?php foreach ($payments as $p): ?><tr>
        <td><?= format_date($p['paid_at'] ?? $p['created_at']) ?></td>
        <td><?= e($p['package_name'] ?? $p['service_name'] ?? 'Ödeme') ?></td>
        <td><?= format_money((float)$p['amount']) ?></td>
        <td><span class="badge bg-<?= $p['status']==='paid'?'success':'warning' ?>"><?= e(strtoupper($p['status'])) ?></span></td>
        </tr><?php endforeach; ?></tbody></table></div>
        <div class="card p-3"><h6>Son Randevular</h6>
        <ul class="list-group list-group-flush"><?php foreach ($appointments as $a): ?>
        <li class="list-group-item d-flex justify-content-between"><span><?= e($a['service_name']) ?> - <?= format_date($a['appointment_date']) ?></span><span class="badge bg-primary"><?= e($a['status']) ?></span></li>
        <?php endforeach; ?></ul></div>
    </div>
    <div class="col-lg-4">
        <div class="loyalty-card p-4 mb-3"><h5>Sadakat Puanı</h5><p class="display-6 mb-0"><?= count($packages) * 50 ?> XP</p><small>Aktif paketlerinizden kazanırsınız.</small></div>
        <div class="card p-3"><h6>Aktif Paketler</h6>
        <?php foreach ($packages as $pkg): ?>
        <div class="mb-2"><strong><?= e($pkg['package_name']) ?></strong><br><small><?= (int)$pkg['remaining_sessions'] ?> seans kaldı</small></div>
        <?php endforeach; ?>
        <a href="<?= customer_url('?route=appointments/create') ?>" class="btn btn-primary w-100 mt-2">Randevu Al</a>
        </div>
    </div>
</div>
<?php require APP_PATH . '/Views/customer/partials/footer.php'; ?>
