<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>
<div class="d-flex justify-content-between mb-3">
    <a href="<?= admin_url('?route=appointments/calendar&month=' . date('Y-m', strtotime($month . '-01 -1 month'))) ?>" class="btn btn-sm btn-outline-secondary">&laquo;</a>
    <h5 class="mb-0"><?= e($month) ?></h5>
    <a href="<?= admin_url('?route=appointments/calendar&month=' . date('Y-m', strtotime($month . '-01 +1 month'))) ?>" class="btn btn-sm btn-outline-secondary">&raquo;</a>
</div>
<div class="table-card p-3">
    <?php foreach ($events as $ev): ?>
    <div class="d-flex justify-content-between border-bottom py-2">
        <span><?= format_date($ev['appointment_date']) ?> <?= format_time($ev['start_time']) ?></span>
        <strong><?= e($ev['title']) ?></strong>
        <span><?= e($ev['service_name']) ?></span>
        <?= status_badge($ev['status']) ?>
        <a href="<?= admin_url('?route=appointments/show&id=' . $ev['id']) ?>">Detay</a>
    </div>
    <?php endforeach; ?>
    <?php if (empty($events)): ?><p class="text-muted mb-0">Bu ay randevu yok.</p><?php endif; ?>
</div>
<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
