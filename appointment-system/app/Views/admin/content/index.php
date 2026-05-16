<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>

<div class="section-title-bar">
    <div>
        <h5>İçerik Yönetimi</h5>
        <small class="text-muted">Frontend bölümleri, SSS, galeri ve müşteri yorumlarını düzenleyin</small>
    </div>
</div>

<ul class="nav nav-pills mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-sections" type="button"><i class="bi bi-file-text me-1"></i> Bölümler</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-faq" type="button"><i class="bi bi-patch-question me-1"></i> SSS</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-gallery" type="button"><i class="bi bi-images me-1"></i> Galeri</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-reviews" type="button"><i class="bi bi-chat-quote me-1"></i> Yorumlar</button></li>
</ul>

<div class="tab-content">

    <!-- SECTIONS ============================================== -->
    <div class="tab-pane fade show active" id="tab-sections">
        <form method="post" action="<?= admin_url('?route=content/save') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <?php foreach ($sections as $sec): ?>
                <div class="col-12 col-xl-6">
                    <div class="panel hover-lift h-100">
                        <div class="panel-header">
                            <h6><i class="bi bi-bookmark me-1"></i> <?= e($sec['section_key']) ?></h6>
                            <span class="chip chip-muted">#<?= (int)$sec['sort_order'] ?></span>
                        </div>
                        <div class="panel-body">
                            <input type="hidden" name="sections[<?= e($sec['section_key']) ?>][sort_order]" value="<?= (int)$sec['sort_order'] ?>">
                            <div class="mb-2"><label class="form-label">Başlık</label><input class="form-control" name="sections[<?= e($sec['section_key']) ?>][title]" value="<?= e($sec['title'] ?? '') ?>"></div>
                            <div class="mb-2"><label class="form-label">Alt başlık</label><input class="form-control" name="sections[<?= e($sec['section_key']) ?>][subtitle]" value="<?= e($sec['subtitle'] ?? '') ?>"></div>
                            <div class="mb-0"><label class="form-label">İçerik</label><textarea class="form-control" name="sections[<?= e($sec['section_key']) ?>][content]" rows="3"><?= e($sec['content'] ?? '') ?></textarea></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-end mt-3"><button class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Bölümleri Kaydet</button></div>
        </form>
    </div>

    <!-- FAQ ============================================== -->
    <div class="tab-pane fade" id="tab-faq">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="panel">
                    <div class="panel-header"><h6>SSS Listesi</h6></div>
                    <div class="panel-body">
                        <?php foreach ($faqs ?? [] as $f): ?>
                        <form method="post" action="<?= admin_url('?route=content/faq') ?>" class="border-bottom pb-3 mb-3">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
                            <input class="form-control mb-2" name="question" value="<?= e($f['question']) ?>" placeholder="Soru">
                            <textarea class="form-control mb-2" rows="2" name="answer" placeholder="Cevap"><?= e($f['answer']) ?></textarea>
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="number" class="form-control form-control-sm" name="sort_order" value="<?= (int)($f['sort_order'] ?? 0) ?>" style="width:80px;">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="status" value="1" <?= !empty($f['status']) ? 'checked' : '' ?> id="faqA<?= (int)$f['id'] ?>"><label class="form-check-label small" for="faqA<?= (int)$f['id'] ?>">Aktif</label></div>
                                </div>
                                <div>
                                    <button class="btn btn-primary btn-sm">Kaydet</button>
                                    <button class="btn btn-outline-danger btn-sm" formaction="<?= admin_url('?route=content/faq/delete') ?>" data-confirm="Bu SSS silinsin mi?"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        </form>
                        <?php endforeach; ?>
                        <?php if (empty($faqs)): ?>
                        <div class="empty-state"><div class="icon"><i class="bi bi-patch-question"></i></div><h6>SSS yok</h6></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="panel">
                    <div class="panel-header"><h6>Yeni SSS</h6></div>
                    <form method="post" action="<?= admin_url('?route=content/faq') ?>" class="panel-body">
                        <?= csrf_field() ?>
                        <input class="form-control mb-2" name="question" placeholder="Soru" required>
                        <textarea class="form-control mb-2" rows="3" name="answer" placeholder="Cevap" required></textarea>
                        <div class="d-flex justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <input type="number" class="form-control form-control-sm" name="sort_order" value="0" style="width:80px;">
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="status" value="1" id="newFaq" checked><label class="form-check-label small" for="newFaq">Aktif</label></div>
                            </div>
                            <button class="btn btn-primary btn-sm"><i class="bi bi-plus me-1"></i> Ekle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- GALLERY ============================================== -->
    <div class="tab-pane fade" id="tab-gallery">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="row g-3">
                    <?php foreach ($gallery ?? [] as $g): ?>
                    <div class="col-md-4 col-lg-3">
                        <div class="panel hover-lift h-100 overflow-hidden">
                            <img src="<?= base_url(e($g['image'])) ?>" alt="" style="width:100%;height:140px;object-fit:cover;">
                            <div class="panel-body p-2">
                                <small class="d-block text-muted text-truncate"><?= e($g['title'] ?? '—') ?></small>
                                <form method="post" action="<?= admin_url('?route=content/gallery/delete') ?>" class="mt-1">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
                                    <button class="btn btn-outline-danger btn-sm w-100" data-confirm="Silinsin mi?"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($gallery)): ?>
                    <div class="col-12"><div class="empty-state"><div class="icon"><i class="bi bi-images"></i></div><h6>Galeri boş</h6></div></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="panel">
                    <div class="panel-header"><h6>Yeni Görsel Yükle</h6></div>
                    <form method="post" action="<?= admin_url('?route=content/gallery') ?>" class="panel-body" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input class="form-control mb-2" name="title" placeholder="Başlık">
                        <div class="upload-card mb-2">
                            <i class="bi bi-cloud-arrow-up fs-3 text-muted d-block mb-2"></i>
                            <small class="text-muted d-block mb-2">JPG / PNG, max 4MB</small>
                            <input type="file" class="form-control form-control-sm" name="image" accept="image/*" required>
                        </div>
                        <button class="btn btn-primary w-100"><i class="bi bi-upload me-1"></i> Yükle</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- REVIEWS ============================================== -->
    <div class="tab-pane fade" id="tab-reviews">
        <div class="row g-3">
            <?php foreach ($reviews ?? [] as $r): ?>
            <div class="col-md-6">
                <div class="panel h-100">
                    <form method="post" action="<?= admin_url('?route=content/review') ?>" class="panel-body">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <div class="row g-2">
                            <div class="col-md-7"><input class="form-control" name="customer_name" value="<?= e($r['customer_name']) ?>"></div>
                            <div class="col-md-5"><select class="form-select" name="rating">
                                <?php for ($i=1; $i<=5; $i++): ?>
                                <option value="<?= $i ?>" <?= ($r['rating'] ?? 5) == $i ? 'selected' : '' ?>><?= str_repeat('★', $i) ?></option>
                                <?php endfor; ?>
                            </select></div>
                            <div class="col-12"><textarea class="form-control" name="comment" rows="3"><?= e($r['comment']) ?></textarea></div>
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check"><input type="checkbox" name="status" value="1" class="form-check-input" id="rv<?= (int)$r['id'] ?>" <?= !empty($r['status']) ? 'checked' : '' ?>><label class="form-check-label" for="rv<?= (int)$r['id'] ?>">Yayında</label></div>
                            </div>
                            <div class="col-md-6 d-flex gap-1 justify-content-end">
                                <button class="btn btn-primary btn-sm">Kaydet</button>
                                <button class="btn btn-outline-danger btn-sm" formaction="<?= admin_url('?route=content/review/delete') ?>" data-confirm="Yorum silinsin mi?"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="col-md-6">
                <div class="panel h-100">
                    <div class="panel-header"><h6>Yeni Yorum</h6></div>
                    <form method="post" action="<?= admin_url('?route=content/review') ?>" class="panel-body row g-2">
                        <?= csrf_field() ?>
                        <div class="col-md-7"><input class="form-control" name="customer_name" placeholder="Müşteri adı" required></div>
                        <div class="col-md-5"><select class="form-select" name="rating">
                            <?php for ($i=5; $i>=1; $i--): ?>
                            <option value="<?= $i ?>"><?= str_repeat('★', $i) ?></option>
                            <?php endfor; ?>
                        </select></div>
                        <div class="col-12"><textarea class="form-control" name="comment" rows="3" placeholder="Yorum metni" required></textarea></div>
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <div class="form-check"><input type="checkbox" name="status" value="1" class="form-check-input" id="newRv" checked><label class="form-check-label" for="newRv">Yayında</label></div>
                            <button class="btn btn-primary btn-sm"><i class="bi bi-plus me-1"></i> Ekle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
