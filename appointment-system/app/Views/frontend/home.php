<?php
$hero      = $sections['hero'] ?? [];
$about     = $sections['about'] ?? [];
$ctaSection = $sections['cta'] ?? [];
$siteTitle = $settings['site_title'] ?? 'RandevuTakip';
$primary   = $settings['theme_primary'] ?? '#4f46e5';
$secondary = $settings['theme_secondary'] ?? '#0ea5e9';
$wa        = preg_replace('/\D/', '', $settings['whatsapp_number'] ?? '');
$showWa    = (($settings['whatsapp_float'] ?? '1') === '1') && $wa;
$trust = [
    ['bi-calendar-check', 'Online Randevu', '7/24 hızlı kayıt'],
    ['bi-people',         'Uzman Ekip',     'Deneyimli kadromuz'],
    ['bi-shield-check',   'Güvenli Ödeme',  '3D Secure altyapı'],
    ['bi-bell',           'SMS / WhatsApp', 'Hatırlatma mesajları'],
];
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/tokens.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/frontend.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/polish.css') ?>" rel="stylesheet">
    <style>
        :root {
            --primary: <?= e($primary) ?>;
            --primary-dark: <?= e($primary) ?>;
            --secondary: <?= e($secondary) ?>;
            --primary-soft: <?= e($primary) ?>1f;
        }
    </style>
</head>
<body class="ps-context">
<?php require APP_PATH . '/Views/frontend/partials/navbar.php'; ?>

<!-- HERO ====================================================== -->
<section class="hero-section hero-pro" id="home">
    <div class="hero-orbs" aria-hidden="true">
        <span class="orb orb-1"></span>
        <span class="orb orb-2"></span>
        <span class="orb orb-3"></span>
    </div>
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="hero-eyebrow">
                    <i class="bi bi-stars"></i> <?= e($settings['business_name'] ?? $siteTitle) ?>
                </span>
                <h1 class="hero-pro__title">
                    <?= e($hero['title'] ?? 'Bakımınızı online planlayın, dakikalar içinde randevu alın.') ?>
                </h1>
                <p class="lead hero-pro__lead">
                    <?= e($hero['subtitle'] ?? $hero['content'] ?? 'Diş kliniği, güzellik merkezi, berber ya da spa — sektörden bağımsız modern bir randevu deneyimi.') ?>
                </p>
                <div class="hero-pro__cta">
                    <button type="button" class="btn btn-cta btn-lg ripple" data-book-start>
                        <i class="bi bi-calendar-plus me-1"></i> Hemen Randevu Al
                    </button>
                    <?php if ($wa): ?>
                    <a href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener" class="btn btn-outline-ghost btn-lg">
                        <i class="bi bi-whatsapp me-1"></i> WhatsApp ile İletişim
                    </a>
                    <?php endif; ?>
                </div>
                <ul class="hero-pro__bullets">
                    <li><i class="bi bi-check-circle-fill"></i> Ücretsiz online randevu</li>
                    <li><i class="bi bi-check-circle-fill"></i> İptal & değişim 1 tıkla</li>
                    <li><i class="bi bi-check-circle-fill"></i> SMS / WhatsApp hatırlatma</li>
                </ul>
                <div class="hero-pro__stats">
                    <div class="hp-stat">
                        <strong class="t-counter" data-counter="<?= (int)($settings['stat_today_bookings'] ?? 23) ?>">0</strong>
                        <span>Bugün randevu</span>
                    </div>
                    <div class="hp-stat">
                        <strong class="t-counter" data-counter="<?= (int)($settings['stat_total_customers'] ?? 1280) ?>" data-suffix="+">0</strong>
                        <span>Mutlu müşteri</span>
                    </div>
                    <div class="hp-stat">
                        <strong>4.9<span class="hp-star">★</span></strong>
                        <span>Müşteri puanı</span>
                    </div>
                </div>
                <div class="hero-live-strip">
                    <span class="dot"></span>
                    Son 1 saat içinde <strong>&nbsp;<span data-counter="12">0</span>&nbsp;</strong> kişi rezerve etti
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-preview lift">
                    <div class="preview-head">
                        <div class="preview-avatar"><i class="bi bi-calendar2-week"></i></div>
                        <div>
                            <strong class="d-block">Randevu Önizleme</strong>
                            <small class="text-white-50">Adım adım rezervasyon</small>
                        </div>
                        <span class="badge bg-success ms-auto"><i class="bi bi-shield-check me-1"></i>SSL</span>
                    </div>
                    <div class="preview-row">
                        <span class="label">Hizmet</span>
                        <span><?= e($services[0]['name'] ?? 'Genel Muayene') ?></span>
                    </div>
                    <div class="preview-row">
                        <span class="label">Personel</span>
                        <span><?= e($staff[0]['name'] ?? 'Sistem önerir') ?></span>
                    </div>
                    <div class="preview-row">
                        <span class="label">Tarih</span>
                        <span><?= date('d.m.Y') ?></span>
                    </div>
                    <div class="preview-row">
                        <span class="label">Müsait Saatler</span>
                        <span class="preview-slots">
                            <em>10:00</em><em>10:30</em><em class="hot">11:00</em>
                        </span>
                    </div>
                    <div class="preview-row">
                        <span class="label">Tahmini Tutar</span>
                        <strong><?= format_money((float)($services[0]['price'] ?? 350)) ?></strong>
                    </div>
                    <button class="btn btn-cta w-100 mt-3 ripple" data-book-start>
                        <i class="bi bi-arrow-right-circle me-1"></i> Bu özet ile devam et
                    </button>
                    <div class="preview-trust">
                        <i class="bi bi-shield-lock"></i> KVKK uyumlu · 256-bit şifreli rezervasyon
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TRUST STRIP =============================================== -->
<section class="trust-strip">
    <div class="container">
        <div class="row g-3">
            <?php foreach ($trust as $t): ?>
            <div class="col-6 col-lg-3">
                <div class="trust-card">
                    <span class="icon"><i class="bi <?= e($t[0]) ?>"></i></span>
                    <div>
                        <h6 class="mb-0"><?= e($t[1]) ?></h6>
                        <p><?= e($t[2]) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ABOUT ===================================================== -->
<section class="section" id="about">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <span class="eyebrow">Biz Kimiz</span>
                <h2 class="section-title mt-3"><?= e($about['title'] ?? 'Modern bakım, profesyonel ekip') ?></h2>
                <p class="text-muted"><?= e($about['subtitle'] ?? '') ?></p>
                <p><?= nl2br(e($about['content'] ?? 'Sektör tecrübemiz, hijyen standartlarımız ve teknoloji altyapımız sayesinde size en iyi deneyimi sunuyoruz.')) ?></p>
                <div class="row g-3 mt-2">
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2"><i class="bi bi-patch-check-fill text-primary"></i> Hijyenik ortam</div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2"><i class="bi bi-patch-check-fill text-primary"></i> Modern ekipman</div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2"><i class="bi bi-patch-check-fill text-primary"></i> Deneyimli kadro</div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2"><i class="bi bi-patch-check-fill text-primary"></i> Kişiselleştirilmiş hizmet</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6"><div class="surface-soft p-4 text-center"><strong class="d-block fs-3 text-gradient" data-counter="<?= (int)preg_replace('/\D/', '', $settings['stat_happy_clients'] ?? '15000') ?>">0</strong><span class="text-muted small">Mutlu Müşteri</span></div></div>
                    <div class="col-6"><div class="surface-soft p-4 text-center"><strong class="d-block fs-3 text-gradient"><?= e($settings['stat_support'] ?? '7/24') ?></strong><span class="text-muted small">Destek</span></div></div>
                    <div class="col-6"><div class="surface-soft p-4 text-center"><strong class="d-block fs-3 text-gradient" data-counter="<?= (int)preg_replace('/\D/', '', $settings['stat_experts'] ?? '45') ?>">0</strong><span class="text-muted small">Uzman</span></div></div>
                    <div class="col-6"><div class="surface-soft p-4 text-center"><strong class="d-block fs-3 text-gradient" data-counter="<?= (int)preg_replace('/\D/', '', $settings['stat_awards'] ?? '12') ?>">0</strong><span class="text-muted small">Ödül</span></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SERVICES ================================================== -->
<section class="section section--alt" id="services">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Hizmetler</span>
            <h2 class="section-title mt-3">Sunduğumuz Hizmetler</h2>
            <p class="section-subtitle">Profesyonel ekibimizle birlikte uzmanlığımıza güvenebilirsiniz.</p>
        </div>
        <div class="service-grid">
            <?php foreach ($services as $svc): ?>
            <div class="service-card" data-reveal>
                <div class="service-cover">
                    <?php if (!empty($svc['image'])): ?>
                        <img src="<?= base_url(e($svc['image'])) ?>" alt="<?= e($svc['name']) ?>">
                    <?php else: ?>
                        <span class="ph"><i class="bi bi-stars"></i></span>
                    <?php endif; ?>
                </div>
                <h5 class="mb-1"><?= e($svc['name']) ?></h5>
                <div class="meta">
                    <span><i class="bi bi-clock me-1"></i><?= (int)($svc['duration_minutes'] ?? 30) ?> dk</span>
                    <span class="price"><?= format_money((float)$svc['price']) ?></span>
                </div>
                <p class="text-muted small mb-3"><?= e($svc['description'] ?? '') ?></p>
                <div class="actions">
                    <button class="btn btn-soft w-100" data-bs-toggle="modal" data-bs-target="#appointmentModal" data-service="<?= (int)$svc['id'] ?>">
                        <i class="bi bi-calendar-plus me-1"></i> Randevu Al
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($services)): ?>
                <p class="text-muted text-center w-100">Şu an aktif hizmet bulunmuyor.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- PACKAGES ================================================== -->
<?php if (!empty($packages)): ?>
<section class="section" id="packages">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Paketler</span>
            <h2 class="section-title mt-3">Avantajlı Seans Paketleri</h2>
            <p class="section-subtitle">Düzenli müşterilerimiz için tasarlanan paketlerle hem zamandan hem bütçeden tasarruf edin.</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php $popularIdx = min(1, count($packages) - 1); foreach ($packages as $i => $pkg):
                $featured = $i === $popularIdx;
                $benefits = array_filter([
                    (int)$pkg['session_count'] . ' seans dahil',
                    (int)$pkg['validity_days'] . ' gün geçerlilik',
                    !empty($pkg['service_name']) ? $pkg['service_name'] . ' için' : null,
                    'Esnek randevu hakkı'
                ]);
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="package-card <?= $featured ? 'featured' : '' ?>" data-reveal>
                    <?php if ($featured): ?><span class="badge-popular">Popüler</span><?php endif; ?>
                    <h5><?= e($pkg['name']) ?></h5>
                    <div class="price">
                        <?= format_money((float)$pkg['price']) ?>
                        <small>/ paket</small>
                    </div>
                    <ul class="benefits">
                        <?php foreach ($benefits as $b): ?>
                        <li><i class="bi bi-check2-circle"></i><?= e($b) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="d-flex flex-column gap-2 mt-auto">
                        <?php if (is_customer_logged_in()): ?>
                        <a href="<?= customer_url('?route=packages') ?>" class="btn <?= $featured ? 'btn-cta' : 'btn-outline-primary' ?>">
                            Satın Al
                        </a>
                        <?php else: ?>
                        <a href="<?= customer_url('?route=register') ?>" class="btn <?= $featured ? 'btn-cta' : 'btn-outline-primary' ?>">
                            Üye Ol &amp; Satın Al
                        </a>
                        <?php endif; ?>
                        <?php if ($wa): ?>
                        <a href="https://wa.me/<?= e($wa) ?>?text=<?= urlencode(($pkg['name'] ?? '') . ' paketi hakkında bilgi almak istiyorum.') ?>" class="btn btn-whatsapp btn-sm" target="_blank" rel="noopener">
                            <i class="bi bi-whatsapp"></i> WhatsApp'tan Bilgi Al
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- APPOINTMENT TEASER ======================================== -->
<section class="section section--alt bg-mesh" id="appointment-teaser">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="eyebrow">5 Adımda Randevu</span>
                <h2 class="section-title mt-3">Modern stepper ile kolay randevu deneyimi</h2>
                <p class="text-muted">Hizmet, personel, tarih ve saat seçimini adım adım yaparak randevunuzu güvenle oluşturun. Seçimleriniz sağ tarafta canlı özet kartında görüntülenir.</p>
                <ol class="list-unstyled">
                    <li class="d-flex gap-3 mb-3"><span class="brand-icon">1</span><div><strong>Hizmet seç</strong><br><small class="text-muted">Tüm aktif hizmetlerimiz arasından seçim yapın.</small></div></li>
                    <li class="d-flex gap-3 mb-3"><span class="brand-icon">2</span><div><strong>Personel seç</strong><br><small class="text-muted">Tercih ettiğiniz uzmanı veya sistemden öneri alın.</small></div></li>
                    <li class="d-flex gap-3 mb-3"><span class="brand-icon">3</span><div><strong>Tarih &amp; saat seç</strong><br><small class="text-muted">Uygun saatler AJAX ile anında listelenir.</small></div></li>
                    <li class="d-flex gap-3 mb-3"><span class="brand-icon">4</span><div><strong>Onayla</strong><br><small class="text-muted">Özet kartını kontrol edin ve randevuyu oluşturun.</small></div></li>
                    <li class="d-flex gap-3 mb-0"><span class="brand-icon">5</span><div><strong>Hatırlatma alın</strong><br><small class="text-muted">SMS, WhatsApp ve e-posta ile bilgilendirilirsiniz.</small></div></li>
                </ol>
                <button type="button" class="btn btn-cta btn-lg mt-3" data-bs-toggle="modal" data-bs-target="#appointmentModal">
                    <i class="bi bi-calendar-plus me-1"></i> Randevu Sihirbazını Aç
                </button>
            </div>
            <div class="col-lg-6">
                <div class="summary-card shadow-lg">
                    <h6>Örnek Özet</h6>
                    <div class="row-line"><span class="label">Hizmet</span><span class="value"><?= e($services[0]['name'] ?? 'Diş Bakımı') ?></span></div>
                    <div class="row-line"><span class="label">Personel</span><span class="value"><?= e($staff[0]['name'] ?? 'Sistem önerir') ?></span></div>
                    <div class="row-line"><span class="label">Tarih</span><span class="value"><?= date('d.m.Y') ?></span></div>
                    <div class="row-line"><span class="label">Saat</span><span class="value">10:30</span></div>
                    <div class="row-line"><span class="label">Tutar</span><span class="value"><?= format_money((float)($services[0]['price'] ?? 450)) ?></span></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TEAM ====================================================== -->
<?php if (!empty($staff)): ?>
<section class="section" id="team">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Ekibimiz</span>
            <h2 class="section-title mt-3">Profesyonel kadromuz</h2>
            <p class="section-subtitle">Uzman ekibimizle size her zaman en iyi hizmeti sunmaya hazırız.</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php foreach ($staff as $member): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="team-card" data-reveal>
                    <?php if (!empty($member['photo'])): ?>
                        <img src="<?= base_url(e($member['photo'])) ?>" class="team-avatar" alt="<?= e($member['name']) ?>">
                    <?php else: ?>
                        <div class="team-avatar bg-light d-inline-flex align-items-center justify-content-center text-primary">
                            <i class="bi bi-person fs-2"></i>
                        </div>
                    <?php endif; ?>
                    <h5 class="mb-1"><?= e($member['name']) ?></h5>
                    <p class="specialty"><?= e($member['title'] ?? '') ?></p>
                    <?php if (!empty($member['specialty'])): ?>
                    <div class="tags">
                        <?php foreach (array_filter(array_map('trim', explode(',', $member['specialty']))) as $tag): ?>
                        <span><?= e($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <button class="btn btn-soft btn-sm" data-bs-toggle="modal" data-bs-target="#appointmentModal">
                        Randevu Al
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CAMPAIGNS ================================================= -->
<?php if (!empty($campaigns)): ?>
<section class="section section--alt" id="campaigns">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Kampanyalar</span>
            <h2 class="section-title mt-3">Güncel kampanyalar</h2>
            <p class="section-subtitle">Sizler için hazırlanan özel fırsatları kaçırmayın.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($campaigns as $c): ?>
            <div class="col-md-6 col-lg-4">
                <div class="campaign-card" data-reveal>
                    <div class="cover">
                        <?php if (!empty($c['image'])): ?>
                            <img src="<?= base_url(e($c['image'])) ?>" alt="<?= e($c['title']) ?>">
                        <?php endif; ?>
                    </div>
                    <div class="body">
                        <?php if (!empty($c['start_date']) || !empty($c['end_date'])): ?>
                        <div class="dates">
                            <i class="bi bi-calendar2 me-1"></i>
                            <?= e($c['start_date'] ?? '—') ?> &mdash; <?= e($c['end_date'] ?? '—') ?>
                        </div>
                        <?php endif; ?>
                        <h5 class="mb-2"><?= e($c['title']) ?></h5>
                        <p class="text-muted small flex-grow-1"><?= e($c['description'] ?? '') ?></p>
                        <div class="actions">
                            <?php if ($wa): ?>
                            <a href="https://wa.me/<?= e($wa) ?>?text=<?= urlencode($c['title'] . ' kampanyası hakkında bilgi almak istiyorum.') ?>" class="btn btn-whatsapp btn-sm w-100" target="_blank" rel="noopener">
                                <i class="bi bi-whatsapp me-1"></i> WhatsApp ile Bilgi Al
                            </a>
                            <?php else: ?>
                            <button class="btn btn-soft btn-sm w-100" data-bs-toggle="modal" data-bs-target="#appointmentModal">
                                Randevu Al
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- GALLERY =================================================== -->
<?php if (!empty($gallery)): ?>
<section class="section" id="gallery">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Galeri</span>
            <h2 class="section-title mt-3">Çalışmalarımızdan kareler</h2>
            <p class="section-subtitle">Görseller üzerine tıklayarak büyütebilirsiniz.</p>
        </div>
        <div class="gallery-grid">
            <?php foreach ($gallery as $g): ?>
            <div class="gallery-item">
                <img src="<?= base_url(e($g['image'])) ?>" alt="<?= e($g['title'] ?? 'Galeri') ?>">
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- REVIEWS =================================================== -->
<?php if (!empty($reviews)): ?>
<section class="section section--alt" id="reviews">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Yorumlar</span>
            <h2 class="section-title mt-3">Müşterilerimiz ne diyor?</h2>
        </div>
        <div class="row g-4">
            <?php foreach ($reviews as $r): ?>
            <div class="col-md-6 col-lg-4">
                <div class="review-card" data-reveal>
                    <div class="stars">
                        <?= str_repeat('<i class="bi bi-star-fill"></i>', (int)($r['rating'] ?? 5)) ?>
                    </div>
                    <p><?= e($r['comment']) ?></p>
                    <div class="author">
                        <?= e($r['customer_name']) ?>
                        <small><?= !empty($r['created_at']) ? format_date($r['created_at']) : '' ?></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- FAQ ======================================================= -->
<?php if (!empty($faqs)): ?>
<section class="section" id="faq">
    <div class="container col-lg-9">
        <div class="section-head">
            <span class="eyebrow">SSS</span>
            <h2 class="section-title mt-3">Sık sorulan sorular</h2>
        </div>
        <div class="faq-list">
            <div class="accordion" id="faqAcc">
                <?php foreach ($faqs as $i => $f): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button <?= $i ? 'collapsed' : '' ?>" data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>"><?= e($f['question']) ?></button>
                    </h2>
                    <div id="faq<?= $i ?>" class="accordion-collapse collapse <?= $i ? '' : 'show' ?>" data-bs-parent="#faqAcc">
                        <div class="accordion-body"><?= nl2br(e($f['answer'])) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CONTACT =================================================== -->
<section class="section section--alt" id="contact">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">İletişim</span>
            <h2 class="section-title mt-3">Bize ulaşın</h2>
            <p class="section-subtitle">Aşağıdaki kanallar üzerinden bize her zaman ulaşabilirsiniz.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="d-flex flex-column gap-3">
                    <?php if ($wa): ?>
                    <a href="https://wa.me/<?= e($wa) ?>" class="contact-card text-decoration-none" target="_blank" rel="noopener">
                        <span class="icon" style="background:rgba(37,211,102,.12); color:#25d366;"><i class="bi bi-whatsapp"></i></span>
                        <div>
                            <div class="label">WhatsApp</div>
                            <div class="value"><?= e($settings['whatsapp_number']) ?></div>
                        </div>
                    </a>
                    <?php endif; ?>
                    <a href="tel:<?= e($settings['site_phone'] ?? '') ?>" class="contact-card text-decoration-none">
                        <span class="icon"><i class="bi bi-telephone"></i></span>
                        <div>
                            <div class="label">Telefon</div>
                            <div class="value"><?= e($settings['site_phone'] ?? '') ?></div>
                        </div>
                    </a>
                    <a href="mailto:<?= e($settings['site_email'] ?? '') ?>" class="contact-card text-decoration-none">
                        <span class="icon"><i class="bi bi-envelope"></i></span>
                        <div>
                            <div class="label">E-posta</div>
                            <div class="value"><?= e($settings['site_email'] ?? '') ?></div>
                        </div>
                    </a>
                    <div class="contact-card">
                        <span class="icon"><i class="bi bi-geo-alt"></i></span>
                        <div>
                            <div class="label">Adres</div>
                            <div class="value"><?= e($settings['site_address'] ?? '') ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="map-wrap mb-3">
                    <?php if (!empty($settings['map_embed'])): ?>
                        <?= $settings['map_embed'] ?>
                    <?php else: ?>
                        <iframe src="https://maps.google.com/maps?q=<?= urlencode($settings['site_address'] ?? 'Istanbul') ?>&output=embed" loading="lazy" allowfullscreen></iframe>
                    <?php endif; ?>
                </div>
                <form class="contact-form" method="post" action="<?= base_url('?route=contact/send') ?>">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6"><input class="form-control" name="name" placeholder="Adınız Soyadınız" required></div>
                        <div class="col-md-6"><input class="form-control" name="phone" placeholder="Telefon" required></div>
                        <div class="col-12"><input class="form-control" type="email" name="email" placeholder="E-posta" required></div>
                        <div class="col-12"><textarea class="form-control" name="message" rows="3" placeholder="Mesajınız" required></textarea></div>
                        <div class="col-12"><button type="submit" class="btn btn-cta">Mesajı Gönder</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require APP_PATH . '/Views/frontend/partials/footer.php'; ?>
<?php require APP_PATH . '/Views/frontend/partials/appointment-modal.php'; ?>
<?php require APP_PATH . '/Views/frontend/partials/auth-drawer.php'; ?>

<?php if ($showWa): ?>
<a href="https://wa.me/<?= e($wa) ?>" class="floating-wa" target="_blank" rel="noopener" aria-label="WhatsApp ile yazın">
    <i class="bi bi-whatsapp"></i>
</a>
<?php endif; ?>

<div class="mobile-cta-bar">
    <button type="button" class="btn btn-cta w-100" id="mobile-cta-book">
        <i class="bi bi-calendar-plus me-1"></i> Hızlı Randevu Al
    </button>
</div>

<script>
    window.APP_BASE = '<?= base_url() ?>';
    window.CSRF = '<?= csrf_token() ?>';
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/frontend.js') ?>"></script>
<script src="<?= base_url('assets/js/auth.js') ?>"></script>
<script src="<?= base_url('assets/js/appointment.js') ?>"></script>
</body>
</html>
