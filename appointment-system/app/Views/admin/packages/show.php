<?php
require APP_PATH . '/Views/admin/partials/header.php';
$p = $package;
$total = (int)($p['total_sessions'] ?? 0);
$rem   = (int)($p['remaining_sessions'] ?? 0);
$used  = max(0, $total - $rem);
$pct   = $total ? round($used / $total * 100) : 0;
$progressTone = $rem <= 1 ? 'danger' : ($rem <= 3 ? 'warning' : '');
?>

<div class="section-title-bar">
    <div>
        <h5>Paket Detayı</h5>
        <small class="text-muted"><?= e($p['package_name']) ?> · <?= e($p['customer_name']) ?></small>
    </div>
    <a href="<?= admin_url('?route=customers/show&id=' . (int)($p['customer_id'] ?? 0)) ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Müşteri sayfasına dön</a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="panel mb-4">
            <div class="panel-header">
                <h6><?= e($p['package_name']) ?></h6>
                <div class="d-flex gap-1">
                    <?= status_badge($p['status']) ?>
                    <?= status_badge($p['payment_status']) ?>
                </div>
            </div>
            <div class="panel-body">
                <div class="row g-3 mb-3">
                    <div class="col-sm-6"><small class="text-muted text-uppercase">Müşteri</small><div><strong><?= e($p['customer_name']) ?></strong></div></div>
                    <div class="col-sm-6"><small class="text-muted text-uppercase">Hizmet</small><div><strong><?= e($p['service_name']) ?></strong></div></div>
                    <div class="col-sm-6"><small class="text-muted text-uppercase">Başlangıç</small><div><strong><?= format_date($p['purchased_at'] ?? $p['created_at'] ?? 'now') ?></strong></div></div>
                    <div class="col-sm-6"><small class="text-muted text-uppercase">Bitiş</small><div><strong><?= !empty($p['expiry_date']) ? format_date($p['expiry_date']) : '—' ?></strong></div></div>
                </div>

                <div class="surface-soft p-3 rounded">
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <strong class="text-primary"><?= $rem ?></strong> seans kaldı
                            <small class="text-muted">/ Toplam <?= $total ?></small>
                        </div>
                        <span class="badge bg-light text-dark"><?= $pct ?>% kullanıldı</span>
                    </div>
                    <div class="session-progress <?= $progressTone ?>" style="height: 12px;">
                        <div class="bar" style="width: <?= $pct ?>%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 small text-muted">
                        <span><i class="bi bi-check-circle me-1"></i>Kullanılan: <?= $used ?></span>
                        <span><i class="bi bi-hourglass me-1"></i>Kalan: <?= $rem ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header"><h6><i class="bi bi-sliders me-1"></i> Manuel Seans Düzenleme</h6></div>
            <form method="post" action="<?= admin_url('?route=packages/session') ?>" class="panel-body">
                <?= csrf_field() ?>
                <input type="hidden" name="customer_package_id" value="<?= (int)$p['id'] ?>">
                <div class="row g-2">
                    <div class="col-md-3"><label class="form-label">Değişim (+/-)</label><input type="number" name="delta" class="form-control" value="1" required></div>
                    <div class="col-md-7"><label class="form-label">Açıklama</label><input name="note" class="form-control" placeholder="Örn: hediye seans"></div>
                    <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">Uygula</button></div>
                </div>
                <small class="text-muted d-block mt-2"><i class="bi bi-info-circle me-1"></i> Pozitif değer eklenir, negatif değer çıkarılır. Tüm değişiklikler loglanır.</small>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="panel mb-3">
            <div class="panel-header"><h6><i class="bi bi-clock-history me-1"></i> Seans Logları</h6></div>
            <div class="panel-body">
                <div class="timeline">
                    <?php foreach ($logs as $log): ?>
                    <div class="timeline-item">
                        <div class="time"><?= format_date($log['created_at']) ?></div>
                        <strong><?= e($log['action']) ?></strong>
                        <span class="badge bg-light text-dark ms-1"><?= (int)$log['session_count'] ?></span>
                        <?php if (!empty($log['note'])): ?>
                        <div class="small text-muted"><?= e($log['note']) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($logs)): ?>
                    <div class="empty-state py-3"><div class="icon"><i class="bi bi-journal"></i></div><h6>Log yok</h6></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header"><h6><i class="bi bi-calendar-check me-1"></i> Bağlı Randevular</h6></div>
            <div class="panel-body">
                <?php foreach ($appointments as $a): ?>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <a href="<?= admin_url('?route=appointments/show&id=' . (int)$a['id']) ?>" class="text-decoration-none">
                            <strong><?= format_date($a['appointment_date']) ?></strong>
                        </a>
                        <small class="text-muted d-block"><?= e($a['service_name']) ?></small>
                    </div>
                    <?= status_badge($a['status']) ?>
                </div>
                <?php endforeach; ?>
                <?php if (empty($appointments)): ?>
                <p class="text-muted text-center small my-3">Bu pakete bağlı randevu yok</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
