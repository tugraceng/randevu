<?php require APP_PATH . '/Views/customer/partials/header.php'; ?>
<a href="<?= customer_url('?route=appointments/create') ?>" class="btn btn-primary mb-3">Yeni Randevu</a>
<table class="table card"><thead><tr><th>Hizmet</th><th>Tarih</th><th>Durum</th></tr></thead>
<tbody><?php foreach ($appointments as $a): ?><tr>
<td><?= e($a['service_name']) ?></td><td><?= format_date($a['appointment_date']) ?> <?= format_time($a['start_time']) ?></td>
<td><span class="badge bg-primary"><?= e($a['status']) ?></span></td></tr><?php endforeach; ?></tbody></table>
<?php require APP_PATH . '/Views/customer/partials/footer.php'; ?>
