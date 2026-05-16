<?php
$title = $title ?? 'Müşteriler';
$breadcrumb = $breadcrumb ?? [
    ['label' => 'Dashboard', 'url' => admin_url('?route=')],
    ['label' => 'Müşteriler'],
];
require APP_PATH . '/Views/admin/partials/header.php';
?>

<div class="section-title-bar">
    <div>
        <h5>Müşteri Yönetimi</h5>
        <small class="text-muted">Toplam <?= number_format($total_customers) ?> müşteri</small>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= admin_url('?route=appointments/create') ?>" class="btn btn-primary"><i class="bi bi-calendar-plus me-1"></i> Yeni Randevu</a>
        <button class="btn btn-outline-success" type="button" data-bs-toggle="collapse" data-bs-target="#quickCustomerForm">
            <i class="bi bi-person-plus me-1"></i> Hızlı Müşteri Kaydı
        </button>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <span class="stat-icon"><i class="bi bi-person-badge"></i></span>
            <div class="stat-label">Toplam Müşteri</div>
            <div class="stat-value"><?= number_format($total_customers) ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card tone-info">
            <span class="stat-icon"><i class="bi bi-box-seam"></i></span>
            <div class="stat-label">Aktif Paket</div>
            <div class="stat-value"><?= number_format($active_packages) ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card tone-success">
            <span class="stat-icon"><i class="bi bi-cash-stack"></i></span>
            <div class="stat-label">Aylık Gelir</div>
            <div class="stat-value"><?= format_money((float)$revenue) ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card tone-danger">
            <span class="stat-icon"><i class="bi bi-exclamation-triangle"></i></span>
            <div class="stat-label">Kritik Seans</div>
            <div class="stat-value"><?= (int)$critical ?></div>
        </div>
    </div>
</div>

<div class="collapse mb-4" id="quickCustomerForm">
    <div class="panel">
        <div class="panel-header"><h6><i class="bi bi-person-plus me-1"></i> Yeni Müşteri</h6></div>
        <form method="post" action="<?= admin_url('?route=customers/create') ?>" class="panel-body row g-3">
            <?= csrf_field() ?>
            <input type="hidden" name="verify_email" value="1">
            <div class="col-md-3"><label class="form-label">Ad *</label><input name="first_name" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Soyad *</label><input name="last_name" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Telefon *</label><input name="phone" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">E-posta *</label><input name="email" type="email" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Şifre</label><input name="password" class="form-control" placeholder="Otomatik üretilsin"></div>
            <div class="col-md-9 d-flex align-items-end">
                <button class="btn btn-success"><i class="bi bi-check2 me-1"></i> Müşteri Oluştur</button>
            </div>
        </form>
    </div>
</div>

<div class="panel mb-4">
    <div class="panel-header">
        <h6>Müşteri Listesi</h6>
        <small class="text-muted small">Detayı görmek için isimlerine tıklayın</small>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Müşteri</th>
                    <th>Telefon</th>
                    <th>Aktif Paket</th>
                    <th>Kalan Seans</th>
                    <th>Son Ödeme</th>
                    <th class="text-end">İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers['data'] as $c): $rem = (int)($c['remaining_sessions'] ?? 0); $totalSess = (int)($c['total_sessions'] ?? max($rem, 10)); $pct = $totalSess ? min(100, round($rem / $totalSess * 100)) : 0; ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;font-weight:700;color:var(--primary);background:var(--primary-soft) !important;">
                                <?= strtoupper(mb_substr($c['first_name'], 0, 1)) ?>
                            </span>
                            <div>
                                <a href="<?= admin_url('?route=customers/show&id=' . (int)$c['id']) ?>" class="text-decoration-none">
                                    <strong><?= e($c['first_name'].' '.$c['last_name']) ?></strong>
                                </a>
                                <small class="text-muted d-block"><?= e($c['email']) ?></small>
                            </div>
                        </div>
                    </td>
                    <td><?= e($c['phone'] ?? '-') ?></td>
                    <td><?= e($c['active_package'] ?? '-') ?></td>
                    <td style="min-width: 160px;">
                        <div class="session-progress <?= $rem <= 1 ? 'danger' : ($rem <= 3 ? 'warning' : '') ?>">
                            <div class="bar" style="width: <?= $pct ?>%"></div>
                        </div>
                        <small class="text-muted"><?= $rem ?> seans</small>
                    </td>
                    <td><?= status_badge($c['last_payment_status'] ?? 'pending') ?></td>
                    <td class="text-end">
                        <a href="<?= admin_url('?route=customers/show&id=' . (int)$c['id']) ?>" class="btn btn-soft btn-sm"><i class="bi bi-eye"></i></a>
                        <a href="<?= admin_url('?route=appointments/create&customer_id=' . (int)$c['id']) ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-calendar-plus"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($customers['data'])): ?>
                <tr><td colspan="6">
                    <div class="empty-state">
                        <div class="icon"><i class="bi bi-people"></i></div>
                        <h6>Henüz müşteri yok</h6>
                        <p>İlk müşterinizi eklemek için "Hızlı Müşteri Kaydı" panelini açın.</p>
                    </div>
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel">
    <div class="panel-header"><h6><i class="bi bi-box me-1"></i> Müşteriye Paket Tanımla</h6></div>
    <form method="post" action="<?= admin_url('?route=customers/package') ?>" class="panel-body row g-3">
        <?= csrf_field() ?>
        <div class="col-md-4">
            <label class="form-label">Müşteri</label>
            <select name="customer_id" class="form-select" required>
                <option value="">Müşteri seçin</option>
                <?php foreach ($customers['data'] as $c): ?>
                <option value="<?= $c['id'] ?>"><?= e($c['first_name'].' '.$c['last_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Paket</label>
            <select name="package_id" class="form-select" required>
                <?php foreach ($packages as $p): ?>
                <option value="<?= $p['id'] ?>"><?= e($p['name']) ?> · <?= format_money((float)$p['price']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Ödeme Yöntemi</label>
            <select name="payment_method" class="form-select">
                <option value="manual">Havale / EFT</option>
                <option value="online">Online Kart</option>
            </select>
        </div>
        <div class="col-12">
            <button class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Paketi Tanımla</button>
        </div>
    </form>
</div>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
