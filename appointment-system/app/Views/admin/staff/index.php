<?php
require APP_PATH . '/Views/admin/partials/header.php';
$isEdit = !empty($edit);
$formHours = $edit_hours ?? [];
?>

<div class="section-title-bar">
    <div>
        <h5>Personel Yönetimi</h5>
        <small class="text-muted">Ekibinizi yönetin, çalışma saatlerini ve hizmet atamalarını tanımlayın</small>
    </div>
    <a href="<?= admin_url('?route=staff') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Yeni Personel</a>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($staff as $m): ?>
    <div class="col-md-6 col-xl-4">
        <div class="panel hover-lift h-100">
            <div class="panel-body d-flex gap-3">
                <?php if (!empty($m['photo'])): ?>
                <img src="<?= base_url(e($m['photo'])) ?>" alt="" class="rounded" style="width:84px;height:84px;object-fit:cover;flex-shrink:0;">
                <?php else: ?>
                <div class="rounded d-flex align-items-center justify-content-center" style="width:84px;height:84px;background:var(--primary-soft);color:var(--primary);flex-shrink:0;">
                    <i class="bi bi-person fs-2"></i>
                </div>
                <?php endif; ?>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-0"><?= e($m['name']) ?></h6>
                            <small class="text-muted"><?= e($m['title'] ?? '') ?></small>
                        </div>
                        <span class="status-pill <?= !empty($m['status']) ? 'status-approved' : 'status-cancelled' ?>">
                            <?= !empty($m['status']) ? 'Aktif' : 'Pasif' ?>
                        </span>
                    </div>
                    <?php if (!empty($m['specialty'])): ?>
                    <div class="d-flex flex-wrap gap-1 mt-2">
                        <?php foreach (array_slice(array_filter(array_map('trim', explode(',', $m['specialty']))), 0, 3) as $sp): ?>
                            <span class="chip"><?= e($sp) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <div class="mt-3 d-flex gap-1 flex-wrap">
                        <a href="<?= admin_url('?route=staff&edit=' . (int)$m['id']) ?>" class="btn btn-soft btn-sm"><i class="bi bi-pencil me-1"></i>Düzenle</a>
                        <?php if (!empty($m['phone'])): ?>
                        <a href="tel:<?= e($m['phone']) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-telephone"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($staff)): ?>
    <div class="col-12">
        <div class="empty-state">
            <div class="icon"><i class="bi bi-people"></i></div>
            <h6>Henüz personel eklenmemiş</h6>
            <p>Aşağıdaki formu kullanarak ekibinizdeki ilk personeli ekleyin. Personel için fotoğraf, uzmanlık ve haftalık çalışma saatlerini tanımlayabilirsiniz.</p>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="panel">
    <div class="panel-header">
        <h6><i class="bi bi-<?= $isEdit ? 'pencil-square' : 'person-plus' ?> me-1"></i>
            <?= $isEdit ? 'Personel Düzenle: ' . e($edit['name']) : 'Yeni Personel Ekle' ?>
        </h6>
        <?php if ($isEdit): ?>
            <a href="<?= admin_url('?route=staff') ?>" class="btn btn-sm btn-outline-secondary">İptal</a>
        <?php endif; ?>
    </div>
    <form method="post" action="<?= admin_url('?route=staff/save') ?>" enctype="multipart/form-data" class="panel-body">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="upload-card">
                    <?php if ($isEdit && !empty($edit['photo'])): ?>
                    <img src="<?= base_url(e($edit['photo'])) ?>" class="rounded mb-2" style="max-height:140px;" alt="">
                    <div class="form-check d-block">
                        <input type="checkbox" name="remove_photo" value="1" class="form-check-input" id="rmPhoto">
                        <label class="form-check-label small" for="rmPhoto">Fotoğrafı kaldır</label>
                    </div>
                    <?php else: ?>
                    <i class="bi bi-camera fs-2 text-muted mb-2 d-block"></i>
                    <small class="text-muted d-block mb-2">PNG / JPG, max 2MB</small>
                    <?php endif; ?>
                    <input type="file" name="photo" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp,image/gif">
                </div>
            </div>
            <div class="col-md-8">
                <div class="row g-2">
                    <div class="col-md-6"><label class="form-label">Ad Soyad *</label><input name="name" class="form-control" value="<?= e($edit['name'] ?? '') ?>" required></div>
                    <div class="col-md-6"><label class="form-label">Ünvan</label><input name="title" class="form-control" value="<?= e($edit['title'] ?? '') ?>" placeholder="Örn: Cilt Bakım Uzmanı"></div>
                    <div class="col-md-6"><label class="form-label">Telefon</label><input name="phone" class="form-control" value="<?= e($edit['phone'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">E-posta</label><input name="email" type="email" class="form-control" value="<?= e($edit['email'] ?? '') ?>"></div>
                    <div class="col-12"><label class="form-label">Uzmanlık (virgülle ayırın)</label><input name="specialty" class="form-control" value="<?= e($edit['specialty'] ?? '') ?>" placeholder="Estetik, Cilt Bakımı, Manikür"></div>
                    <div class="col-12"><label class="form-label">Bio / Açıklama</label><textarea name="bio" class="form-control" rows="2"><?= e($edit['bio'] ?? '') ?></textarea></div>
                    <div class="col-md-4">
                        <label class="form-label">Durum</label>
                        <select name="status" class="form-select">
                            <option value="1" <?= ($edit['status'] ?? 1) ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= isset($edit['status']) && !$edit['status'] ? 'selected' : '' ?>>Pasif</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <fieldset class="form-fieldset">
                    <legend>Bağlı Hizmetler</legend>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($services as $svc): ?>
                        <label class="btn btn-sm btn-outline-secondary">
                            <input type="checkbox" name="service_ids[]" value="<?= $svc['id'] ?>" class="me-1"
                                <?= in_array($svc['id'], $edit_service_ids ?? [], true) ? 'checked' : '' ?>>
                            <?= e($svc['name']) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>
            </div>

            <div class="col-12">
                <fieldset class="form-fieldset">
                    <legend>Çalışma Saatleri</legend>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Gün</th>
                                    <th style="width:120px;">Durum</th>
                                    <th style="width:160px;">Başlangıç</th>
                                    <th style="width:160px;">Bitiş</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($day_labels as $dow => $dayName): $h = $formHours[$dow] ?? []; $closed = !empty($h['is_closed']); ?>
                                <tr>
                                    <td><strong><?= e($dayName) ?></strong></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="hours[<?= $dow ?>][is_closed]" value="1" class="form-check-input day-closed"
                                                <?= $closed ? 'checked' : '' ?> data-day="<?= $dow ?>" id="closed<?= $dow ?>">
                                            <label class="form-check-label small" for="closed<?= $dow ?>">Kapalı</label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="time" name="hours[<?= $dow ?>][start_time]" class="form-control form-control-sm day-start"
                                            value="<?= e($h['start_time'] ?? '09:00') ?>" data-day="<?= $dow ?>">
                                    </td>
                                    <td>
                                        <input type="time" name="hours[<?= $dow ?>][end_time]" class="form-control form-control-sm day-end"
                                            value="<?= e($h['end_time'] ?? '18:00') ?>" data-day="<?= $dow ?>">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Personel özel saat tanımlanmazsa genel işletme saatleri kullanılır.</small>
                </fieldset>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2">
                <?php if ($isEdit): ?><a href="<?= admin_url('?route=staff') ?>" class="btn btn-outline-secondary">İptal</a><?php endif; ?>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i><?= $isEdit ? 'Güncelle' : 'Personel Ekle' ?></button>
            </div>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('.day-closed').forEach(cb => {
    const toggle = () => {
        const d = cb.dataset.day;
        const disabled = cb.checked;
        document.querySelectorAll('.day-start[data-day="'+d+'"],.day-end[data-day="'+d+'"]').forEach(el => {
            el.disabled = disabled;
        });
    };
    cb.addEventListener('change', toggle);
    toggle();
});
</script>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
