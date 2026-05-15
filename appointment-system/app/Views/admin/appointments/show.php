<?php
require APP_PATH . '/Views/admin/partials/header.php';
$a = $appointment;
?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="table-card p-4">
            <p><strong>Müşteri:</strong> <?= e($a['first_name'] . ' ' . $a['last_name']) ?> (<?= e($a['phone'] ?? '') ?>)</p>
            <p><strong>Hizmet:</strong> <?= e($a['service_name']) ?> · <strong>Personel:</strong> <?= e($a['staff_name'] ?? '-') ?></p>
            <p><strong>Tarih:</strong> <?= format_date($a['appointment_date']) ?> <?= format_time($a['start_time']) ?> - <?= format_time($a['end_time']) ?></p>
            <p><strong>Durum:</strong> <?= status_badge($a['status']) ?> · <strong>Ödeme:</strong> <?= status_badge($a['payment_status']) ?></p>
            <?php if ($a['notes']): ?><p><strong>Not:</strong> <?= e($a['notes']) ?></p><?php endif; ?>
        </div>
        <form method="post" action="<?= admin_url('?route=appointments/note') ?>" class="table-card p-3 mt-3">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $a['id'] ?>">
            <input type="hidden" name="redirect" value="<?= e(admin_url('?route=appointments/show&id=' . $a['id'])) ?>">
            <label class="form-label">Not ekle / güncelle</label>
            <textarea name="notes" class="form-control mb-2" rows="2"><?= e($a['notes'] ?? '') ?></textarea>
            <button class="btn btn-sm btn-outline-primary">Kaydet</button>
        </form>
    </div>
    <div class="col-lg-4">
        <div class="table-card p-3">
            <h6 class="fw-semibold">Hızlı İşlemler</h6>
            <?php
            $actions = [
                'approved' => 'Onayla', 'completed' => 'Tamamlandı', 'cancelled' => 'İptal',
                'no_show' => 'Gelmedi',
            ];
            foreach ($actions as $st => $label):
            ?>
            <form method="post" action="<?= admin_url('?route=appointments/status') ?>" class="mb-2">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                <input type="hidden" name="status" value="<?= $st ?>">
                <input type="hidden" name="redirect" value="<?= e(admin_url('?route=appointments/show&id=' . $a['id'])) ?>">
                <button class="btn btn-sm btn-outline-secondary w-100" data-confirm="<?= e($label) ?>?"><?= e($label) ?></button>
            </form>
            <?php endforeach; ?>
            <form method="post" action="<?= admin_url('?route=appointments/paid') ?>" class="mb-2">
                <?= csrf_field() ?><input type="hidden" name="id" value="<?= $a['id'] ?>">
                <input type="hidden" name="redirect" value="<?= e(admin_url('?route=appointments/show&id=' . $a['id'])) ?>">
                <button class="btn btn-sm btn-success w-100">Ödeme Alındı</button>
            </form>
            <form method="post" action="<?= admin_url('?route=appointments/message') ?>" class="mb-2">
                <?= csrf_field() ?><input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                <input type="hidden" name="channel" value="sms">
                <input type="hidden" name="redirect" value="<?= e(admin_url('?route=appointments/show&id=' . $a['id'])) ?>">
                <button class="btn btn-sm btn-outline-info w-100">SMS Hatırlat</button>
            </form>
            <a href="<?= admin_url('?route=appointments/edit&id=' . $a['id']) ?>" class="btn btn-sm btn-primary w-100 mt-2">Düzenle</a>
        </div>
    </div>
</div>
<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
