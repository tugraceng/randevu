<?php
require APP_PATH . '/Views/customer/partials/header.php';
$upcoming = array_filter($appointments, fn($a) => in_array($a['status'], ['pending','approved'], true) && $a['appointment_date'] >= date('Y-m-d'));
$pendingPay = array_filter($payments, fn($p) => $p['status'] === 'pending');
$totalSessions = array_sum(array_column($packages, 'remaining_sessions'));
$user = customer_user();
?>

<?php $hour = (int) date('H'); $greet = $hour < 6 ? 'İyi geceler' : ($hour < 12 ? 'Günaydın' : ($hour < 18 ? 'İyi günler' : 'İyi akşamlar')); ?>
<div class="customer-hero-card mb-4" data-reveal>
    <div class="row align-items-center g-3">
        <div class="col-md-8">
            <small class="d-block opacity-75 mb-1"><?= e(date('l, d F Y', time())) ?></small>
            <h4 class="text-white mb-1"><?= e($greet) ?>, <?= e($user['first_name'] ?? '') ?> 👋</h4>
            <p class="mb-0 opacity-75">
                <?php if ($upcoming): $u = reset($upcoming); ?>
                Yaklaşan randevunuz: <strong class="text-white"><?= e($u['service_name']) ?></strong> · <?= format_date($u['appointment_date']) ?> <?= format_time($u['start_time']) ?>
                <?php else: ?>
                Aktif randevunuz yok — şimdi yeni bir randevu oluşturabilirsiniz.
                <?php endif; ?>
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="<?= customer_url('?route=appointments/create') ?>" class="btn btn-light text-primary fw-semibold ripple">
                <i class="bi bi-calendar-plus me-1"></i> Yeni Randevu Al
            </a>
        </div>
    </div>
</div>

<?php if ($pendingPay): ?>
<div class="alert-pro alert-pro--warn mb-3" data-reveal>
    <span class="alert-pro__icon"><i class="bi bi-credit-card"></i></span>
    <div class="alert-pro__body flex-grow-1">
        <strong>Bekleyen <?= count($pendingPay) ?> ödemeniz var</strong>
        <small>Randevularınızın geçerli kalması için ödemenizi tamamlayın.</small>
    </div>
    <a href="<?= customer_url('?route=payments') ?>" class="btn-pro btn-pro--soft btn-pro--sm align-self-center">Öde</a>
</div>
<?php endif; ?>

<?php if (!empty($user) && empty($user['email_verified_at'])): ?>
<div class="alert-pro alert-pro--info mb-3" data-reveal>
    <span class="alert-pro__icon"><i class="bi bi-envelope-paper"></i></span>
    <div class="alert-pro__body flex-grow-1">
        <strong>E-posta doğrulaması bekliyor</strong>
        <small><?= e($user['email']) ?> adresine gönderilen bağlantıyı kontrol edin.</small>
    </div>
</div>
<?php endif; ?>

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
            <h5><i class="bi bi-box-seam me-1"></i> Paketlerim</h5>
            <small class="opacity-75 d-block mb-3">Aktif paketlerinizdeki kalan seanslar</small>

            <?php if (empty($packages)): ?>
            <div class="loyalty-empty">
                <i class="bi bi-box-seam"></i>
                <p class="mb-0">Aktif paketiniz yok.<br><small>Satın alarak indirimli seans paketlerinden faydalanın.</small></p>
            </div>
            <?php else: ?>
                <?php foreach ($packages as $pkg):
                    $tot  = (int)($pkg['total_sessions'] ?? 0);
                    $rem  = (int)$pkg['remaining_sessions'];
                    $used = max(0, $tot - $rem);
                    $pct  = $tot ? round($used / $tot * 100) : 0;
                    $lowClass = $rem === 0 ? 'is-zero' : ($rem <= 2 ? 'is-low' : '');
                ?>
                <div class="pkg-progress <?= $lowClass ?>" style="background: rgba(255,255,255,.08); border-color: rgba(255,255,255,.18); color: #fff;">
                    <div class="pkg-progress__meta" style="color: rgba(255,255,255,.8);">
                        <span><strong style="color:#fff"><?= e($pkg['package_name']) ?></strong></span>
                        <strong style="color:#fff"><?= $rem ?>/<?= $tot ?></strong>
                    </div>
                    <div class="pkg-bar" style="background: rgba(255,255,255,.18);">
                        <span style="width: <?= $pct ?>%"></span>
                    </div>
                    <small style="color: rgba(255,255,255,.65); font-size: .72rem;">
                        <?php if ($rem === 0): ?>Tükendi · Yenileme önerilir
                        <?php elseif ($rem <= 2): ?>Son <?= $rem ?> seans · Yakında bitiyor
                        <?php else: ?>%<?= $pct ?> kullanıldı
                        <?php endif; ?>
                    </small>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <a href="<?= customer_url('?route=packages') ?>" class="btn-pro btn-pro--soft w-100 justify-content-center ripple"><i class="bi bi-plus-circle"></i> Paket Satın Al</a>
    </div>
</div>

<?php require APP_PATH . '/Views/customer/partials/footer.php'; ?>
