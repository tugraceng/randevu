<?php
require APP_PATH . '/Views/admin/partials/header.php';
$c = $customer;
$fullName = trim($c['first_name'] . ' ' . $c['last_name']);
$initial = strtoupper(mb_substr($c['first_name'] ?? '?', 0, 1));
$totalAppointments = count($appointments);
$activeP = array_values(array_filter($packages, fn($p) => ($p['status'] ?? '') === 'active'));
$remainingSum = array_sum(array_column($activeP, 'remaining_sessions'));
$verified = !empty($c['email_verified_at']);
?>

<div class="customer-hero">
    <div class="avatar"><?= e($initial) ?></div>
    <div class="flex-grow-1">
        <h4><?= e($fullName) ?></h4>
        <div class="meta">
            <span class="me-3"><i class="bi bi-telephone me-1"></i><?= e($c['phone'] ?? '-') ?></span>
            <span class="me-3"><i class="bi bi-envelope me-1"></i><?= e($c['email']) ?></span>
            <?php if ($verified): ?>
                <span class="chip"><i class="bi bi-shield-check"></i>Doğrulanmış</span>
            <?php else: ?>
                <span class="chip chip-muted"><i class="bi bi-shield-x"></i>Doğrulanmamış</span>
            <?php endif; ?>
            <?php if (!empty($c['sms_permission'])): ?><span class="chip ms-1"><i class="bi bi-chat"></i>SMS</span><?php endif; ?>
            <?php if (!empty($c['whatsapp_permission'])): ?><span class="chip ms-1"><i class="bi bi-whatsapp"></i>WhatsApp</span><?php endif; ?>
            <?php if (!empty($c['is_blacklisted'])): ?><span class="chip" style="background:rgba(239,68,68,.12); color:var(--danger);"><i class="bi bi-slash-circle"></i>Kara Liste</span><?php endif; ?>
        </div>
    </div>
    <div class="d-flex">
        <div class="stat"><strong><?= $totalAppointments ?></strong><small>Randevu</small></div>
        <div class="stat"><strong><?= count($activeP) ?></strong><small>Aktif Paket</small></div>
        <div class="stat"><strong><?= $remainingSum ?></strong><small>Kalan Seans</small></div>
    </div>
</div>

<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="<?= admin_url('?route=appointments/create&customer_id=' . (int)$c['id']) ?>" class="btn btn-primary"><i class="bi bi-calendar-plus me-1"></i> Randevu Oluştur</a>
    <button class="btn btn-soft" data-bs-toggle="tab" data-bs-target="#tab-packages" type="button"><i class="bi bi-box me-1"></i> Paket Ata</button>
    <form method="post" action="<?= admin_url('?route=customers/message') ?>" class="d-inline">
        <?= csrf_field() ?><input type="hidden" name="customer_id" value="<?= (int)$c['id'] ?>"><input type="hidden" name="channel" value="sms">
        <button class="btn btn-outline-info"><i class="bi bi-chat-dots me-1"></i> SMS Gönder</button>
    </form>
    <form method="post" action="<?= admin_url('?route=customers/message') ?>" class="d-inline">
        <?= csrf_field() ?><input type="hidden" name="customer_id" value="<?= (int)$c['id'] ?>"><input type="hidden" name="channel" value="whatsapp">
        <button class="btn btn-outline-success"><i class="bi bi-whatsapp me-1"></i> WhatsApp</button>
    </form>
    <form method="post" action="<?= admin_url('?route=customers/blacklist') ?>" class="d-inline">
        <?= csrf_field() ?><input type="hidden" name="customer_id" value="<?= (int)$c['id'] ?>">
        <input type="hidden" name="blacklist" value="<?= $c['is_blacklisted'] ? '0' : '1' ?>">
        <button class="btn btn-outline-danger" data-confirm="<?= $c['is_blacklisted'] ? 'Kara listeden çıkarılsın mı?' : 'Bu müşteri kara listeye alınsın mı?' ?>">
            <i class="bi bi-slash-circle me-1"></i> <?= $c['is_blacklisted'] ? 'Kara listeden çıkar' : 'Kara listeye al' ?>
        </button>
    </form>
</div>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-general" type="button"><i class="bi bi-person me-1"></i>Genel</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-appointments" type="button"><i class="bi bi-calendar me-1"></i>Randevular (<?= $totalAppointments ?>)</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-packages" type="button"><i class="bi bi-box me-1"></i>Paketler (<?= count($packages) ?>)</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-payments" type="button"><i class="bi bi-cash-coin me-1"></i>Ödemeler (<?= count($payments) ?>)</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-messages" type="button"><i class="bi bi-chat-dots me-1"></i>Mesajlar (<?= count($messages) ?>)</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-notes" type="button"><i class="bi bi-journal me-1"></i>Notlar (<?= count($notes) ?>)</button></li>
</ul>

<div class="tab-content">
    <!-- GENERAL ============================================== -->
    <div class="tab-pane fade show active" id="tab-general">
        <div class="panel">
            <div class="panel-header"><h6>Müşteri Bilgileri</h6></div>
            <form method="post" action="<?= admin_url('?route=customers/save') ?>" class="panel-body row g-3">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                <div class="col-md-6"><label class="form-label">Ad</label><input name="first_name" class="form-control" value="<?= e($c['first_name']) ?>" required></div>
                <div class="col-md-6"><label class="form-label">Soyad</label><input name="last_name" class="form-control" value="<?= e($c['last_name']) ?>" required></div>
                <div class="col-md-6"><label class="form-label">E-posta</label><input name="email" type="email" class="form-control" value="<?= e($c['email']) ?>"></div>
                <div class="col-md-6"><label class="form-label">Telefon</label><input name="phone" class="form-control" value="<?= e($c['phone'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label">Doğum Tarihi</label><input name="birthdate" type="date" class="form-control" value="<?= e($c['birthdate'] ?? '') ?>"></div>
                <div class="col-md-8 d-flex align-items-end gap-3">
                    <div class="form-check"><input type="checkbox" name="sms_permission" value="1" class="form-check-input" id="cSms" <?= !empty($c['sms_permission']) ? 'checked' : '' ?>><label class="form-check-label" for="cSms">SMS</label></div>
                    <div class="form-check"><input type="checkbox" name="whatsapp_permission" value="1" class="form-check-input" id="cWa" <?= !empty($c['whatsapp_permission']) ? 'checked' : '' ?>><label class="form-check-label" for="cWa">WhatsApp</label></div>
                    <div class="form-check"><input type="checkbox" name="marketing_permission" value="1" class="form-check-input" id="cMkt" <?= !empty($c['marketing_permission']) ? 'checked' : '' ?>><label class="form-check-label" for="cMkt">Pazarlama</label></div>
                </div>
                <div class="col-12"><button class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Kaydet</button></div>
            </form>
        </div>
    </div>

    <!-- APPOINTMENTS ========================================= -->
    <div class="tab-pane fade" id="tab-appointments">
        <div class="panel">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Tarih</th><th>Hizmet</th><th>Personel</th><th>Durum</th><th>Ödeme</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach ($appointments as $a): ?>
                        <tr>
                            <td><?= format_date($a['appointment_date']) ?><br><small class="text-muted"><?= format_time($a['start_time']) ?></small></td>
                            <td><?= e($a['service_name']) ?></td>
                            <td><?= e($a['staff_name'] ?? '-') ?></td>
                            <td><?= status_badge($a['status']) ?></td>
                            <td><?= status_badge($a['payment_status']) ?></td>
                            <td class="text-end"><a href="<?= admin_url('?route=appointments/show&id=' . (int)$a['id']) ?>" class="btn btn-soft btn-sm"><i class="bi bi-eye"></i></a></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($appointments)): ?>
                        <tr><td colspan="6"><div class="empty-state"><div class="icon"><i class="bi bi-calendar-x"></i></div><h6>Henüz randevu yok</h6></div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PACKAGES ============================================= -->
    <div class="tab-pane fade" id="tab-packages">
        <div class="row g-3 mb-3">
            <?php foreach ($packages as $p):
                $tot = (int)($p['total_sessions'] ?? 0);
                $rem = (int)($p['remaining_sessions'] ?? 0);
                $used = max(0, $tot - $rem);
                $pct = $tot ? round($used / $tot * 100) : 0;
            ?>
            <div class="col-md-6">
                <div class="panel">
                    <div class="panel-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="mb-0"><?= e($p['package_name']) ?></h6>
                                <small class="text-muted"><?= e($p['service_name'] ?? '') ?></small>
                            </div>
                            <?= status_badge($p['status']) ?>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between small mb-1"><span><?= $used ?>/<?= $tot ?> seans</span><span><?= $rem ?> kalan</span></div>
                            <div class="session-progress <?= $rem <= 1 ? 'danger' : ($rem <= 3 ? 'warning' : '') ?>">
                                <div class="bar" style="width: <?= $pct ?>%"></div>
                            </div>
                        </div>
                        <a href="<?= admin_url('?route=packages/show&id=' . (int)$p['id']) ?>" class="btn btn-soft btn-sm mt-3">Paket detay</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="panel">
            <div class="panel-header"><h6>Paket Ata</h6></div>
            <form method="post" action="<?= admin_url('?route=customers/package') ?>" class="panel-body row g-3">
                <?= csrf_field() ?><input type="hidden" name="customer_id" value="<?= (int)$c['id'] ?>">
                <div class="col-md-6">
                    <label class="form-label">Paket</label>
                    <select name="package_id" class="form-select">
                        <?php foreach ($packages_catalog as $pk): ?>
                        <option value="<?= $pk['id'] ?>"><?= e($pk['name']) ?> · <?= format_money((float)$pk['price']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ödeme Yöntemi</label>
                    <select name="payment_method" class="form-select">
                        <option value="manual">Manuel</option>
                        <option value="online">Online</option>
                    </select>
                </div>
                <div class="col-12"><button class="btn btn-primary">Paket Ata</button></div>
            </form>
        </div>
    </div>

    <!-- PAYMENTS ============================================= -->
    <div class="tab-pane fade" id="tab-payments">
        <div class="panel">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>#</th><th>Tutar</th><th>Sağlayıcı</th><th>Durum</th><th>Tarih</th></tr></thead>
                    <tbody>
                        <?php foreach ($payments as $p): ?>
                        <tr>
                            <td>#<?= (int)$p['id'] ?></td>
                            <td><strong><?= format_money((float)$p['amount']) ?></strong></td>
                            <td><?= e($p['provider'] ?? 'manual') ?></td>
                            <td><?= status_badge($p['status']) ?></td>
                            <td><?= format_date($p['paid_at'] ?? $p['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($payments)): ?>
                        <tr><td colspan="5"><div class="empty-state"><div class="icon"><i class="bi bi-cash-coin"></i></div><h6>Ödeme bulunamadı</h6></div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MESSAGES ============================================= -->
    <div class="tab-pane fade" id="tab-messages">
        <div class="panel">
            <div class="panel-body">
                <?php foreach ($messages as $m): ?>
                <div class="d-flex justify-content-between align-items-start py-2 border-bottom">
                    <div>
                        <span class="chip"><?= e(strtoupper($m['channel'])) ?></span>
                        <span class="ms-2 small text-muted"><?= e($m['recipient']) ?></span>
                        <div class="small mt-1"><?= e($m['body'] ?? $m['subject'] ?? '') ?></div>
                    </div>
                    <div class="text-end">
                        <?= status_badge($m['status']) ?>
                        <small class="text-muted d-block"><?= format_date($m['created_at']) ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($messages)): ?>
                <div class="empty-state"><div class="icon"><i class="bi bi-chat-dots"></i></div><h6>Mesaj geçmişi yok</h6></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- NOTES ================================================ -->
    <div class="tab-pane fade" id="tab-notes">
        <div class="panel">
            <div class="panel-body">
                <div class="timeline mb-4">
                    <?php foreach ($notes as $n): ?>
                    <div class="timeline-item">
                        <div class="time"><?= format_date($n['created_at']) ?></div>
                        <div><?= nl2br(e($n['note'])) ?></div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($notes)): ?>
                        <div class="empty-state"><div class="icon"><i class="bi bi-journal"></i></div><h6>Henüz not yok</h6></div>
                    <?php endif; ?>
                </div>
                <form method="post" action="<?= admin_url('?route=customers/note') ?>">
                    <?= csrf_field() ?><input type="hidden" name="customer_id" value="<?= (int)$c['id'] ?>">
                    <textarea name="note" class="form-control mb-2" rows="3" placeholder="Müşteri ile ilgili dahili not..." required></textarea>
                    <button class="btn btn-primary btn-sm"><i class="bi bi-plus me-1"></i> Not Ekle</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
