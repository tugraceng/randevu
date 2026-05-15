<?php require APP_PATH . '/Views/customer/partials/header.php';
$upcoming = array_filter($appointments, fn($a) => in_array($a['status'], ['pending','approved'], true) && $a['appointment_date'] >= date('Y-m-d'));
$pendingPay = array_filter($payments, fn($p) => $p['status'] === 'pending');
?>
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <small class="text-muted">Yaklaşan Randevu</small>
            <?php if ($upcoming): $u = reset($upcoming); ?>
            <h5 class="mt-2 mb-0"><?= e($u['service_name']) ?></h5>
            <p class="text-primary mb-0"><?= format_date($u['appointment_date']) ?> · <?= format_time($u['start_time']) ?></p>
            <?php else: ?><p class="mb-0 text-muted">Yaklaşan randevu yok</p><?php endif; ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <small class="text-muted">Aktif Paketler</small>
            <h3 class="mt-2 mb-0"><?= count($packages) ?></h3>
            <small><?= array_sum(array_column($packages, 'remaining_sessions')) ?> toplam kalan seans</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <small class="text-muted">Ödeme Bekleyen</small>
            <h3 class="mt-2 mb-0"><?= count($pendingPay) ?></h3>
            <a href="<?= customer_url('?route=payments') ?>" class="small">Ödemelere git</a>
        </div>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-3 mb-3 shadow-sm border-0"><h6 class="fw-semibold">Son Randevular</h6>
        <ul class="list-group list-group-flush"><?php foreach ($appointments as $a): ?>
        <li class="list-group-item d-flex justify-content-between px-0">
            <span><?= e($a['service_name']) ?> — <?= format_date($a['appointment_date']) ?> <?= format_time($a['start_time']) ?></span>
            <span class="badge bg-primary"><?= e($a['status']) ?></span>
        </li><?php endforeach; ?></ul>
        <a href="<?= customer_url('?route=appointments/create') ?>" class="btn btn-primary mt-3">Yeni Randevu Al</a>
        </div>
        <div class="card p-3 shadow-sm border-0"><h6 class="fw-semibold">Ödeme Geçmişi</h6>
        <table class="table table-sm mb-0"><thead><tr><th>Tarih</th><th>Tutar</th><th>Durum</th></tr></thead>
        <tbody><?php foreach ($payments as $p): ?><tr>
        <td><?= format_date($p['paid_at'] ?? $p['created_at']) ?></td>
        <td><?= format_money((float)$p['amount']) ?></td>
        <td><span class="badge bg-<?= $p['status']==='paid'?'success':'warning' ?>"><?= e($p['status']) ?></span></td>
        </tr><?php endforeach; ?></tbody></table></div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4 shadow-sm border-0 mb-3" style="background:linear-gradient(135deg,#4f46e5,#0ea5e9);color:#fff">
            <h5 class="text-white">Paketlerim</h5>
            <?php foreach ($packages as $pkg): ?>
            <div class="mb-2 pb-2 border-bottom border-white border-opacity-25">
                <strong><?= e($pkg['package_name']) ?></strong><br>
                <small><?= (int)$pkg['remaining_sessions'] ?> seans kaldı</small>
            </div>
            <?php endforeach; ?>
            <?php if (empty($packages)): ?><p class="small mb-0 opacity-75">Aktif paketiniz yok.</p><?php endif; ?>
        </div>
        <a href="<?= customer_url('?route=packages') ?>" class="btn btn-outline-primary w-100">Paket Satın Al</a>
    </div>
</div>
<?php require APP_PATH . '/Views/customer/partials/footer.php'; ?>
