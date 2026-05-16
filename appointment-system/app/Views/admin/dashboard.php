<?php require APP_PATH . '/Views/admin/partials/header.php';

$adminUser = admin_user();
$adminName = $adminUser['name'] ?? $adminUser['username'] ?? 'Admin';
$hour = (int) date('H');
$greet = $hour < 6 ? 'İyi geceler' : ($hour < 12 ? 'Günaydın' : ($hour < 18 ? 'İyi günler' : 'İyi akşamlar'));

$kpis = [
    ['label' => 'Bugünkü Randevu',   'value' => (int)($stats['today'] ?? 0),            'icon' => 'bi-calendar-day',         'tone' => '',          'trend' => '+12%', 'up' => true],
    ['label' => 'Bekleyen',          'value' => (int)($stats['pending'] ?? 0),          'icon' => 'bi-hourglass-split',      'tone' => 'k-warning', 'trend' => null,   'up' => true],
    ['label' => 'Onaylanan',         'value' => (int)($stats['approved'] ?? 0),         'icon' => 'bi-check2-circle',        'tone' => '',          'trend' => null,   'up' => true],
    ['label' => 'Tamamlanan',        'value' => (int)($stats['completed'] ?? 0),        'icon' => 'bi-check-all',            'tone' => 'k-success', 'trend' => '+8%',  'up' => true],
    ['label' => 'İptal Edilen',      'value' => (int)($stats['cancelled'] ?? 0),        'icon' => 'bi-x-circle',             'tone' => 'k-danger',  'trend' => '-3%',  'up' => false],
    ['label' => 'Toplam Müşteri',    'value' => (int)($stats['customers'] ?? 0),        'icon' => 'bi-person-badge',         'tone' => 'k-info',    'trend' => '+24',  'up' => true],
    ['label' => 'Aktif Paket',       'value' => (int)($stats['active_packages'] ?? 0),  'icon' => 'bi-box-seam',             'tone' => '',          'trend' => null,   'up' => true],
    ['label' => 'Kalan Seans Uyarı', 'value' => (int)($stats['critical_sessions'] ?? 0),'icon' => 'bi-exclamation-triangle', 'tone' => 'k-warning', 'trend' => null,   'up' => true],
    ['label' => 'Aylık Gelir',       'value' => format_money((float)($stats['revenue'] ?? 0)), 'icon' => 'bi-cash-stack', 'tone' => 'k-success', 'trend' => '+18%', 'up' => true],
    ['label' => 'Ödeme Bekleyen',    'value' => (int)($stats['pending_payments'] ?? 0), 'icon' => 'bi-credit-card-2-back',   'tone' => 'k-danger',  'trend' => null,   'up' => false],
    ['label' => 'SMS (Ay)',          'value' => (int)($stats['sms_sent'] ?? 0),         'icon' => 'bi-chat-text',            'tone' => 'k-info',    'trend' => null,   'up' => true],
    ['label' => 'WhatsApp (Ay)',     'value' => (int)($stats['whatsapp_sent'] ?? 0),    'icon' => 'bi-whatsapp',             'tone' => 'k-success', 'trend' => null,   'up' => true],
];
?>

<div class="dash-hero" data-reveal>
    <div class="dash-hero__left">
        <span class="dash-hero__eyebrow"><?= e(date('l, d F Y', strtotime(date('Y-m-d')))) ?></span>
        <h2 class="dash-hero__title"><?= e($greet) ?>, <?= e($adminName) ?> 👋</h2>
        <p class="dash-hero__sub">Bugün için <strong><?= (int)($stats['today'] ?? 0) ?></strong> randevu planlanmış, <strong><?= (int)($stats['pending'] ?? 0) ?></strong> tanesi onay bekliyor.</p>
    </div>
    <div class="dash-hero__right">
        <a href="<?= admin_url('?route=appointments/create') ?>" class="btn-pro btn-pro--primary btn-pro--lg ripple">
            <i class="bi bi-calendar-plus"></i> Yeni Randevu
        </a>
        <a href="<?= admin_url('?route=appointments/calendar') ?>" class="btn-pro btn-pro--ghost btn-pro--lg">
            <i class="bi bi-calendar3"></i> Takvim
        </a>
    </div>
</div>

<?php if (!empty($stats['pending']) && (int)$stats['pending'] > 0): ?>
<div class="alert-pro alert-pro--warn mb-4" data-reveal>
    <span class="alert-pro__icon"><i class="bi bi-bell"></i></span>
    <div class="alert-pro__body flex-grow-1">
        <strong>Onay bekleyen <?= (int)$stats['pending'] ?> randevu var</strong>
        <small>Müşteri deneyimi için randevuları zamanında onaylayın.</small>
    </div>
    <a href="<?= admin_url('?route=appointments&status=pending') ?>" class="btn-pro btn-pro--soft btn-pro--sm align-self-center">İncele</a>
</div>
<?php endif; ?>

<?php if (!empty($stats['critical_sessions']) && (int)$stats['critical_sessions'] > 0): ?>
<div class="alert-pro alert-pro--info mb-4" data-reveal>
    <span class="alert-pro__icon"><i class="bi bi-box-seam"></i></span>
    <div class="alert-pro__body flex-grow-1">
        <strong><?= (int)$stats['critical_sessions'] ?> paketin seansı azalıyor</strong>
        <small>Yenileme önerisi gönderme zamanı — kampanya/şablon mesajı tetikleyin.</small>
    </div>
    <a href="<?= admin_url('?route=packages') ?>" class="btn-pro btn-pro--soft btn-pro--sm align-self-center">Paketler</a>
</div>
<?php endif; ?>

<div class="kpi-grid">
    <?php foreach ($kpis as $kpi): $isInt = is_int($kpi['value']); ?>
    <div class="kpi-card <?= e($kpi['tone']) ?>">
        <div class="kpi-head">
            <span class="kpi-ic"><i class="bi <?= e($kpi['icon']) ?>"></i></span>
            <?php if (!empty($kpi['trend'])): ?>
                <span class="trend <?= $kpi['up'] ? 'up' : 'down' ?>">
                    <i class="bi <?= $kpi['up'] ? 'bi-arrow-up-right' : 'bi-arrow-down-right' ?>"></i> <?= e($kpi['trend']) ?>
                </span>
            <?php endif; ?>
        </div>
        <div class="label"><?= e($kpi['label']) ?></div>
        <div class="value"<?= $isInt ? ' data-counter="' . (int)$kpi['value'] . '"' : '' ?>>
            <?= $isInt ? '0' : e($kpi['value']) ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="chart-card" style="min-height: 320px;">
            <div class="head">
                <div>
                    <h6>Haftalık Randevu Trendi</h6>
                    <small class="text-muted">Son 7 günün randevu sayıları</small>
                </div>
                <a href="<?= admin_url('?route=appointments') ?>" class="btn btn-light btn-sm">Tümü</a>
            </div>
            <div style="height: 240px;"><canvas id="chartWeekly"></canvas></div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="chart-card h-100">
            <div class="head"><h6>Hizmet Dağılımı</h6></div>
            <div style="height: 240px;"><canvas id="chartService"></canvas></div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="chart-card h-100">
            <div class="head">
                <div>
                    <h6>Personel Performansı</h6>
                    <small class="text-muted">Bu ay tamamlanan randevular</small>
                </div>
            </div>
            <div style="height: 240px;"><canvas id="chartStaff"></canvas></div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="chart-card h-100">
            <div class="head"><h6>Ödeme Durumu</h6></div>
            <div style="height: 240px;"><canvas id="chartPayment"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="panel">
            <div class="panel-header">
                <h6>Gelecek Randevular</h6>
                <a href="<?= admin_url('?route=appointments') ?>" class="small">Tümünü gör <i class="bi bi-arrow-right"></i></a>
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
                        <?php foreach ($upcoming as $a):
                            $initial = strtoupper(mb_substr($a['customer_name'] ?? '?', 0, 1));
                        ?>
                        <tr>
                            <td>
                                <div class="table-avatar">
                                    <span class="avatar"><?= e($initial) ?></span>
                                    <div>
                                        <strong><?= e($a['customer_name']) ?></strong>
                                        <small><?= e($a['customer_phone'] ?? '') ?></small>
                                    </div>
                                </div>
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
                            <td><span class="status-pill status-<?= e($a['status']) ?>"><?= e($a['status']) ?></span></td>
                            <td class="text-end">
                                <a href="<?= admin_url('?route=appointments/show&id=' . (int)$a['id']) ?>" class="btn btn-icon" title="Detay">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($upcoming)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="icon"><i class="bi bi-calendar-x"></i></div>
                                    <h6>Gelecek randevu yok</h6>
                                    <p>Henüz planlanmış bir randevu bulunmuyor.</p>
                                    <a href="<?= admin_url('?route=appointments/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus me-1"></i> Yeni randevu ekle</a>
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
        <div class="panel mb-3">
            <div class="panel-header">
                <h6>Son Ödemeler</h6>
                <a href="<?= admin_url('?route=payments') ?>" class="small">Tümü</a>
            </div>
            <div class="panel-body divider-y">
                <?php foreach ($recent_payments as $p): ?>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>#<?= (int)$p['id'] ?></strong>
                        <small class="text-muted d-block"><?= e($p['customer_name'] ?? '—') ?></small>
                    </div>
                    <div class="text-end">
                        <strong class="text-primary"><?= format_money((float)$p['amount']) ?></strong>
                        <small class="text-muted d-block">
                            <span class="status-pill status-<?= e($p['status']) ?>"><?= e($p['status']) ?></span>
                        </small>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($recent_payments)): ?>
                <div class="empty-state" style="padding:1.5rem 0;">
                    <div class="icon" style="width:50px;height:50px;font-size:1.4rem;"><i class="bi bi-credit-card"></i></div>
                    <h6>Henüz ödeme yok</h6>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header"><h6>Hızlı İşlemler</h6></div>
            <div class="panel-body d-flex flex-column gap-2">
                <a href="<?= admin_url('?route=appointments/create') ?>" class="btn btn-primary"><i class="bi bi-calendar-plus me-1"></i> Yeni Randevu</a>
                <a href="<?= admin_url('?route=customers') ?>" class="btn btn-outline-primary"><i class="bi bi-person-plus me-1"></i> Müşteri Yönetimi</a>
                <a href="<?= admin_url('?route=packages') ?>" class="btn btn-light"><i class="bi bi-box me-1"></i> Paketler</a>
                <a href="<?= admin_url('?route=reports') ?>" class="btn btn-light"><i class="bi bi-graph-up me-1"></i> Raporları Aç</a>
            </div>
        </div>
    </div>
</div>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
