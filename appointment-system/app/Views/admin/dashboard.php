<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card p-3">
            <small class="text-muted">Toplam Randevu</small>
            <div class="stat-value"><?= number_format($stats['appointments']) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3">
            <small class="text-muted">Aktif Paketler</small>
            <div class="stat-value"><?= number_format($stats['active_packages']) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3">
            <small class="text-muted">Aylık Gelir</small>
            <div class="stat-value"><?= format_money($stats['revenue']) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3">
            <small class="text-muted">Yeni Müşteriler</small>
            <div class="stat-value"><?= number_format($stats['new_customers']) ?></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="table-card p-3">
            <h6 class="fw-semibold mb-3">Gelecek Randevular</h6>
            <table class="table table-hover">
                <thead><tr><th>Müşteri</th><th>Hizmet</th><th>Tarih</th><th>Durum</th></tr></thead>
                <tbody>
                <?php foreach ($upcoming as $a): ?>
                <tr>
                    <td><?= e($a['customer_name']) ?></td>
                    <td><?= e($a['service_name']) ?></td>
                    <td><?= format_date($a['appointment_date']) ?> <?= format_time($a['start_time']) ?></td>
                    <td><span class="badge bg-primary"><?= e($a['status']) ?></span></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <a href="<?= admin_url('?route=appointments') ?>">Tüm randevular</a>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="table-card p-3">
            <h6 class="fw-semibold mb-3">Son Ödemeler</h6>
            <?php foreach ($recent_payments as $p): ?>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span>#<?= (int) $p['id'] ?></span>
                <strong class="text-success">+<?= format_money((float) $p['amount']) ?></strong>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
