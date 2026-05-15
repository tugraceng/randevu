<?php require APP_PATH . '/Views/admin/partials/header.php';
$rows = $result['data'] ?? [];
$page = $result['page'] ?? 1;
?>
<div class="d-flex flex-wrap gap-2 mb-3">
    <a href="<?= admin_url('?route=appointments/create') ?>" class="btn btn-primary"><i class="bi bi-plus"></i> Yeni</a>
    <a href="<?= admin_url('?route=appointments/calendar') ?>" class="btn btn-outline-secondary"><i class="bi bi-calendar3"></i> Takvim</a>
</div>

<form class="filter-bar row g-2" method="get">
    <input type="hidden" name="route" value="appointments">
    <div class="col-md-2"><input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($filters['date_from'] ?? '') ?>" placeholder="Başlangıç"></div>
    <div class="col-md-2"><input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($filters['date_to'] ?? '') ?>"></div>
    <div class="col-md-2">
        <select name="status" class="form-select form-select-sm">
            <option value="">Durum</option>
            <?php foreach (['pending','approved','completed','cancelled','no_show'] as $s): ?>
            <option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= e($s) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="service_id" class="form-select form-select-sm">
            <option value="">Hizmet</option>
            <?php foreach ($services as $s): ?><option value="<?= $s['id'] ?>" <?= ($filters['service_id'] ?? '') == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="staff_id" class="form-select form-select-sm">
            <option value="">Personel</option>
            <?php foreach ($staff as $st): ?><option value="<?= $st['id'] ?>" <?= ($filters['staff_id'] ?? '') == $st['id'] ? 'selected' : '' ?>><?= e($st['name']) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2"><input type="search" name="search" class="form-control form-control-sm" value="<?= e($filters['search'] ?? '') ?>" placeholder="Müşteri ara"></div>
    <div class="col-md-2"><button class="btn btn-sm btn-primary w-100">Filtrele</button></div>
</form>

<div class="table-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Müşteri</th><th>Hizmet</th><th>Personel</th><th>Tarih</th><th>Durum</th><th>Ödeme</th><th>Paket</th><th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $a): ?>
            <tr>
                <td>
                    <strong><?= e($a['customer_name']) ?></strong><br>
                    <small class="text-muted"><?= e($a['customer_phone'] ?? '') ?></small>
                </td>
                <td><?= e($a['service_name']) ?></td>
                <td><?= e($a['staff_name'] ?? '-') ?></td>
                <td><?= format_date($a['appointment_date']) ?><br><small><?= format_time($a['start_time']) ?></small></td>
                <td><?= status_badge($a['status']) ?></td>
                <td><?= status_badge($a['payment_status']) ?></td>
                <td><?php if ($a['customer_package_id']): ?><span class="badge bg-info"><?= e($a['package_name'] ?? 'Paket') ?> (<?= (int)($a['remaining_sessions'] ?? 0) ?>)</span><?php else: ?>-<?php endif; ?></td>
                <td class="appointment-row-actions text-nowrap">
                    <a href="<?= admin_url('?route=appointments/show&id=' . $a['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                    <form method="post" action="<?= admin_url('?route=appointments/status') ?>" class="d-inline">
                        <?= csrf_field() ?><input type="hidden" name="id" value="<?= $a['id'] ?>">
                        <input type="hidden" name="redirect" value="<?= e(admin_url('?route=appointments')) ?>">
                        <input type="hidden" name="status" value="approved">
                        <button class="btn btn-sm btn-success" title="Onayla" data-confirm="Onaylansın mı?"><i class="bi bi-check"></i></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($rows)): ?>
            <tr><td colspan="8" class="text-center text-muted py-5">Randevu bulunamadı</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= pagination_links($result['total'] ?? 0, $page, $result['per_page'] ?? 20, admin_url('?route=appointments')) ?>
<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
