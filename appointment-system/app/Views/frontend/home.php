<?php
$hero = $sections['hero'] ?? [];
$about = $sections['about'] ?? [];
$siteTitle = $settings['site_title'] ?? 'RandevuTakip';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($siteTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/frontend.css') ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#"><?= e($siteTitle) ?></a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#about">Hakkımızda</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Hizmetler</a></li>
                <li class="nav-item"><a class="nav-link" href="#packages">Paketler</a></li>
                <li class="nav-item"><a class="nav-link" href="#team">Ekibimiz</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">İletişim</a></li>
                <li class="nav-item"><a class="btn btn-primary ms-2" href="<?= customer_url('?route=login') ?>">Müşteri Paneli</a></li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero-section" id="home">
    <div class="container py-5 mt-5">
        <span class="badge bg-primary bg-opacity-75 mb-3"><?= e($settings['business_name'] ?? '') ?></span>
        <h1><?= e($hero['title'] ?? 'Modern Bakım, Profesyonel Hizmet') ?></h1>
        <p class="lead col-lg-8"><?= e($hero['content'] ?? '') ?></p>
        <a href="#appointment" class="btn btn-primary btn-lg me-2">Hemen Randevu Al</a>
        <a href="#services" class="btn btn-outline-light btn-lg">Hizmetlerimizi İnceleyin</a>
    </div>
</section>

<section class="py-5" id="about">
    <div class="container text-center">
        <h2 class="section-title"><?= e($about['title'] ?? 'Hakkımızda') ?></h2>
        <p class="text-muted col-lg-8 mx-auto"><?= e($about['content'] ?? '') ?></p>
    </div>
</section>

<section class="py-5 bg-light" id="services">
    <div class="container">
        <h2 class="section-title text-center mb-5">Hizmetlerimiz</h2>
        <div class="row g-4">
            <?php foreach ($services as $svc): ?>
            <div class="col-md-4">
                <div class="card service-card p-4">
                    <i class="bi bi-heart-pulse fs-1 text-primary mb-3"></i>
                    <h5><?= e($svc['name']) ?></h5>
                    <p class="text-muted small"><?= e($svc['description'] ?? '') ?></p>
                    <p class="fw-bold text-primary"><?= format_money((float)$svc['price']) ?></p>
                    <small class="text-muted"><?= (int)$svc['duration_minutes'] ?> dk</small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5" id="packages">
    <div class="container">
        <h2 class="section-title text-center mb-5">Avantajlı Paketler</h2>
        <div class="row g-4 justify-content-center">
            <?php $i = 0; foreach ($packages as $pkg): $i++; $featured = $i === 2; ?>
            <div class="col-md-4">
                <div class="card package-card p-4 text-center position-relative <?= $featured ? 'featured' : '' ?>">
                    <?php if ($featured): ?><span class="badge-popular">EN ÇOK TERCİH EDİLEN</span><?php endif; ?>
                    <h5><?= e($pkg['name']) ?></h5>
                    <p class="display-6 fw-bold text-primary"><?= format_money((float)$pkg['price']) ?></p>
                    <p class="text-muted"><?= (int)$pkg['session_count'] ?> seans · <?= (int)$pkg['validity_days'] ?> gün</p>
                    <p class="small"><?= e($pkg['description'] ?? '') ?></p>
                    <a href="<?= customer_url('?route=register') ?>" class="btn <?= $featured ? 'btn-primary' : 'btn-outline-primary' ?>">Satın Al</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="stats-bar">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3"><div class="stat-num"><?= e($settings['stat_happy_clients'] ?? '15k+') ?></div><small>Mutlu Danışan</small></div>
            <div class="col-md-3"><div class="stat-num"><?= e($settings['stat_support'] ?? '7/24') ?></div><small>Online Destek</small></div>
            <div class="col-md-3"><div class="stat-num"><?= e($settings['stat_experts'] ?? '45+') ?></div><small>Uzman Personel</small></div>
            <div class="col-md-3"><div class="stat-num"><?= e($settings['stat_awards'] ?? '12') ?></div><small>Ödüllü Merkez</small></div>
        </div>
    </div>
</section>

<section class="py-5" id="team">
    <div class="container">
        <h2 class="section-title text-center mb-5">Ekibimiz</h2>
        <div class="row g-4 justify-content-center">
            <?php foreach ($staff as $member): ?>
            <div class="col-md-4 text-center team-card">
                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px">
                    <i class="bi bi-person fs-2 text-primary"></i>
                </div>
                <h5><?= e($member['name']) ?></h5>
                <p class="text-muted"><?= e($member['title'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (!empty($campaigns)): ?>
<section class="py-5 bg-light" id="campaigns">
    <div class="container"><h2 class="section-title text-center mb-4">Kampanyalar</h2>
    <div class="row g-3"><?php foreach ($campaigns as $c): ?>
    <div class="col-md-4"><div class="card p-3"><h5><?= e($c['title']) ?></h5><p class="small"><?= e($c['description'] ?? '') ?></p></div>
    <?php endforeach; ?></div></div>
</section>
<?php endif; ?>

<section class="py-5" id="reviews">
    <div class="container"><h2 class="section-title text-center mb-4">Yorumlar</h2>
    <div class="row g-3"><?php foreach ($reviews as $r): ?>
    <div class="col-md-6"><div class="card p-3"><div class="text-warning"><?= str_repeat('★', (int)$r['rating']) ?></div><p><?= e($r['comment']) ?></p><small class="text-muted">— <?= e($r['customer_name']) ?></small></div></div>
    <?php endforeach; ?></div></div>
</section>

<section class="py-5 bg-light" id="faq">
    <div class="container"><h2 class="section-title text-center mb-4">SSS</h2>
    <div class="accordion" id="faqAcc"><?php foreach ($faqs as $i => $f): ?>
    <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button <?= $i?'collapsed':'' ?>" data-bs-toggle="collapse" data-bs-target="#f<?= $i ?>"><?= e($f['question']) ?></button></h2>
    <div id="f<?= $i ?>" class="accordion-collapse collapse <?= $i?'':'show' ?>"><div class="accordion-body"><?= e($f['answer']) ?></div></div></div>
    <?php endforeach; ?></div></div>
</section>

<section class="py-5" id="appointment">
    <div class="container col-lg-8">
        <h2 class="section-title text-center mb-4">Online Randevu</h2>
        <?php if (!is_customer_logged_in()): ?>
        <div class="alert alert-info">Randevu almak için <a href="<?= customer_url('?route=register') ?>">kayıt olun</a> ve e-postanızı doğrulayın.</div>
        <?php elseif (empty(customer_user()['email_verified_at'])): ?>
        <div class="alert alert-warning">E-posta doğrulaması gerekli. <a href="<?= customer_url('?route=verify-email') ?>">Doğrula</a></div>
        <?php else: ?>
        <form id="appointment-form" class="card p-4 shadow-sm">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Hizmet</label><select name="service_id" id="service_id" class="form-select" required>
                    <option value="">Seçin</option><?php foreach ($services as $svc): ?><option value="<?= $svc['id'] ?>"><?= e($svc['name']) ?></option><?php endforeach; ?>
                </select></div>
                <div class="col-md-6"><label class="form-label">Personel</label><select name="staff_id" id="staff_id" class="form-select"><option value="">Farketmez</option><?php foreach ($staff as $m): ?><option value="<?= $m['id'] ?>"><?= e($m['name']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-6"><label class="form-label">Tarih</label><input type="date" name="appointment_date" id="appointment_date" class="form-control" min="<?= date('Y-m-d') ?>" required></div>
                <div class="col-md-6"><label class="form-label">Saat</label><input type="hidden" name="start_time" id="start_time"><div id="slots-container" class="border rounded p-2 min-h-50"></div></div>
                <div class="col-12"><label class="form-label">Not</label><textarea name="notes" class="form-control" rows=2></textarea></div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Randevu Oluştur</button></div>
            </div>
        </form>
        <?php endif; ?>
    </div>
</section>

<section class="py-5" id="contact">
    <div class="container text-center">
        <h2 class="section-title">İletişim</h2>
        <p><i class="bi bi-telephone"></i> <?= e($settings['site_phone'] ?? '') ?></p>
        <p><i class="bi bi-envelope"></i> <?= e($settings['site_email'] ?? '') ?></p>
        <p><i class="bi bi-geo-alt"></i> <?= e($settings['site_address'] ?? '') ?></p>
    </div>
</section>

<footer class="text-center">
    <div class="container"><p class="mb-0">&copy; <?= date('Y') ?> <?= e($siteTitle) ?></p></div>
</footer>

<script>window.APP_BASE = '<?= base_url() ?>';</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<script>
document.getElementById('appointment-form')?.addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const res = await fetch(window.APP_BASE + '/api/appointment', { method: 'POST', body: fd });
    const data = await res.json();
    alert(data.message || (data.success ? 'Randevu oluşturuldu!' : 'Hata'));
    if (data.success) e.target.reset();
});
</script>
</body>
</html>
