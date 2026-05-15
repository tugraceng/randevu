<?php require APP_PATH . '/Views/admin/partials/header.php'; ?>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card stat-card p-3"><small class="text-muted">Toplam Müşteri</small><div class="stat-value"><?= number_format($total_customers) ?></div></div></div>
    <div class="col-md-3"><div class="card stat-card p-3"><small class="text-muted">Aktif Paketler</small><div class="stat-value"><?= number_format($active_packages) ?></div></div></div>
    <div class="col-md-3"><div class="card stat-card p-3"><small class="text-muted">Aylık Gelir</small><div class="stat-value"><?= format_money($revenue) ?></div></div></div>
    <div class="col-md-3"><div class="card stat-card p-3"><small class="text-muted text-danger">Kritik Seans</small><div class="stat-value text-danger"><?= $critical ?></div></div></div>
</div>
<div class="table-card p-3 mb-4"><h6>Müşteri Listesi</h6>
<table class="table"><thead><tr><th>Müşteri</th><th>Aktif Paket</th><th>Kalan Seans</th><th>Ödeme</th></tr></thead>
<tbody><?php foreach ($customers['data'] as $c): ?>
<tr><td><strong><?= e($c['first_name'].' '.$c['last_name']) ?></strong><br><small><?= e($c['email']) ?></small></td>
<td><?= e($c['active_package'] ?? '-') ?></td>
<td><?php $rem=(int)($c['remaining_sessions']??0); ?>
<div class="progress" style="height:8px"><div class="progress-bar bg-<?= $rem<=1?'danger':'primary' ?>" style="width:<?= min(100,$rem*10) ?>%"></div></div>
<small><?= $rem ?> seans</small></td>
<td><span class="badge bg-secondary"><?= e($c['last_payment_status'] ?? '-') ?></span></td></tr>
<?php endforeach; ?></tbody></table></div>
<div class="card p-3"><h6>Yeni Paket Tanımla</h6>
<form method="post" action="<?= admin_url('?route=customers/package') ?>"><?= csrf_field() ?>
<div class="row g-2">
<div class="col-md-4"><select name="customer_id" class="form-select" required><?php foreach ($customers['data'] as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['first_name'].' '.$c['last_name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><select name="package_id" class="form-select" required><?php foreach ($packages as $p): ?><option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><select name="payment_method" class="form-select"><option value="manual">Havale/EFT</option><option value="online">Online (Kart)</option></select></div>
<div class="col-12"><button class="btn btn-primary">Paketi Oluştur</button></div></div></form></div>
<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
