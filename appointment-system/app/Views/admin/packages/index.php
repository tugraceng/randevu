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
        <div class="package-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0"><?= e($p['name']) ?></h6>
                <?= status_badge(!empty($p['status']) ? 'approved' : 'cancelled') ?>
            </div>
            <small class="text-muted d-block mb-3"><i class="bi bi-stars me-1"></i><?= e($p['service_name']) ?></small>
            <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Seans</span><strong><?= (int)$p['session_count'] ?></strong></div>
            <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Süre</span><strong><?= (int)$p['validity_days'] ?> gün</strong></div>
            <div class="d-flex justify-content-between mb-3"><span class="text-muted small">Fiyat</span><strong class="text-primary"><?= format_money((float)$p['price']) ?></strong></div>
            <a href="<?= admin_url('?route=customers') ?>" class="btn btn-soft btn-sm w-100"><i class="bi bi-person-plus me-1"></i> Müşteriye Tanımla</a>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($packages)): ?>
    <div class="col-12">
        <div class="empty-state">
            <div class="icon"><i class="bi bi-box"></i></div>
            <h6>Tanımlı paket yok</h6>
            <p>İlk paketinizi oluşturmak için yukarıdaki "Yeni Paket" panelini açın.</p>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
    .package-card { background:#fff; border:1px solid var(--line); border-radius:var(--radius); padding:1.25rem 1.5rem; box-shadow:var(--shadow-xs); height:100%; }
</style>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
