<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>

<div class="section-title-bar">
    <div>
        <h5>Hizmetler</h5>
        <small class="text-muted">Müşterilerinize sunduğunuz hizmetler ve fiyat listesi</small>
    </div>
    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#serviceModal">
        <i class="bi bi-plus-lg me-1"></i> Yeni Hizmet
    </button>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($services as $s): ?>
    <div class="col-md-6 col-xl-4">
        <div class="panel hover-lift h-100 d-flex flex-column overflow-hidden">
            <?php if (!empty($s['image'])): ?>
            <div class="service-cover">
                <img src="<?= base_url(e($s['image'])) ?>" alt="<?= e($s['name']) ?>">
                <span class="service-price-tag"><?= format_money((float)$s['price']) ?></span>
            </div>
            <?php endif; ?>
            <div class="panel-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0"><?= e($s['name']) ?></h6>
                    <span class="status-pill <?= !empty($s['status']) ? 'status-approved' : 'status-cancelled' ?>">
                        <?= !empty($s['status']) ? 'Aktif' : 'Pasif' ?>
                    </span>
                </div>
                <p class="text-muted small mb-3"><?= e($s['description'] ?? '') ?></p>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="chip"><i class="bi bi-clock"></i><?= (int)$s['duration_minutes'] ?> dk</span>
                    <span class="chip" style="background:rgba(16,185,129,.12); color:var(--success);">
                        <i class="bi bi-tag"></i><?= format_money((float)$s['price']) ?>
                    </span>
                    <?php if (!empty($s['deposit_price'])): ?>
                    <span class="chip chip-muted"><i class="bi bi-cash-coin"></i>Kapora <?= format_money((float)$s['deposit_price']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#serviceModal-<?= (int)$s['id'] ?>">
                        <i class="bi bi-pencil"></i> Düzenle
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit modal -->
        <div class="modal fade" id="serviceModal-<?= (int)$s['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form method="post" action="<?= admin_url('?route=services/save') ?>" class="modal-content" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Hizmet Düzenle</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-md-6"><label class="form-label">Ad</label><input name="name" class="form-control" value="<?= e($s['name']) ?>" required></div>
                        <div class="col-md-3"><label class="form-label">Süre (dk)</label><input name="duration_minutes" type="number" class="form-control" value="<?= (int)$s['duration_minutes'] ?>"></div>
                        <div class="col-md-3"><label class="form-label">Sıra</label><input name="sort_order" type="number" class="form-control" value="<?= (int)($s['sort_order'] ?? 0) ?>"></div>
                        <div class="col-md-6"><label class="form-label">Fiyat</label><input name="price" type="number" step="0.01" class="form-control" value="<?= e($s['price']) ?>"></div>
                        <div class="col-md-6"><label class="form-label">Kapora</label><input name="deposit_price" type="number" step="0.01" class="form-control" value="<?= e($s['deposit_price'] ?? 0) ?>"></div>
                        <div class="col-12"><label class="form-label">Açıklama</label><textarea name="description" class="form-control" rows="3"><?= e($s['description'] ?? '') ?></textarea></div>
                        <div class="col-md-6"><label class="form-label">Görsel</label><input type="file" name="image" class="form-control" accept="image/*"></div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check"><input type="checkbox" name="status" value="1" class="form-check-input" id="svc<?= (int)$s['id'] ?>Active" <?= !empty($s['status']) ? 'checked' : '' ?>><label class="form-check-label" for="svc<?= (int)$s['id'] ?>Active">Aktif</label></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">İptal</button>
                        <button class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($services)): ?>
    <div class="col-12">
        <div class="empty-state">
            <div class="icon"><i class="bi bi-briefcase"></i></div>
            <h6>Henüz hizmet yok</h6>
            <p>"Yeni Hizmet" butonu ile ilk hizmetinizi tanımlayın.</p>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Create modal -->
<div class="modal fade" id="serviceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="post" action="<?= admin_url('?route=services/save') ?>" class="modal-content" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title">Yeni Hizmet</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6"><label class="form-label">Ad *</label><input name="name" class="form-control" required></div>
                <div class="col-md-3"><label class="form-label">Süre (dk)</label><input name="duration_minutes" type="number" class="form-control" value="30"></div>
                <div class="col-md-3"><label class="form-label">Sıra</label><input name="sort_order" type="number" class="form-control" value="0"></div>
                <div class="col-md-6"><label class="form-label">Fiyat *</label><input name="price" type="number" step="0.01" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Kapora</label><input name="deposit_price" type="number" step="0.01" class="form-control" value="0"></div>
                <div class="col-12"><label class="form-label">Açıklama</label><textarea name="description" class="form-control" rows="3"></textarea></div>
                <div class="col-md-6"><label class="form-label">Görsel</label><input type="file" name="image" class="form-control" accept="image/*"></div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check"><input type="checkbox" name="status" value="1" class="form-check-input" id="svcActive" checked><label class="form-check-label" for="svcActive">Aktif</label></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">İptal</button>
                <button class="btn btn-primary">Kaydet</button>
            </div>
        </form>
    </div>
</div>

<style>
.service-cover { height: 140px; position: relative; overflow: hidden; background: var(--grad-primary); }
.service-cover img { width: 100%; height: 100%; object-fit: cover; transition: transform var(--t-med); }
.service-cover:hover img { transform: scale(1.05); }
.service-price-tag {
    position: absolute;
    bottom: 10px; right: 10px;
    background: rgba(255, 255, 255, .95);
    color: var(--primary);
    padding: .25rem .75rem;
    border-radius: var(--radius-pill);
    font-weight: 700;
    font-size: .82rem;
    box-shadow: var(--shadow-sm);
    backdrop-filter: blur(8px);
}
</style>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
