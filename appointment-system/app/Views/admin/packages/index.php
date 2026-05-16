<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>

<div class="section-title-bar">
    <div>
        <h5>Paket Kataloğu</h5>
        <small class="text-muted">Müşterilere satabileceğiniz seans paketleri</small>
    </div>
    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#newPackagePane">
        <i class="bi bi-plus-lg me-1"></i> Yeni Paket
    </button>
</div>

<div class="collapse mb-4" id="newPackagePane">
    <div class="panel">
        <div class="panel-header"><h6>Yeni Paket Tanımla</h6></div>
        <form method="post" action="<?= admin_url('?route=packages/save') ?>" class="panel-body row g-3">
            <?= csrf_field() ?>
            <div class="col-md-3">
                <label class="form-label">Hizmet *</label>
                <select name="service_id" class="form-select" required>
                    <?php foreach ($services as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4"><label class="form-label">Paket Adı *</label><input name="name" class="form-control" required></div>
            <div class="col-md-2"><label class="form-label">Seans Sayısı *</label><input name="session_count" type="number" min="1" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Fiyat *</label><input name="price" type="number" step="0.01" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Geçerlilik (gün)</label><input name="validity_days" type="number" class="form-control" value="180"></div>
            <div class="col-md-3"><label class="form-label">Sıra</label><input name="sort_order" type="number" class="form-control" value="0"></div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check"><input type="checkbox" name="status" value="1" class="form-check-input" id="pkgActive" checked><label class="form-check-label" for="pkgActive">Aktif</label></div>
            </div>
            <div class="col-12"><button class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Paketi Oluştur</button></div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($packages as $p): ?>
    <div class="col-md-6 col-xl-4">
        <div class="package-card hover-lift d-flex flex-column h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h6 class="mb-1"><?= e($p['name']) ?></h6>
                    <small class="text-muted"><i class="bi bi-stars me-1"></i><?= e($p['service_name']) ?></small>
                </div>
                <?= status_badge(!empty($p['status']) ? 'approved' : 'cancelled') ?>
            </div>
            <div class="surface-soft p-3 rounded my-3 d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block">Paket Fiyatı</small>
                    <strong class="fs-5 text-primary"><?= format_money((float)$p['price']) ?></strong>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">Seans</small>
                    <strong class="fs-5"><?= (int)$p['session_count'] ?></strong>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="chip"><i class="bi bi-calendar3"></i><?= (int)$p['validity_days'] ?> gün geçerli</span>
                <?php if (!empty($p['price']) && !empty($p['session_count'])): ?>
                <span class="chip chip-muted"><i class="bi bi-calculator"></i>Seans ≈ <?= format_money((float)$p['price'] / max(1,(int)$p['session_count'])) ?></span>
                <?php endif; ?>
            </div>
            <a href="<?= admin_url('?route=customers') ?>" class="btn btn-soft btn-sm w-100 mt-auto"><i class="bi bi-person-plus me-1"></i> Müşteriye Tanımla</a>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($packages)): ?>
    <div class="col-12">
        <div class="empty-state">
            <div class="icon"><i class="bi bi-box"></i></div>
            <h6>Tanımlı paket yok</h6>
            <p>İlk paketinizi oluşturmak için yukarıdaki "Yeni Paket" panelini açın.</p>
            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#newPackagePane">
                <i class="bi bi-plus-lg me-1"></i> Yeni Paket Oluştur
            </button>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
