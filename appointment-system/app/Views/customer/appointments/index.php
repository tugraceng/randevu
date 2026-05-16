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
                    <td><?= status_badge($a['status']) ?></td>
                    <td><?= status_badge($a['payment_status'] ?? 'not_required') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($appointments)): ?>
                <tr><td colspan="5">
                    <div class="empty-state">
                        <div class="icon"><i class="bi bi-calendar-x"></i></div>
                        <h6>Henüz randevunuz yok</h6>
                        <p>İlk randevunuzu oluşturmak için tek dokunuş yeterli.</p>
                        <a href="<?= customer_url('?route=appointments/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-calendar-plus me-1"></i> Randevu Oluştur</a>
                    </div>
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require APP_PATH . '/Views/customer/partials/footer.php'; ?>
