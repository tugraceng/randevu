<?php
require APP_PATH . '/Views/admin/partials/header.php';
$prev = date('Y-m', strtotime($month . '-01 -1 month'));
$next = date('Y-m', strtotime($month . '-01 +1 month'));
$monthLabel = strftime('%B %Y', strtotime($month . '-01')) ?: $month;
?>

<div class="section-title-bar">
    <div>
        <h5>Randevu Takvimi</h5>
        <small class="text-muted"><?= e($month) ?> dönemi · Toplam <?= count($events) ?> randevu</small>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <a href="<?= admin_url('?route=appointments/calendar&month=' . $prev) ?>" class="btn btn-icon" title="Önceki ay"><i class="bi bi-chevron-left"></i></a>
        <span class="chip chip-primary px-3 py-2"><i class="bi bi-calendar3 me-1"></i><?= e($month) ?></span>
        <a href="<?= admin_url('?route=appointments/calendar&month=' . $next) ?>" class="btn btn-icon" title="Sonraki ay"><i class="bi bi-chevron-right"></i></a>
        <a href="<?= admin_url('?route=appointments') ?>" class="btn btn-outline-secondary"><i class="bi bi-list me-1"></i> Liste</a>
        <a href="<?= admin_url('?route=appointments/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Yeni</a>
    </div>
</div>

<div class="table-rounded">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Tarih</th>
                    <th>Saat</th>
                    <th>Müşteri</th>
                    <th>Hizmet</th>
                    <th>Durum</th>
                    <th class="text-end">İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $ev):
                    $initial = strtoupper(mb_substr($ev['title'] ?? '?', 0, 1));
                ?>
                <tr>
                    <td><strong><?= format_date($ev['appointment_date']) ?></strong></td>
                    <td><span class="chip chip-muted"><i class="bi bi-clock me-1"></i><?= format_time($ev['start_time']) ?></span></td>
                    <td>
                        <div class="table-avatar">
                            <span class="avatar"><?= e($initial) ?></span>
                            <div><strong><?= e($ev['title']) ?></strong></div>
                        </div>
                    </td>
                    <td><?= e($ev['service_name']) ?></td>
                    <td><?= status_badge($ev['status']) ?></td>
                    <td class="text-end">
                        <a href="<?= admin_url('?route=appointments/show&id=' . (int)$ev['id']) ?>" class="btn btn-icon" title="Detay"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($events)): ?>
                <tr><td colspan="6">
                    <div class="empty-state">
                        <div class="icon"><i class="bi bi-calendar-x"></i></div>
                        <h6>Bu ay randevu yok</h6>
                        <p>Henüz <?= e($month) ?> dönemine ait randevu bulunmuyor.</p>
                        <a href="<?= admin_url('?route=appointments/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus me-1"></i> Yeni Randevu</a>
                    </div>
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
