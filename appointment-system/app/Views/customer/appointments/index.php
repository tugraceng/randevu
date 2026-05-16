<?php require APP_PATH . '/Views/customer/partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0">Randevularım</h5>
        <small class="text-muted">Geçmiş ve yaklaşan tüm randevularınız</small>
    </div>
    <a href="<?= customer_url('?route=appointments/create') ?>" class="btn btn-primary"><i class="bi bi-calendar-plus me-1"></i> Yeni Randevu</a>
</div>

<div class="c-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Hizmet</th>
                    <th>Personel</th>
                    <th>Tarih &amp; Saat</th>
                    <th>Durum</th>
                    <th>Ödeme</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $a): ?>
                <tr>
                    <td><strong><?= e($a['service_name']) ?></strong></td>
                    <td><?= e($a['staff_name'] ?? '—') ?></td>
                    <td>
                        <?= format_date($a['appointment_date']) ?><br>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i><?= format_time($a['start_time']) ?></small>
                    </td>
                    <td><span class="badge bg-<?= $a['status']==='completed'?'success':($a['status']==='cancelled'?'secondary':($a['status']==='pending'?'warning':'primary')) ?>"><?= e($a['status']) ?></span></td>
                    <td><span class="badge bg-<?= ($a['payment_status']??'')==='paid'?'success':'warning' ?>"><?= e($a['payment_status'] ?? '-') ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($appointments)): ?>
                <tr><td colspan="5"><div class="empty-state"><i class="bi bi-calendar-x"></i><br>Henüz randevunuz yok</div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require APP_PATH . '/Views/customer/partials/footer.php'; ?>
