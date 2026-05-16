<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>

<div class="section-title-bar">
    <div>
        <h5>Kampanyalar</h5>
        <small class="text-muted">Müşterilerinize özel duyurular, indirimler ve sezonluk fırsatlar</small>
    </div>
    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#newCampaignPane">
        <i class="bi bi-plus-lg me-1"></i> Yeni Kampanya
    </button>
</div>

<div class="collapse mb-4" id="newCampaignPane">
    <div class="panel">
        <div class="panel-header"><h6>Yeni Kampanya Tanımla</h6></div>
        <form method="post" action="<?= admin_url('?route=campaigns/save') ?>" class="panel-body row g-3" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="col-md-6"><label class="form-label">Başlık *</label><input name="title" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Başlangıç</label><input name="start_date" type="date" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">Bitiş</label><input name="end_date" type="date" class="form-control"></div>
            <div class="col-12"><label class="form-label">Açıklama</label><textarea name="description" class="form-control" rows="3"></textarea></div>
            <div class="col-md-6"><label class="form-label">Görsel</label><input type="file" name="image" class="form-control" accept="image/*"></div>
            <div class="col-md-6 d-flex align-items-end">
                <div class="form-check form-switch"><input type="checkbox" class="form-check-input" id="campActive" name="status" value="1" checked><label class="form-check-label" for="campActive">Aktif yayında</label></div>
            </div>
            <div class="col-12"><button class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Kampanyayı Kaydet</button></div>
        </form>
    </div>
</div>

<div class="row g-3">
    <?php foreach ($campaigns as $c): ?>
    <div class="col-md-6 col-xl-4">
        <div class="panel hover-lift h-100 overflow-hidden d-flex flex-column">
            <?php if (!empty($c['image'])): ?>
            <div class="campaign-cover">
                <img src="<?= base_url(e($c['image'])) ?>" alt="">
            </div>
            <?php else: ?>
            <div class="campaign-cover campaign-cover-empty"></div>
            <?php endif; ?>
            <div class="panel-body d-flex flex-column flex-grow-1">
                <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                    <h6 class="mb-0"><?= e($c['title']) ?></h6>
                    <span class="status-pill <?= !empty($c['status']) ? 'status-approved' : 'status-cancelled' ?>"><?= !empty($c['status']) ? 'Aktif' : 'Pasif' ?></span>
                </div>
                <?php if (!empty($c['start_date']) || !empty($c['end_date'])): ?>
                <small class="text-muted d-block mb-2"><i class="bi bi-calendar3 me-1"></i><?= e($c['start_date'] ?? '—') ?> &mdash; <?= e($c['end_date'] ?? '—') ?></small>
                <?php endif; ?>
                <p class="text-muted small mb-3"><?= e($c['description'] ?? '') ?></p>
                <form method="post" action="<?= admin_url('?route=campaigns/delete') ?>" class="mt-auto">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                    <button class="btn btn-outline-danger btn-sm" data-confirm="Bu kampanya silinsin mi?"><i class="bi bi-trash me-1"></i> Sil</button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($campaigns)): ?>
    <div class="col-12">
        <div class="empty-state">
            <div class="icon"><i class="bi bi-megaphone"></i></div>
            <h6>Henüz kampanya yok</h6>
            <p>Müşterilerinize özel duyurular ve indirimler oluşturmak için ilk kampanyanızı ekleyin.</p>
            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#newCampaignPane">
                <i class="bi bi-plus-lg me-1"></i> Yeni Kampanya
            </button>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.campaign-cover { height: 140px; background: var(--grad-primary); border-radius: var(--radius-lg) var(--radius-lg) 0 0; overflow: hidden; position: relative; }
.campaign-cover img { width: 100%; height: 100%; object-fit: cover; }
.campaign-cover-empty { height: 80px; }
.campaign-cover-empty::after { content: '\F4DA'; font-family: 'bootstrap-icons'; color: rgba(255,255,255,.5); font-size: 2.5rem; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); }
</style>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
