<?php
require APP_PATH . '/Views/admin/partials/header.php';
$a = $appointment;
$redirect = e(admin_url('?route=appointments/show&id=' . (int)$a['id']));
$quickStatuses = [
    'approved'  => ['btn' => 'success',  'icon' => 'bi-check2', 'label' => 'Onayla'],
    'completed' => ['btn' => 'primary',  'icon' => 'bi-check-all', 'label' => 'Tamamlandı'],
    'cancelled' => ['btn' => 'secondary','icon' => 'bi-x',     'label' => 'İptal'],
    'no_show'   => ['btn' => 'danger',   'icon' => 'bi-person-x', 'label' => 'Gelmedi'],
];
?>

<div class="section-title-bar">
    <div>
        <h5>Randevu #<?= (int)$a['id'] ?></h5>
        <small class="text-muted">Detay ve hızlı işlemler</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= admin_url('?route=appointments') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Listeye Dön</a>
        <a href="<?= admin_url('?route=appointments/edit&id=' . (int)$a['id']) ?>" class="btn btn-primary"><i class="bi bi-pencil me-1"></i> Düzenle</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="panel mb-4">
            <div class="panel-header">
                <h6>Randevu Bilgileri</h6>
                <div class="d-flex gap-1">
                    <?= status_badge($a['status']) ?>
                    <?= status_badge($a['payment_status']) ?>
                </div>
            </div>
            <div class="panel-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted text-uppercase d-block mb-1">Müşteri</small>
                        <strong class="d-block"><?= e($a['first_name'] . ' ' . $a['last_name']) ?></strong>
                        <small class="text-muted"><i class="bi bi-telephone me-1"></i><?= e($a['phone'] ?? '-') ?></small><br>
                        <small class="text-muted"><i class="bi bi-envelope me-1"></i><?= e($a['email'] ?? '-') ?></small>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted text-uppercase d-block mb-1">Tarih &amp; Saat</small>
                        <strong class="d-block"><?= format_date($a['appointment_date']) ?></strong>
                        <small class="text-muted">
                            <i class="bi bi-clock me-1"></i><?= format_time($a['start_time']) ?> — <?= format_time($a['end_time']) ?>
                        </small>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted text-uppercase d-block mb-1">Hizmet</small>
                        <strong><?= e($a['service_name']) ?></strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted text-uppercase d-block mb-1">Personel</small>
                        <strong><?= e($a['staff_name'] ?? 'Atanmamış') ?></strong>
                    </div>
                    <?php if (!empty($a['customer_package_id'])): ?>
                    <div class="col-sm-6">
                        <small class="text-muted text-uppercase d-block mb-1">Paket</small>
                        <span class="chip"><i class="bi bi-box-seam"></i><?= e($a['package_name'] ?? 'Paket') ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($a['deposit_amount'])): ?>
                    <div class="col-sm-6">
                        <small class="text-muted text-uppercase d-block mb-1">Kapora</small>
                        <strong><?= format_money((float)$a['deposit_amount']) ?></strong>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($a['notes'])): ?>
                    <div class="col-12">
                        <small class="text-muted text-uppercase d-block mb-1">Müşteri Notu</small>
                        <div class="surface-soft p-3"><?= nl2br(e($a['notes'])) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h6><i class="bi bi-journal-text me-1"></i> Admin Notu</h6>
            </div>
            <form method="post" action="<?= admin_url('?route=appointments/note') ?>" class="panel-body">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                <input type="hidden" name="redirect" value="<?= $redirect ?>">
                <textarea name="notes" class="form-control mb-3" rows="3" placeholder="Bu randevuyla ilgili dahili notunuz"><?= e($a['admin_note'] ?? $a['notes'] ?? '') ?></textarea>
                <button class="btn btn-soft btn-sm">Notu Kaydet</button>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="panel mb-3">
            <div class="panel-header"><h6><i class="bi bi-lightning me-1"></i> Durum İşlemleri</h6></div>
            <div class="panel-body d-grid gap-2">
                <?php foreach ($quickStatuses as $st => $cfg): if (($a['status'] ?? '') === $st) continue; ?>
                <form method="post" action="<?= admin_url('?route=appointments/status') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                    <input type="hidden" name="status" value="<?= $st ?>">
                    <input type="hidden" name="redirect" value="<?= $redirect ?>">
                    <button class="btn btn-outline-<?= e($cfg['btn']) ?> w-100" data-confirm="<?= e($cfg['label']) ?> işlemi uygulansın mı?">
                        <i class="bi <?= e($cfg['icon']) ?> me-1"></i> <?= e($cfg['label']) ?>
                    </button>
                </form>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="panel mb-3">
            <div class="panel-header"><h6><i class="bi bi-cash-coin me-1"></i> Ödeme</h6></div>
            <div class="panel-body">
                <form method="post" action="<?= admin_url('?route=appointments/paid') ?>" class="mb-2">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                    <input type="hidden" name="redirect" value="<?= $redirect ?>">
                    <button class="btn btn-success w-100"><i class="bi bi-check2 me-1"></i> Ödeme Alındı</button>
                </form>
                <small class="text-muted d-block">Durum: <?= status_badge($a['payment_status']) ?></small>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header"><h6><i class="bi bi-megaphone me-1"></i> Bildirim Gönder</h6></div>
            <div class="panel-body d-grid gap-2">
                <?php foreach (['sms' => ['SMS Hatırlat', 'bi-chat-dots'], 'whatsapp' => ['WhatsApp Gönder', 'bi-whatsapp'], 'mail' => ['E-posta Gönder', 'bi-envelope']] as $ch => $info): ?>
                <form method="post" action="<?= admin_url('?route=appointments/message') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="appointment_id" value="<?= (int)$a['id'] ?>">
                    <input type="hidden" name="channel" value="<?= e($ch) ?>">
                    <input type="hidden" name="redirect" value="<?= $redirect ?>">
                    <button class="btn btn-outline-info w-100"><i class="bi <?= e($info[1]) ?> me-1"></i><?= e($info[0]) ?></button>
                </form>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
