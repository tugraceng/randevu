<?php require APP_PATH . '/Views/admin/partials/header.php'; $p = $package; ?>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="table-card p-4">
            <h5><?= e($p['package_name']) ?></h5>
            <p class="mb-1"><strong>Müşteri:</strong> <?= e($p['customer_name']) ?></p>
            <p class="mb-1"><strong>Hizmet:</strong> <?= e($p['service_name']) ?></p>
            <p class="mb-1"><strong>Seans:</strong> <?= (int)$p['used_sessions'] ?> kullanılan / <?= (int)$p['remaining_sessions'] ?> kalan / <?= (int)$p['total_sessions'] ?> toplam</p>
            <p class="mb-1"><strong>Bitiş:</strong> <?= format_date($p['expiry_date']) ?: '-' ?></p>
            <p class="mb-0"><strong>Durum:</strong> <?= status_badge($p['status']) ?> · <?= status_badge($p['payment_status']) ?></p>
        </div>
        <form method="post" action="<?= admin_url('?route=packages/session') ?>" class="table-card p-3 mt-3">
            <?= csrf_field() ?>
            <input type="hidden" name="customer_package_id" value="<?= $p['id'] ?>">
            <label class="form-label">Manuel seans (+/-)</label>
            <div class="input-group">
                <input type="number" name="delta" class="form-control" value="1" required>
                <input type="text" name="note" class="form-control" placeholder="Not">
                <button class="btn btn-outline-primary">Uygula</button>
            </div>
        </form>
    </div>
    <div class="col-lg-6">
        <div class="table-card p-3 mb-3">
            <h6>Seans Logları</h6>
            <?php foreach ($logs as $log): ?>
            <div class="small border-bottom py-1"><?= e($log['action']) ?> (<?= (int)$log['session_count'] ?>) — <?= e($log['note'] ?? '') ?></div>
            <?php endforeach; ?>
        </div>
        <div class="table-card p-3">
            <h6>Bağlı Randevular</h6>
            <?php foreach ($appointments as $a): ?>
            <div class="small py-1"><a href="<?= admin_url('?route=appointments/show&id=' . $a['id']) ?>"><?= format_date($a['appointment_date']) ?> — <?= e($a['service_name']) ?></a></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
