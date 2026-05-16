<?php
require APP_PATH . '/Views/customer/partials/header.php';
$upcoming = array_filter($appointments, fn($a) => in_array($a['status'], ['pending','approved'], true) && $a['appointment_date'] >= date('Y-m-d'));
$pendingPay = array_filter($payments, fn($p) => $p['status'] === 'pending');
$totalSessions = array_sum(array_column($packages, 'remaining_sessions'));
$user = customer_user();
?>

<div class="customer-hero-card mb-4">
    <div class="row align-items-center g-3">
        <div class="col-md-8">
            <h4 class="text-white mb-1">Hoş geldiniz, <?= e($user['first_name'] ?? '') ?></h4>
            <p class="mb-0 opacity-75">Randevularınızı, paketlerinizi ve ödemelerinizi tek bir yerden takip edin.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="<?= customer_url('?route=appointments/create') ?>" class="btn btn-light text-primary fw-semibold">
                <i class="bi bi-calendar-plus me-1"></i> Yeni Randevu Al
            </a>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="c-stat">
            <span class="stat-icon"><i class="bi bi-calendar-check"></i></span>
            <span class="label">Yaklaşan Randevu</span>
            <?php if ($upcoming): $u = reset($upcoming); ?>
            <span class="value h5 mb-0"><?= e($u['service_name']) ?></span>
            <small class="meta"><?= format_date($u['appointment_date']) ?> · <?= format_time($u['start_time']) ?></small>
            <?php else: ?>
            <span class="value h5 mb-0">—</span>
            <small class="meta">Aktif randevu yok</small>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="c-stat">
            <span class="stat-icon"><i class="bi bi-box-seam"></i></span>
            <span class="label">Aktif Paketler</span>
            <span class="value"><?= count($packages) ?></span>
            <small class="meta"><?= (int)$totalSessions ?> toplam kalan seans</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="c-stat">
            <span class="stat-icon"><i class="bi bi-credit-card"></i></span>
            <span class="label">Ödeme Bekleyen</span>
            <span class="value"><?= count($pendingPay) ?></span>
            <a href="<?= customer_url('?route=payments') ?>" class="meta">Ödemelere git →</a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="c-card mb-3">
            <div class="c-card-head">
                <h6><i class="bi bi-clock-history me-1"></i> Son Randevular</h6>
                <a href="<?= customer_url('?route=appointments') ?>" class="small">Tümü</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Hizmet</th><th>Tarih</th><th>Durum</th></tr></thead>
                    <tbody>
                        <?php foreach ($appointments as $a): ?>
                        <tr>
                            <td><strong><?= e($a['service_name']) ?></strong></td>
                            <td><?= format_date($a['appointment_date']) ?> · <small class="text-muted"><?= format_time($a['start_time']) ?></small></td>
                            <td><?= status_badge($a['status']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($appointments)): ?>
                        <tr><td colspan="3">
                            <div class="empty-state">
                                <div class="icon"><i class="bi bi-calendar-x"></i></div>
                                <h6>Henüz randevunuz yok</h6>
                                <p>İlk randevunuzu hemen oluşturabilirsiniz.</p>
                                <a href="<?= customer_url('?route=appointments/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-calendar-plus me-1"></i> Randevu Oluştur</a>
                            </div>
                        </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="c-card">
            <div class="c-card-head">
                <h6><i class="bi bi-cash-coin me-1"></i> Ödeme Geçmişi</h6>
                <a href="<?= customer_url('?route=payments') ?>" class="small">Tümü</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Tarih</th><th>Tutar</th><th>Durum</th></tr></thead>
                    <tbody>
                        <?php foreach (array_slice($payments, 0, 5) as $p): ?>
                        <tr>
                            <td><?= format_date($p['paid_at'] ?? $p['created_at']) ?></td>
                            <td><strong class="text-primary"><?= format_money((float)$p['amount']) ?></strong></td>
                            <td><?= status_badge($p['status']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($payments)): ?>
                        <tr><td colspan="3">
                            <div class="empty-state">
                                <div class="icon"><i class="bi bi-cash"></i></div>
                                <h6>Ödeme yok</h6>
                                <p>Bir paket satın aldığınızda burada görünür.</p>
                            </div>
                        </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="loyalty-card mb-3">
            <h5>Paketlerim</h5>
            <small class="opacity-75 d-block mb-3">Aktif paketlerinizdeki kalan seanslar</small>
            <?php foreach ($packages as $pkg):
                $tot = (int)($pkg['total_sessions'] ?? 0);
                $rem = (int)$pkg['remaining_sessions'];
                $used = max(0, $tot - $rem);
                $pct = $tot ? round($used / $tot * 100) : 0;
            ?>
            <div class="pkg-row">
                <div class="d-flex justify-content-between mb-1">
                    <strong><?= e($pkg['package_name']) ?></strong>
                    <span><?= $rem ?> / <?= $tot ?></span>
                </div>
                <div class="session-progress">
                    <div class="bar" style="width: <?= $pct ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($packages)): ?>
            <p class="small mb-0 opacity-75">Aktif paketiniz yok.</p>
            <?php endif; ?>
        </div>
        <a href="<?= customer_url('?route=packages') ?>" class="btn btn-outline-primary w-100"><i class="bi bi-plus me-1"></i> Paket Satın Al</a>
    </div>
</div>

<?php require APP_PATH . '/Views/customer/partials/footer.php'; ?>
