<?php require APP_PATH . '/Views/admin/partials/header.php'; $c = $customer; ?>
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-general">Genel</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-appointments">Randevular</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-packages">Paketler</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-payments">Ödemeler</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-messages">Mesajlar</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-notes">Notlar</button></li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="tab-general">
        <form method="post" action="<?= admin_url('?route=customers/save') ?>" class="table-card p-4">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $c['id'] ?>">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Ad</label><input name="first_name" class="form-control" value="<?= e($c['first_name']) ?>" required></div>
                <div class="col-md-6"><label class="form-label">Soyad</label><input name="last_name" class="form-control" value="<?= e($c['last_name']) ?>" required></div>
                <div class="col-md-6"><label class="form-label">E-posta</label><input name="email" type="email" class="form-control" value="<?= e($c['email']) ?>"></div>
                <div class="col-md-6"><label class="form-label">Telefon</label><input name="phone" class="form-control" value="<?= e($c['phone'] ?? '') ?>"></div>
                <div class="col-12"><button class="btn btn-primary">Kaydet</button></div>
            </div>
        </form>
        <form method="post" action="<?= admin_url('?route=customers/blacklist') ?>" class="mt-2">
            <?= csrf_field() ?><input type="hidden" name="customer_id" value="<?= $c['id'] ?>">
            <input type="hidden" name="blacklist" value="<?= $c['is_blacklisted'] ? '0' : '1' ?>">
            <button class="btn btn-sm btn-outline-danger" data-confirm="Kara liste güncellensin mi?"><?= $c['is_blacklisted'] ? 'Kara listeden çıkar' : 'Kara listeye al' ?></button>
        </form>
        <a href="<?= admin_url('?route=appointments/create&customer_id=' . $c['id']) ?>" class="btn btn-sm btn-success mt-2">Randevu Oluştur</a>
    </div>
    <div class="tab-pane fade" id="tab-appointments">
        <div class="table-card p-3"><table class="table table-sm"><thead><tr><th>Tarih</th><th>Hizmet</th><th>Durum</th></tr></thead><tbody>
        <?php foreach ($appointments as $a): ?><tr><td><?= format_date($a['appointment_date']) ?></td><td><?= e($a['service_name']) ?></td><td><?= status_badge($a['status']) ?></td></tr><?php endforeach; ?>
        </tbody></table></div>
    </div>
    <div class="tab-pane fade" id="tab-packages">
        <?php foreach ($packages as $p): ?>
        <div class="border rounded p-2 mb-2 d-flex justify-content-between">
            <span><?= e($p['package_name']) ?> — <?= (int)$p['remaining_sessions'] ?> / <?= (int)$p['total_sessions'] ?> seans</span>
            <a href="<?= admin_url('?route=packages/show&id=' . $p['id']) ?>">Detay</a>
        </div>
        <?php endforeach; ?>
        <form method="post" action="<?= admin_url('?route=customers/package') ?>" class="table-card p-3 mt-3">
            <?= csrf_field() ?><input type="hidden" name="customer_id" value="<?= $c['id'] ?>">
            <select name="package_id" class="form-select mb-2"><?php foreach ($packages_catalog as $pk): ?><option value="<?= $pk['id'] ?>"><?= e($pk['name']) ?></option><?php endforeach; ?></select>
            <select name="payment_method" class="form-select mb-2"><option value="manual">Manuel</option><option value="online">Online</option></select>
            <button class="btn btn-primary btn-sm">Paket Ata</button>
        </form>
    </div>
    <div class="tab-pane fade" id="tab-payments">
        <div class="table-card p-3"><table class="table table-sm"><thead><tr><th>#</th><th>Tutar</th><th>Durum</th></tr></thead><tbody>
        <?php foreach ($payments as $p): ?><tr><td><?= $p['id'] ?></td><td><?= format_money((float)$p['amount']) ?></td><td><?= status_badge($p['status']) ?></td></tr><?php endforeach; ?>
        </tbody></table></div>
    </div>
    <div class="tab-pane fade" id="tab-messages">
        <?php foreach ($messages as $m): ?>
        <div class="small border-bottom py-1"><?= e($m['channel']) ?> → <?= e($m['recipient']) ?> (<?= e($m['status']) ?>) <?= format_date($m['created_at']) ?></div>
        <?php endforeach; ?>
    </div>
    <div class="tab-pane fade" id="tab-notes">
        <?php foreach ($notes as $n): ?><div class="border rounded p-2 mb-2 small"><?= e($n['note']) ?><br><span class="text-muted"><?= e($n['created_at']) ?></span></div><?php endforeach; ?>
        <form method="post" action="<?= admin_url('?route=customers/note') ?>" class="mt-2">
            <?= csrf_field() ?><input type="hidden" name="customer_id" value="<?= $c['id'] ?>">
            <textarea name="note" class="form-control mb-2" rows="2" required></textarea>
            <button class="btn btn-sm btn-primary">Not Ekle</button>
        </form>
    </div>
</div>
<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
