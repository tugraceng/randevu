<?php require APP_PATH . '/Views/customer/partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0">Ödemelerim</h5>
        <small class="text-muted">Tüm ödeme hareketleriniz</small>
    </div>
</div>

<div class="c-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Tarih</th>
                    <th>Açıklama</th>
                    <th>Tutar</th>
                    <th>Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $p): ?>
                <tr>
                    <td><?= format_date($p['paid_at'] ?? $p['created_at']) ?></td>
                    <td>
                        <strong><?= e($p['package_name'] ?? $p['service_name'] ?? $p['payment_type']) ?></strong>
                        <?php if (!empty($p['provider'])): ?>
                        <small class="text-muted d-block"><?= e($p['provider']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= format_money((float)$p['amount']) ?></strong></td>
                    <td>
                        <span class="badge bg-<?= $p['status']==='paid'?'success':($p['status']==='failed'?'danger':'warning') ?>">
                            <?= e($p['status']) ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($payments)): ?>
                <tr><td colspan="4"><div class="empty-state"><i class="bi bi-credit-card"></i><br>Henüz ödeme yok</div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require APP_PATH . '/Views/customer/partials/footer.php'; ?>
