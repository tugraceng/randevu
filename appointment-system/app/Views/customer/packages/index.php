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
                <div class="c-card h-100">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="mb-0"><?= e($p['package_name']) ?></h6>
                        <span class="badge bg-<?= $rem<=1?'danger':'primary' ?>"><?= $rem ?> seans</span>
                    </div>
                    <small class="text-muted d-block mb-3"><?= e($p['service_name'] ?? '') ?></small>
                    <div class="session-progress mb-1" style="background:#eef2f7;">
                        <div class="bar" style="width: <?= $pct ?>%;background:linear-gradient(135deg,var(--c-primary),var(--c-secondary));"></div>
                    </div>
                    <small class="text-muted"><?= $used ?>/<?= $tot ?> kullanıldı</small>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($my_packages)): ?>
            <div class="col-12"><div class="empty-state"><i class="bi bi-box"></i><br>Aktif paketiniz yok</div></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-5">
        <h5 class="mb-2">Satın Alınabilir Paketler</h5>
        <small class="text-muted d-block mb-3">Avantajlı seans paketleri ile tasarruf edin.</small>
        <?php foreach ($available as $pkg): ?>
        <form method="post" action="<?= customer_url('?route=packages/buy') ?>" class="c-card mb-2">
            <?= csrf_field() ?>
            <input type="hidden" name="package_id" value="<?= (int)$pkg['id'] ?>">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= e($pkg['name']) ?></strong><br>
                    <small class="text-muted"><?= (int)$pkg['session_count'] ?> seans · <?= (int)$pkg['validity_days'] ?> gün</small>
                </div>
                <div class="text-end">
                    <div class="fw-bold text-primary mb-1"><?= format_money((float)$pkg['price']) ?></div>
                    <button class="btn btn-primary btn-sm">Satın Al</button>
                </div>
            </div>
        </form>
        <?php endforeach; ?>
        <?php if (empty($available)): ?>
        <div class="empty-state"><i class="bi bi-cart"></i><br>Satılan paket bulunmuyor</div>
        <?php endif; ?>
    </div>
</div>

<?php require APP_PATH . '/Views/customer/partials/footer.php'; ?>
