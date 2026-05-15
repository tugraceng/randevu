<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>

<div id="dashboardCharts" class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card p-3"><small class="text-muted">Bugün</small><div class="stat-value"><?= $stats['today'] ?></div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card stat-warning p-3"><small class="text-muted">Bekleyen</small><div class="stat-value"><?= $stats['pending'] ?></div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card stat-success p-3"><small class="text-muted">Tamamlanan</small><div class="stat-value"><?= $stats['completed'] ?></div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card stat-danger p-3"><small class="text-muted">İptal</small><div class="stat-value"><?= $stats['cancelled'] ?></div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card p-3"><small class="text-muted">Müşteri</small><div class="stat-value"><?= $stats['customers'] ?></div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card p-3"><small class="text-muted">Aktif Paket</small><div class="stat-value"><?= $stats['active_packages'] ?></div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card stat-warning p-3"><small class="text-muted">Seans Uyarı</small><div class="stat-value"><?= $stats['critical_sessions'] ?></div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card p-3"><small class="text-muted">Aylık Gelir</small><div class="stat-value fs-6"><?= format_money($stats['revenue']) ?></div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card p-3"><small class="text-muted">Ödeme Bekleyen</small><div class="stat-value"><?= $stats['pending_payments'] ?></div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card p-3"><small class="text-muted">SMS (ay)</small><div class="stat-value"><?= $stats['sms_sent'] ?></div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card p-3"><small class="text-muted">WhatsApp (ay)</small><div class="stat-value"><?= $stats['whatsapp_sent'] ?></div></div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6"><div class="chart-box"><canvas id="chartDaily" height="200"></canvas></div></div>
    <div class="col-lg-6"><div class="chart-box"><canvas id="chartService" height="200"></canvas></div></div>
    <div class="col-lg-6"><div class="chart-box"><canvas id="chartStaff" height="200"></canvas></div></div>
    <div class="col-lg-3"><div class="chart-box"><canvas id="chartPayment" height="200"></canvas></div></div>
    <div class="col-lg-3"><div class="chart-box"><canvas id="chartPackage" height="200"></canvas></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="table-card p-3">
            <div class="d-flex justify-content-between mb-3">
                <h6 class="fw-semibold mb-0">Gelecek Randevular</h6>
                <a href="<?= admin_url('?route=appointments') ?>">Tümü</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>Müşteri</th><th>Hizmet</th><th>Tarih</th><th>Durum</th></tr></thead>
                    <tbody>
                    <?php foreach ($upcoming as $a): ?>
                    <tr>
                        <td><?= e($a['customer_name']) ?></td>
                        <td><?= e($a['service_name']) ?></td>
                        <td><?= format_date($a['appointment_date']) ?> <?= format_time($a['start_time']) ?></td>
                        <td><?= status_badge($a['status']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($upcoming)): ?><tr><td colspan="4" class="text-muted text-center">Kayıt yok</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="table-card p-3">
            <h6 class="fw-semibold mb-3">Son Ödemeler</h6>
            <?php foreach ($recent_payments as $p): ?>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span>#<?= (int)$p['id'] ?> <?= e($p['customer_name'] ?? '') ?></span>
                <strong class="text-success"><?= format_money((float)$p['amount']) ?></strong>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
