<?php require APP_PATH . '/Views/customer/partials/header.php'; ?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-0">Aktif Paketlerim</h5>
                <small class="text-muted">Mevcut seans paketlerinizin kalan kullanımı</small>
            </div>
        </div>
        <div class="row g-3">
            <?php foreach ($my_packages as $p):
                $tot = (int)($p['total_sessions'] ?? 0);
                $rem = (int)$p['remaining_sessions'];
                $used = max(0, $tot - $rem);
                $pct = $tot ? round($used / $tot * 100) : 0;
            ?>
            <div class="col-md-6">
                <div class="c-card h-100 hover-lift">
                    <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                        <div>
                            <h6 class="mb-1"><?= e($p['package_name']) ?></h6>
                            <small class="text-muted"><?= e($p['service_name'] ?? '') ?></small>
                        </div>
                        <span class="status-pill <?= $rem<=1?'status-cancelled':'status-approved' ?>"><?= $rem ?> seans</span>
                    </div>
                    <div class="session-progress mb-1 mt-3 <?= $rem<=1?'danger':($rem<=3?'warning':'') ?>">
                        <div class="bar" style="width: <?= $pct ?>%;"></div>
                    </div>
                    <div class="d-flex justify-content-between small mt-2">
                        <span class="text-muted"><?= $used ?>/<?= $tot ?> kullanıldı</span>
                        <span class="text-muted">%<?= $pct ?> tamamlandı</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($my_packages)): ?>
            <div class="col-12"><div class="empty-state">
                <div class="icon"><i class="bi bi-box"></i></div>
                <h6>Aktif paketiniz yok</h6>
                <p>Sağdaki listeden uygun paketi seçerek seanslarınızı tasarruflu kullanın.</p>
            </div></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-5">
        <h5 class="mb-2">Satın Alınabilir Paketler</h5>
        <small class="text-muted d-block mb-3">Avantajlı seans paketleri ile tasarruf edin.</small>
        <?php foreach ($available as $pkg): ?>
        <form method="post" action="<?= customer_url('?route=packages/buy') ?>" class="c-card mb-2 hover-lift">
            <?= csrf_field() ?>
            <input type="hidden" name="package_id" value="<?= (int)$pkg['id'] ?>">
            <div class="d-flex justify-content-between align-items-center gap-3">
                <div>
                    <strong><?= e($pkg['name']) ?></strong>
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        <small class="text-muted"><i class="bi bi-stack me-1"></i><?= (int)$pkg['session_count'] ?> seans</small>
                        <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?= (int)$pkg['validity_days'] ?> gün</small>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fw-bold text-primary fs-5 mb-1"><?= format_money((float)$pkg['price']) ?></div>
                    <button class="btn btn-primary btn-sm"><i class="bi bi-cart-plus me-1"></i> Satın Al</button>
                </div>
            </div>
        </form>
        <?php endforeach; ?>
        <?php if (empty($available)): ?>
        <div class="empty-state">
            <div class="icon"><i class="bi bi-cart"></i></div>
            <h6>Şu an satılan paket yok</h6>
            <p>Yeni paketler için kısa bir süre sonra tekrar kontrol edin.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require APP_PATH . '/Views/customer/partials/footer.php'; ?>
