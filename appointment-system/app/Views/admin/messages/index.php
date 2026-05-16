<?php require APP_PATH . '/Views/admin/partials/header.php';
$activeChannel = $_GET['channel'] ?? 'all';
$channels = [
    'all'      => ['Tümü',     'bi-collection', 'secondary'],
    'sms'      => ['SMS',      'bi-chat-dots',  'info'],
    'whatsapp' => ['WhatsApp', 'bi-whatsapp',   'success'],
    'mail'     => ['E-posta',  'bi-envelope',   'primary'],
];
$vars = ['{name}','{phone}','{email}','{date}','{time}','{service}','{staff}','{package}','{remaining_sessions}','{payment_amount}','{business_name}'];
?>

<div class="section-title-bar">
    <div>
        <h5>Mesaj Şablonları</h5>
        <small class="text-muted">SMS, WhatsApp ve e-posta şablonlarınızı düzenleyin</small>
    </div>
    <div class="btn-group">
        <?php foreach ($channels as $key => [$label, $icon, $btn]): ?>
        <a href="<?= admin_url('?route=messages&channel=' . $key) ?>" class="btn btn-sm btn-outline-<?= $activeChannel === $key ? $btn : 'secondary' ?> <?= $activeChannel === $key ? 'active' : '' ?>">
            <i class="bi <?= $icon ?> me-1"></i><?= $label ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <?php foreach ($templates as $tpl):
            if ($activeChannel !== 'all' && $tpl['channel'] !== $activeChannel) continue;
            $taId = 'tpl-body-' . (int)$tpl['id'];
        ?>
        <div class="panel mb-3 template-card">
            <div class="panel-header">
                <div>
                    <h6 class="mb-1">
                        <span class="badge bg-light text-dark"><i class="bi bi-<?= e($tpl['channel'] === 'sms' ? 'chat-dots' : ($tpl['channel'] === 'whatsapp' ? 'whatsapp' : 'envelope')) ?> me-1"></i><?= e(strtoupper($tpl['channel'])) ?></span>
                        <?= e($tpl['title']) ?>
                    </h6>
                    <small class="text-muted">Şablon kodu: <code><?= e($tpl['key'] ?? $tpl['code'] ?? '—') ?></code></small>
                </div>
                <span class="badge <?= !empty($tpl['status']) ? 'bg-success' : 'bg-secondary' ?>">
                    <?= !empty($tpl['status']) ? 'Aktif' : 'Pasif' ?>
                </span>
            </div>
            <form method="post" action="<?= admin_url('?route=messages/save') ?>" class="panel-body">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$tpl['id'] ?>">
                <?php if (!empty($tpl['subject']) || $tpl['channel'] === 'mail'): ?>
                <div class="mb-2">
                    <label class="form-label">Konu (E-posta)</label>
                    <input type="text" name="subject" class="form-control" value="<?= e($tpl['subject'] ?? '') ?>">
                </div>
                <?php endif; ?>
                <label class="form-label">Mesaj İçeriği</label>
                <textarea id="<?= $taId ?>" name="body" class="form-control mb-2" rows="4"><?= e($tpl['body']) ?></textarea>

                <div class="template-vars">
                    <?php foreach ($vars as $v): ?>
                    <span class="var" data-insert-var="<?= e($v) ?>" data-target="#<?= $taId ?>"><?= e($v) ?></span>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">Değişkenler mesaj gönderiminde otomatik değiştirilir.</small>
                    <button class="btn btn-primary btn-sm"><i class="bi bi-check2 me-1"></i> Şablonu Kaydet</button>
                </div>
            </form>
        </div>
        <?php endforeach; ?>
        <?php if (empty($templates)): ?>
        <div class="empty-state"><div class="icon"><i class="bi bi-chat-dots"></i></div><h6>Şablon bulunamadı</h6></div>
        <?php endif; ?>
    </div>

    <div class="col-xl-4">
        <div class="panel mb-3">
            <div class="panel-header"><h6><i class="bi bi-eye me-1"></i> Önizleme</h6></div>
            <div class="panel-body">
                <div class="surface-soft p-3 rounded">
                    <small class="text-muted text-uppercase">Örnek</small>
                    <p class="mb-0 mt-1">
                        Merhaba <strong>Ahmet Bey</strong>, <?= date('d.m.Y') ?> tarihinde saat <strong>10:00</strong> için olan
                        <strong>Cilt Bakımı</strong> randevunuz onaylanmıştır. Sizi <strong>Ayşe Hanım</strong> karşılayacaktır.
                    </p>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header"><h6><i class="bi bi-braces me-1"></i> Kullanılabilir Değişkenler</h6></div>
            <div class="panel-body">
                <div class="template-vars">
                    <?php foreach ($vars as $v): ?>
                    <span class="var"><?= e($v) ?></span>
                    <?php endforeach; ?>
                </div>
                <small class="form-help mt-3 d-block">Mesaj alanında imleci nereye yerleştirirseniz, değişken oraya eklenir.</small>
            </div>
        </div>
    </div>
</div>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
