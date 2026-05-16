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
                    <span class="brand-icon-inline" style="width:42px;height:42px;font-size:1.2rem;"><i class="bi bi-calendar2-check"></i></span>
                    <h5 class="mb-0 text-white"><?= e($siteTitle) ?></h5>
                </div>
                <p class="mb-3" style="color:rgba(255,255,255,.7);">
                    <?= e($footer['content'] ?? $settings['site_tagline'] ?? 'Online randevu sistemi ile zamanınızı verimli kullanın.') ?>
                </p>
                <div class="footer-social">
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
                <h6>Hızlı Linkler</h6>
                <a href="#about">Hakkımızda</a>
                <a href="#services">Hizmetler</a>
                <a href="#packages">Paketler</a>
                <a href="#team">Ekibimiz</a>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Yardım</h6>
                <a href="#faq">SSS</a>
                <a href="#contact">İletişim</a>
                <a href="<?= customer_url('?route=login') ?>">Giriş Yap</a>
                <a href="<?= customer_url('?route=register') ?>">Üye Ol</a>
            </div>
            <div class="col-lg-4">
                <h6>İletişim</h6>
                <p class="mb-1"><i class="bi bi-telephone me-2"></i><?= e($settings['site_phone'] ?? '') ?></p>
                <p class="mb-1"><i class="bi bi-envelope me-2"></i><?= e($settings['site_email'] ?? '') ?></p>
                <p class="mb-3"><i class="bi bi-geo-alt me-2"></i><?= e($settings['site_address'] ?? '') ?></p>
                <?php if (!empty($settings['whatsapp_number'])): $wa = preg_replace('/\D/', '', $settings['whatsapp_number']); ?>
                <a href="https://wa.me/<?= e($wa) ?>" class="btn btn-whatsapp btn-sm" target="_blank" rel="noopener">
                    <i class="bi bi-whatsapp me-1"></i> WhatsApp ile yazın
                </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>
                &copy; <?= date('Y') ?> <?= e($siteTitle) ?>. Tüm hakları saklıdır.
                <span class="footer-credit ms-2">
                    · Powered by
                    <a href="https://tgrsoft.com" target="_blank" rel="noopener sponsored">
                        <i class="bi bi-lightning-charge-fill"></i> TGR <strong>Soft</strong>
                    </a>
                </span>
            </span>
            <div class="d-flex gap-3">
                <a href="#">KVKK</a>
                <a href="#">Gizlilik</a>
                <a href="#">Çerez Politikası</a>
            </div>
        </div>
    </div>
</footer>
