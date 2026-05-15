<?php
$hero = $sections['hero'] ?? [];
$about = $sections['about'] ?? [];
$siteTitle = $settings['site_title'] ?? 'RandevuTakip';
$primary = $settings['theme_primary'] ?? '#4f46e5';
$wa = preg_replace('/\D/', '', $settings['whatsapp_number'] ?? '');
$showWa = ($settings['whatsapp_float'] ?? '1') === '1' && $wa;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($settings['seo_title'] ?? $siteTitle) ?></title>
    <meta name="description" content="<?= e($settings['seo_description'] ?? '') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/frontend/css/style.css') ?>" rel="stylesheet">
    <style>:root { --primary: <?= e($primary) ?>; }</style>
</head>
<body>
<?php require APP_PATH . '/Views/frontend/partials/navbar.php'; ?>

<section class="hero-section" id="home">
    <div class="container py-5">
        <span class="badge bg-white bg-opacity-25 text-white mb-3 px-3 py-2"><?= e($settings['business_name'] ?? '') ?></span>
        <h1 class="mb-3"><?= e($hero['title'] ?? 'Modern Bakım, Profesyonel Hizmet') ?></h1>
        <p class="lead col-lg-7 opacity-90"><?= e($hero['subtitle'] ?? $hero['content'] ?? '') ?></p>
        <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="button" class="btn btn-cta btn-lg" data-bs-toggle="modal" data-bs-target="#appointmentModal">
                <i class="bi bi-calendar-plus me-1"></i> Hemen Randevu Al
            </button>
            <a href="#services" class="btn btn-outline-light btn-lg">Hizmetlerimiz</a>
        </div>
    </div>
</section>

<section class="py-5" id="about">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title"><?= e($about['title'] ?? 'Hakkımızda') ?></h2>
            <p class="section-subtitle"><?= e($about['subtitle'] ?? '') ?></p>
        </div>
        <p class="text-muted col-lg-8 mx-auto text-center"><?= e($about['content'] ?? '') ?></p>
    </div>
</section>

<section class="py-5 bg-light" id="services">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Hizmetlerimiz</h2>
            <p class="section-subtitle">Size özel profesyonel hizmetler</p>
        </div>
        <div class="row g-4">
            <?php foreach ($services as $svc): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card card-premium service-card p-4">
                    <div class="icon-wrap mb-3"><i class="bi bi-stars"></i></div>
                    <?php if (!empty($svc['image'])): ?>
                    <img src="<?= base_url(e($svc['image'])) ?>" class="rounded mb-3 w-100" style="height:140px;object-fit:cover" alt="">
                    <?php endif; ?>
                    <h5><?= e($svc['name']) ?></h5>
                    <p class="text-muted small"><?= e($svc['description'] ?? '') ?></p>
                    <p class="fw-bold text-primary mb-0"><?= format_money((float)$svc['price']) ?></p>
                    <small class="text-muted"><?= (int)($svc['duration_minutes'] ?? 30) ?> dk</small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5" id="packages">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Avantajlı Paketler</h2>
            <p class="section-subtitle">Seans paketleriyle tasarruf edin</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php $i = 0; foreach ($packages as $pkg): $i++; $featured = $i === min(2, count($packages)); ?>
            <div class="col-md-6 col-lg-4">
                <div class="card card-premium package-card p-4 text-center <?= $featured ? 'featured' : '' ?>">
                    <?php if ($featured): ?><span class="badge-popular">Popüler</span><?php endif; ?>
                    <h5><?= e($pkg['name']) ?></h5>
                    <p class="display-6 fw-bold text-primary"><?= format_money((float)$pkg['price']) ?></p>
                    <p class="text-muted"><?= (int)$pkg['session_count'] ?> seans · <?= (int)$pkg['validity_days'] ?> gün</p>
                    <a href="<?= customer_url('?route=register') ?>" class="btn <?= $featured ? 'btn-cta' : 'btn-outline-primary' ?>">Satın Al</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="stats-bar">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-6 col-md-3"><div class="stat-num"><?= e($settings['stat_happy_clients'] ?? '15k+') ?></div><small class="text-muted">Mutlu Danışan</small></div>
            <div class="col-6 col-md-3"><div class="stat-num"><?= e($settings['stat_support'] ?? '7/24') ?></div><small class="text-muted">Destek</small></div>
            <div class="col-6 col-md-3"><div class="stat-num"><?= e($settings['stat_experts'] ?? '45+') ?></div><small class="text-muted">Uzman</small></div>
            <div class="col-6 col-md-3"><div class="stat-num"><?= e($settings['stat_awards'] ?? '12') ?></div><small class="text-muted">Ödül</small></div>
        </div>
    </div>
</section>

<section class="py-5" id="team">
    <div class="container">
        <div class="text-center mb-5"><h2 class="section-title">Ekibimiz</h2></div>
        <div class="row g-4 justify-content-center">
            <?php foreach ($staff as $member): ?>
            <div class="col-6 col-md-4 col-lg-3 text-center team-card">
                <?php if (!empty($member['photo'])): ?>
                <img src="<?= base_url(e($member['photo'])) ?>" class="team-avatar mb-3" alt="">
                <?php else: ?>
                <div class="team-avatar bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3"><i class="bi bi-person fs-2 text-primary"></i></div>
                <?php endif; ?>
                <h5 class="mb-0"><?= e($member['name']) ?></h5>
                <p class="text-muted small"><?= e($member['title'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (!empty($campaigns)): ?>
<section class="py-5 bg-light" id="campaigns">
    <div class="container">
        <h2 class="section-title text-center mb-4">Kampanyalar</h2>
        <div class="row g-4">
            <?php foreach ($campaigns as $c): ?>
            <div class="col-md-4">
                <div class="card card-premium p-4">
                    <h5><?= e($c['title']) ?></h5>
                    <p class="text-muted small mb-0"><?= e($c['description'] ?? '') ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($gallery)): ?>
<section class="py-5" id="gallery">
    <div class="container">
        <h2 class="section-title text-center mb-4">Galeri</h2>
        <div class="row g-3">
            <?php foreach ($gallery as $g): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="gallery-item">
                    <img src="<?= base_url(e($g['image'])) ?>" alt="<?= e($g['title'] ?? '') ?>">
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="py-5 bg-light" id="reviews">
    <div class="container">
        <h2 class="section-title text-center mb-4">Müşteri Yorumları</h2>
        <div class="row g-4">
            <?php foreach ($reviews as $r): ?>
            <div class="col-md-6">
                <div class="card review-card p-4">
                    <div class="text-warning mb-2"><?= str_repeat('★', (int)($r['rating'] ?? 5)) ?></div>
                    <p class="mb-2"><?= e($r['comment']) ?></p>
                    <small class="text-muted">— <?= e($r['customer_name']) ?></small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5" id="faq">
    <div class="container col-lg-8">
        <h2 class="section-title text-center mb-4">Sık Sorulan Sorular</h2>
        <div class="accordion" id="faqAcc">
            <?php foreach ($faqs as $i => $f): ?>
            <div class="accordion-item border-0 mb-2 shadow-sm rounded overflow-hidden">
                <h2 class="accordion-header">
                    <button class="accordion-button <?= $i ? 'collapsed' : '' ?>" data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>"><?= e($f['question']) ?></button>
                </h2>
                <div id="faq<?= $i ?>" class="accordion-collapse collapse <?= $i ? '' : 'show' ?>" data-bs-parent="#faqAcc">
                    <div class="accordion-body text-muted"><?= e($f['answer']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5" id="contact">
    <div class="container">
        <div class="text-center mb-5"><h2 class="section-title">İletişim</h2></div>
        <div class="row g-4">
            <div class="col-lg-5">
                <p><i class="bi bi-telephone text-primary me-2"></i><?= e($settings['site_phone'] ?? '') ?></p>
                <p><i class="bi bi-envelope text-primary me-2"></i><?= e($settings['site_email'] ?? '') ?></p>
                <p><i class="bi bi-geo-alt text-primary me-2"></i><?= e($settings['site_address'] ?? '') ?></p>
            </div>
            <div class="col-lg-7">
                <div class="map-wrap">
                    <?php if (!empty($settings['map_embed'])): ?>
                    <?= $settings['map_embed'] ?>
                    <?php else: ?>
                    <iframe src="https://maps.google.com/maps?q=<?= urlencode($settings['site_address'] ?? 'Istanbul') ?>&output=embed" loading="lazy"></iframe>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require APP_PATH . '/Views/frontend/partials/footer.php'; ?>
<?php require APP_PATH . '/Views/frontend/partials/appointment-modal.php'; ?>

<?php if ($showWa): ?>
<a href="https://wa.me/<?= e($wa) ?>" class="floating-wa" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
<?php endif; ?>

<div class="mobile-cta-bar d-lg-none">
    <button type="button" class="btn btn-cta w-100" id="mobile-cta-book"><i class="bi bi-calendar-plus me-1"></i> Hızlı Randevu</button>
</div>

<script>window.APP_BASE = '<?= base_url() ?>'; window.CSRF = '<?= csrf_token() ?>';</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/frontend/js/main.js') ?>"></script>
<script src="<?= base_url('assets/frontend/js/appointment.js') ?>"></script>
</body>
</html>
