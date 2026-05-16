<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>

<div class="section-title-bar">
    <div>
        <h5>Ödemeler</h5>
        <small class="text-muted">Tüm online ve manuel ödeme hareketleri</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= admin_url('?route=reports') ?>" class="btn btn-outline-secondary"><i class="bi bi-bar-chart me-1"></i> Raporlar</a>
    </div>
</div>

<form class="filter-bar row g-2 align-items-end" method="get">
    <input type="hidden" name="route" value="payments">
    <div class="col-md-3">
        <label>Durum</label>
        <select name="status" class="form-select form-select-sm">
            <option value="">Tümü</option>
            <?php foreach (['pending','paid','failed','cancelled','refunded'] as $st): ?>
            <option value="<?= $st ?>" <?= ($_GET['status'] ?? '') === $st ? 'selected' : '' ?>><?= e($st) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label>Sağlayıcı</label>
        <select name="provider" class="form-select form-select-sm">
            <option value="">Tümü</option>
            <option value="iyzico"<?= ($_GET['provider'] ?? '') === 'iyzico' ? ' selected' : '' ?>>iyzico</option>
            <option value="paytr"<?= ($_GET['provider'] ?? '') === 'paytr' ? ' selected' : '' ?>>PayTR</option>
            <option value="manual"<?= ($_GET['provider'] ?? '') === 'manual' ? ' selected' : '' ?>>Manuel</option>
        </select>
    </div>
    <div class="col-md-4">
        <label>Müşteri ara</label>
        <input type="search" name="search" class="form-control form-control-sm" value="<?= e($_GET['search'] ?? '') ?>">
    </div>
    <div class="col-md-2 filter-actions">
        <button class="btn btn-primary btn-sm flex-grow-1">Filtrele</button>
        <a href="<?= admin_url('?route=payments') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
    </div>
</form>

<div class="panel">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th><th>Müşteri</th><th>Tutar</th><th>Sağlayıcı</th><th>Durum</th><th>Tarih</th><th class="text-end">İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $p): ?>
                <tr>
                    <td><strong>#<?= (int)$p['id'] ?></strong></td>
                    <td>
                        <strong><?= e($p['customer_name'] ?? '—') ?></strong>
                        <?php if (!empty($p['related_label'])): ?>
                        <small class="text-muted d-block"><?= e($p['related_label']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><strong class="text-success"><?= format_money((float)$p['amount']) ?></strong></td>
                    <td><span class="chip chip-muted"><?= e($p['provider'] ?? 'manual') ?></span></td>
                    <td><?= status_badge($p['status']) ?></td>
                    <td class="text-muted small"><?= format_date($p['created_at']) ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?php if (($p['status'] ?? '') !== 'paid'): ?>
                            <form method="post" action="<?= admin_url('?route=payments/status') ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                <input type="hidden" name="status" value="paid">
                                <input type="hidden" name="redirect" value="<?= e(admin_url('?route=payments')) ?>">
                                <button class="btn btn-outline-success" data-confirm="Bu ödeme onaylanıp PAID yapılsın mı?" title="Ödeme Alındı"><i class="bi bi-check2"></i></button>
                            </form>
                            <?php endif; ?>
                            <?php if (($p['status'] ?? '') === 'paid'): ?>
                            <form method="post" action="<?= admin_url('?route=payments/status') ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                <input type="hidden" name="status" value="refunded">
                                <input type="hidden" name="redirect" value="<?= e(admin_url('?route=payments')) ?>">
                                <button class="btn btn-outline-info" data-confirm="Bu ödeme iade olarak işaretlensin mi?" title="İade"><i class="bi bi-arrow-counterclockwise"></i></button>
                            </form>
                            <?php endif; ?>
                            <a href="<?= admin_url('?route=payments/show&id=' . (int)$p['id']) ?>" class="btn btn-soft"><i class="bi bi-eye"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($payments)): ?>
                <tr><td colspan="7"><div class="empty-state"><div class="icon"><i class="bi bi-credit-card"></i></div><h6>Ödeme bulunamadı</h6></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
