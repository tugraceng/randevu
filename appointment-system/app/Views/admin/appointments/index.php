<?php
require APP_PATH . '/Views/admin/partials/header.php';
$rows    = $result['data'] ?? [];
$page    = $result['page'] ?? 1;
$total   = $result['total'] ?? 0;
$perPage = $result['per_page'] ?? 20;
$statuses = [
    'pending'   => ['label' => 'Bekliyor',    'btn' => 'warning'],
    'approved'  => ['label' => 'Onaylı',      'btn' => 'primary'],
    'completed' => ['label' => 'Tamamlandı',  'btn' => 'success'],
    'cancelled' => ['label' => 'İptal',       'btn' => 'secondary'],
    'no_show'   => ['label' => 'Gelmedi',     'btn' => 'danger'],
];
?>

<div class="section-title-bar">
    <div>
        <h5>Randevular</h5>
        <small class="text-muted">Toplam <?= number_format($total) ?> kayıt</small>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= admin_url('?route=appointments/calendar') ?>" class="btn btn-outline-secondary"><i class="bi bi-calendar3 me-1"></i> Takvim</a>
        <a href="<?= admin_url('?route=appointments/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Yeni Randevu</a>
    </div>
</div>

<form class="filter-bar row g-2 align-items-end" method="get">
    <input type="hidden" name="route" value="appointments">
    <div class="col-6 col-md-2">
        <label>Başlangıç</label>
        <input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($filters['date_from'] ?? '') ?>">
    </div>
    <div class="col-6 col-md-2">
        <label>Bitiş</label>
        <input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($filters['date_to'] ?? '') ?>">
    </div>
    <div class="col-6 col-md-2">
        <label>Durum</label>
        <select name="status" class="form-select form-select-sm">
            <option value="">Tümü</option>
            <?php foreach ($statuses as $s => $info): ?>
            <option value="<?= e($s) ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= e($info['label']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-6 col-md-2">
        <label>Hizmet</label>
        <select name="service_id" class="form-select form-select-sm">
            <option value="">Tümü</option>
            <?php foreach ($services as $s): ?>
            <option value="<?= $s['id'] ?>" <?= ($filters['service_id'] ?? '') == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-6 col-md-2">
        <label>Personel</label>
        <select name="staff_id" class="form-select form-select-sm">
            <option value="">Tümü</option>
            <?php foreach ($staff as $st): ?>
            <option value="<?= $st['id'] ?>" <?= ($filters['staff_id'] ?? '') == $st['id'] ? 'selected' : '' ?>><?= e($st['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-6 col-md-2">
        <label>Ödeme</label>
        <select name="payment_status" class="form-select form-select-sm">
            <option value="">Tümü</option>
            <?php foreach (['pending','paid','failed','cancelled','refunded','not_required'] as $ps): ?>
            <option value="<?= $ps ?>" <?= ($filters['payment_status'] ?? '') === $ps ? 'selected' : '' ?>><?= e($ps) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-12 col-md-3">
        <label>Müşteri / Telefon</label>
        <input type="search" name="search" class="form-control form-control-sm" value="<?= e($filters['search'] ?? '') ?>" placeholder="Ad, telefon veya e-posta">
    </div>
    <div class="col-6 col-md-2">
        <label>Tip</label>
        <select name="package_only" class="form-select form-select-sm">
            <option value="">Tümü</option>
            <option value="1" <?= !empty($filters['package_only']) ? 'selected' : '' ?>>Paket randevuları</option>
            <option value="0" <?= isset($filters['package_only']) && $filters['package_only'] === '0' ? 'selected' : '' ?>>Tekil randevular</option>
        </select>
    </div>
    <div class="col-12 col-md-3 filter-actions">
        <button class="btn btn-primary btn-sm flex-grow-1"><i class="bi bi-funnel me-1"></i> Filtrele</button>
        <a href="<?= admin_url('?route=appointments') ?>" class="btn btn-outline-secondary btn-sm" data-filter-reset="form" data-submit="false">
            <i class="bi bi-arrow-counterclockwise"></i>
        </a>
    </div>
</form>

<!-- Desktop table -->
<div class="table-card d-none d-md-block">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Müşteri</th>
                    <th>Hizmet / Personel</th>
                    <th>Tarih</th>
                    <th>Durum</th>
                    <th>Ödeme</th>
                    <th>Paket</th>
                    <th class="text-end">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $a): $redir = e(admin_url('?route=appointments')); ?>
                <tr>
                    <td>
                        <strong><?= e($a['customer_name']) ?></strong><br>
                        <small class="text-muted"><i class="bi bi-telephone me-1"></i><?= e($a['customer_phone'] ?? '-') ?></small>
                    </td>
                    <td>
                        <?= e($a['service_name']) ?>
                        <?php if (!empty($a['staff_name'])): ?>
                        <br><small class="text-muted"><i class="bi bi-person me-1"></i><?= e($a['staff_name']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= format_date($a['appointment_date']) ?><br>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i><?= format_time($a['start_time']) ?></small>
                    </td>
                    <td><?= status_badge($a['status']) ?></td>
                    <td><?= status_badge($a['payment_status']) ?></td>
                    <td>
                        <?php if (!empty($a['customer_package_id'])): ?>
                            <span class="chip"><i class="bi bi-box-seam"></i><?= e($a['package_name'] ?? 'Paket') ?></span>
                            <small class="text-muted d-block">Kalan: <?= (int)($a['remaining_sessions'] ?? 0) ?></small>
                        <?php else: ?>
                            <span class="chip chip-muted">Tekil</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end appointment-row-actions">
                        <a href="<?= admin_url('?route=appointments/show&id=' . (int)$a['id']) ?>" class="btn btn-soft btn-sm btn-icon" title="Detay"><i class="bi bi-eye"></i></a>
                        <a href="<?= admin_url('?route=appointments/edit&id=' . (int)$a['id']) ?>" class="btn btn-outline-secondary btn-sm btn-icon" title="Düzenle"><i class="bi bi-pencil"></i></a>
                        <?php
                        $quickStatuses = [
                            'approved'  => ['btn' => 'success',   'icon' => 'bi-check2', 'label' => 'Onayla'],
                            'completed' => ['btn' => 'primary',   'icon' => 'bi-check-all', 'label' => 'Tamamlandı'],
                            'cancelled' => ['btn' => 'secondary', 'icon' => 'bi-x',      'label' => 'İptal'],
                            'no_show'   => ['btn' => 'danger',    'icon' => 'bi-person-x', 'label' => 'Gelmedi'],
                        ];
                        foreach ($quickStatuses as $st => $cfg):
                            if (($a['status'] ?? '') === $st) continue;
                        ?>
                        <form method="post" action="<?= admin_url('?route=appointments/status') ?>" class="d-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                            <input type="hidden" name="status" value="<?= $st ?>">
                            <input type="hidden" name="redirect" value="<?= $redir ?>">
                            <button class="btn btn-outline-<?= e($cfg['btn']) ?> btn-sm btn-icon" title="<?= e($cfg['label']) ?>" data-confirm="<?= e($cfg['label']) ?> işlemi uygulansın mı?"><i class="bi <?= e($cfg['icon']) ?>"></i></button>
                        </form>
                        <?php endforeach; ?>
                        <form method="post" action="<?= admin_url('?route=appointments/message') ?>" class="d-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="appointment_id" value="<?= (int)$a['id'] ?>">
                            <input type="hidden" name="channel" value="sms">
                            <input type="hidden" name="redirect" value="<?= $redir ?>">
                            <button class="btn btn-outline-info btn-sm btn-icon" title="SMS Hatırlat"><i class="bi bi-chat-dots"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="icon"><i class="bi bi-calendar-x"></i></div>
                            <h6>Randevu bulunamadı</h6>
                            <p>Filtrelerinizi değiştirin veya yeni randevu oluşturun.</p>
                            <a href="<?= admin_url('?route=appointments/create') ?>" class="btn btn-primary btn-sm">Yeni Randevu</a>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile card view -->
<div class="d-md-none">
    <?php foreach ($rows as $a): ?>
    <div class="appointment-card mb-2">
        <div class="head">
            <strong><?= e($a['customer_name']) ?></strong>
            <?= status_badge($a['status']) ?>
        </div>
        <div class="meta"><i class="bi bi-clock me-1"></i><?= format_date($a['appointment_date']) ?> · <?= format_time($a['start_time']) ?></div>
        <div class="meta"><i class="bi bi-stars me-1"></i><?= e($a['service_name']) ?> · <?= e($a['staff_name'] ?? 'Personel atanmamış') ?></div>
        <div class="meta">
            <?= status_badge($a['payment_status']) ?>
            <?php if (!empty($a['customer_package_id'])): ?>
                <span class="chip ms-1"><i class="bi bi-box-seam"></i><?= e($a['package_name']) ?> (<?= (int)$a['remaining_sessions'] ?>)</span>
            <?php endif; ?>
        </div>
        <div class="actions">
            <a href="<?= admin_url('?route=appointments/show&id=' . (int)$a['id']) ?>" class="btn btn-soft btn-sm flex-grow-1"><i class="bi bi-eye me-1"></i> Detay</a>
            <a href="<?= admin_url('?route=appointments/edit&id=' . (int)$a['id']) ?>" class="btn btn-outline-secondary btn-sm flex-grow-1"><i class="bi bi-pencil me-1"></i> Düzenle</a>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($rows)): ?>
        <div class="empty-state">
            <div class="icon"><i class="bi bi-calendar-x"></i></div>
            <h6>Randevu bulunamadı</h6>
        </div>
    <?php endif; ?>
</div>

<?= pagination_links($total, $page, $perPage, admin_url('?route=appointments')) ?>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
