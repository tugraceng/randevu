<?php require APP_PATH . '/Views/admin/partials/header.php';

$kpis = [
    ['label' => 'Bugünkü Randevu',   'value' => (int)($stats['today'] ?? 0),            'icon' => 'bi-calendar-day',         'tone' => ''],
    ['label' => 'Bekleyen',          'value' => (int)($stats['pending'] ?? 0),          'icon' => 'bi-hourglass-split',      'tone' => 'tone-warning'],
    ['label' => 'Onaylanan',         'value' => (int)($stats['approved'] ?? 0),         'icon' => 'bi-check2-circle',        'tone' => ''],
    ['label' => 'Tamamlanan',        'value' => (int)($stats['completed'] ?? 0),        'icon' => 'bi-check-all',            'tone' => 'tone-success'],
    ['label' => 'İptal Edilen',      'value' => (int)($stats['cancelled'] ?? 0),        'icon' => 'bi-x-circle',             'tone' => 'tone-danger'],
    ['label' => 'Toplam Müşteri',    'value' => (int)($stats['customers'] ?? 0),        'icon' => 'bi-person-badge',         'tone' => ''],
    ['label' => 'Aktif Paket',       'value' => (int)($stats['active_packages'] ?? 0),  'icon' => 'bi-box-seam',             'tone' => ''],
    ['label' => 'Kalan Seans Uyarı', 'value' => (int)($stats['critical_sessions'] ?? 0),'icon' => 'bi-exclamation-triangle', 'tone' => 'tone-warning'],
    ['label' => 'Aylık Gelir',       'value' => format_money((float)($stats['revenue'] ?? 0)), 'icon' => 'bi-cash-stack', 'tone' => 'tone-success'],
    ['label' => 'Ödeme Bekleyen',    'value' => (int)($stats['pending_payments'] ?? 0), 'icon' => 'bi-credit-card-2-back',   'tone' => 'tone-danger'],
    ['label' => 'SMS (Ay)',          'value' => (int)($stats['sms_sent'] ?? 0),         'icon' => 'bi-chat-text',            'tone' => 'tone-info'],
    ['label' => 'WhatsApp (Ay)',     'value' => (int)($stats['whatsapp_sent'] ?? 0),    'icon' => 'bi-whatsapp',             'tone' => 'tone-success'],
];
?>

<div id="dashboardCharts"></div>

<div class="row g-3 mb-4">
    <?php foreach ($kpis as $kpi): ?>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="stat-card <?= e($kpi['tone']) ?>">
            <span class="stat-icon"><i class="bi <?= e($kpi['icon']) ?>"></i></span>
            <div class="stat-label"><?= e($kpi['label']) ?></div>
            <div class="stat-value"><?= is_string($kpi['value']) ? $kpi['value'] : number_format($kpi['value']) ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="chart-card" style="min-height: 320px;">
            <div class="chart-head">
                <div>
                    <h6>Haftalık Randevu Trendi</h6>
                    <small>Son 7 günün randevu sayıları</small>
                </div>
                <a href="<?= admin_url('?route=appointments') ?>" class="btn btn-soft btn-sm">Tümü</a>
            </div>
            <div style="height: 240px;"><canvas id="chartDaily"></canvas></div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="chart-card h-100">
            <div class="chart-head">
                <h6>Hizmet Dağılımı</h6>
            </div>
            <div style="height: 240px;"><canvas id="chartService"></canvas></div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="chart-card h-100">
            <div class="chart-head">
                <h6>Personel Performansı</h6>
                <small>Bu ay tamamlanan randevular</small>
            </div>
            <div style="height: 240px;"><canvas id="chartStaff"></canvas></div>
        </div>
    </div>
    <div class="col-xl-3">
        <div class="chart-card h-100">
            <div class="chart-head"><h6>Ödeme Durumu</h6></div>
            <div style="height: 240px;"><canvas id="chartPayment"></canvas></div>
        </div>
    </div>
    <div class="col-xl-3">
        <div class="chart-card h-100">
            <div class="chart-head"><h6>Paket Satış</h6></div>
            <div style="height: 240px;"><canvas id="chartPackage"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="table-card">
            <div class="panel-header">
                <h6>Gelecek Randevular</h6>
                <a href="<?= admin_url('?route=appointments') ?>" class="text-decoration-none small">Tümünü gör <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Müşteri</th>
                            <th>Hizmet</th>
                            <th>Tarih &amp; Saat</th>
                            <th>Durum</th>
                            <th class="text-end">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcoming as $a): ?>
                        <tr>
                            <td>
                                <strong><?= e($a['customer_name']) ?></strong><br>
                                <small class="text-muted"><?= e($a['customer_phone'] ?? '') ?></small>
                            </td>
                            <td>
                                <?= e($a['service_name']) ?>
                                <?php if (!empty($a['staff_name'])): ?>
                                <br><small class="text-muted"><i class="bi bi-person me-1"></i><?= e($a['staff_name']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= format_date($a['appointment_date']) ?><br>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i><?= format_time($a['start_time']) ?></small>
                            </td>
                            <td><?= status_badge($a['status']) ?></td>
                            <td class="text-end">
                                <a href="<?= admin_url('?route=appointments/show&id=' . (int)$a['id']) ?>" class="btn btn-soft btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($upcoming)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state py-4">
                                    <div class="icon"><i class="bi bi-calendar-x"></i></div>
                                    <h6>Gelecek randevu yok</h6>
                                    <p class="mb-0">Henüz planlanmış bir randevu bulunmuyor.</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="table-card mb-3">
            <div class="panel-header">
                <h6>Son Ödemeler</h6>
                <a href="<?= admin_url('?route=payments') ?>" class="text-decoration-none small">Tümü</a>
            </div>
            <div class="panel-body">
                <?php foreach ($recent_payments as $p): ?>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <strong>#<?= (int)$p['id'] ?></strong>
                        <small class="text-muted d-block"><?= e($p['customer_name'] ?? '—') ?></small>
                    </div>
                    <div class="text-end">
                        <strong class="text-success"><?= format_money((float)$p['amount']) ?></strong>
                        <small class="text-muted d-block"><?= e($p['status']) ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($recent_payments)): ?>
                <p class="text-muted text-center small my-3">Henüz ödeme yok</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-card">
            <div class="panel-header">
                <h6>Hızlı İşlemler</h6>
            </div>
            <div class="panel-body d-flex flex-column gap-2">
                <a href="<?= admin_url('?route=appointments/create') ?>" class="btn btn-primary"><i class="bi bi-calendar-plus me-1"></i> Yeni Randevu</a>
                <a href="<?= admin_url('?route=customers') ?>" class="btn btn-soft"><i class="bi bi-person-plus me-1"></i> Müşteri Yönetimi</a>
                <a href="<?= admin_url('?route=packages') ?>" class="btn btn-soft"><i class="bi bi-box me-1"></i> Paketler</a>
                <a href="<?= admin_url('?route=reports') ?>" class="btn btn-soft"><i class="bi bi-graph-up me-1"></i> Raporları Aç</a>
            </div>
        </div>
    </div>
</div>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
