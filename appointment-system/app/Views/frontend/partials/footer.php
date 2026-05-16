<?php
$siteTitle = $settings['site_title'] ?? 'RandevuTakip';
$footer = $sections['footer'] ?? [];
$social = [
    'facebook'  => $settings['social_facebook']  ?? '',
    'instagram' => $settings['social_instagram'] ?? '',
    'twitter'   => $settings['social_twitter']   ?? '',
    'youtube'   => $settings['social_youtube']   ?? '',
];
?>
<footer class="site-footer">
    <div class="container">
        <div class="row g-4 pb-4">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="brand-icon"><i class="bi bi-calendar2-check"></i></span>
                    <h5 class="mb-0"><?= e($siteTitle) ?></h5>
                </div>
                <p class="text-white-50 mb-3">
                    <?= e($footer['content'] ?? $settings['site_tagline'] ?? 'Online randevu sistemi ile zamanınızı verimli kullanın.') ?>
                </p>
                <div class="social-links">
                    <?php foreach ($social as $icon => $url): if (!$url) continue; ?>
                        <a href="<?= e($url) ?>" target="_blank" rel="noopener" aria-label="<?= e($icon) ?>">
                            <i class="bi bi-<?= e($icon) ?>"></i>
                        </a>
                    <?php endforeach; ?>
                    <?php if (!array_filter($social)): ?>
                        <a href="#" aria-label="instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" aria-label="facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" aria-label="whatsapp"><i class="bi bi-whatsapp"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="mb-3">Hızlı Linkler</h6>
                <ul class="footer-links">
                    <li><a href="#about">Hakkımızda</a></li>
                    <li><a href="#services">Hizmetler</a></li>
                    <li><a href="#packages">Paketler</a></li>
                    <li><a href="#team">Ekibimiz</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="mb-3">Yardım</h6>
                <ul class="footer-links">
                    <li><a href="#faq">SSS</a></li>
                    <li><a href="#contact">İletişim</a></li>
                    <li><a href="<?= customer_url('?route=login') ?>">Giriş Yap</a></li>
                    <li><a href="<?= customer_url('?route=register') ?>">Üye Ol</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6 class="mb-3">İletişim</h6>
                <p class="text-white-50 mb-1"><i class="bi bi-telephone me-2"></i><?= e($settings['site_phone'] ?? '') ?></p>
                <p class="text-white-50 mb-1"><i class="bi bi-envelope me-2"></i><?= e($settings['site_email'] ?? '') ?></p>
                <p class="text-white-50 mb-3"><i class="bi bi-geo-alt me-2"></i><?= e($settings['site_address'] ?? '') ?></p>
                <?php if (!empty($settings['whatsapp_number'])): $wa = preg_replace('/\D/', '', $settings['whatsapp_number']); ?>
                <a href="https://wa.me/<?= e($wa) ?>" class="btn btn-whatsapp btn-sm" target="_blank" rel="noopener">
                    <i class="bi bi-whatsapp me-1"></i> WhatsApp ile yazın
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="text-white-50">&copy; <?= date('Y') ?> <?= e($siteTitle) ?>. Tüm hakları saklıdır.</span>
            <div class="d-flex gap-3 small">
                <a href="#" class="text-white-50">KVKK</a>
                <a href="#" class="text-white-50">Gizlilik</a>
                <a href="#" class="text-white-50">Çerez Politikası</a>
            </div>
        </div>
    </div>
</footer>
