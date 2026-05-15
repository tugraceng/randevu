<?php
$siteTitle = $settings['site_title'] ?? 'RandevuTakip';
$footer = $sections['footer'] ?? [];
?>
<footer class="site-footer">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <h5 class="text-white mb-3"><?= e($siteTitle) ?></h5>
                <p class="text-white-50"><?= e($footer['content'] ?? $settings['site_description'] ?? '') ?></p>
            </div>
            <div class="col-lg-4">
                <h6 class="text-white mb-3">Hızlı Linkler</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="#services">Hizmetler</a></li>
                    <li><a href="#packages">Paketler</a></li>
                    <li><a href="#faq">SSS</a></li>
                    <li><a href="#contact">İletişim</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6 class="text-white mb-3">İletişim</h6>
                <p class="text-white-50 mb-1"><i class="bi bi-telephone me-2"></i><?= e($settings['site_phone'] ?? '') ?></p>
                <p class="text-white-50 mb-1"><i class="bi bi-envelope me-2"></i><?= e($settings['site_email'] ?? '') ?></p>
                <p class="text-white-50"><i class="bi bi-geo-alt me-2"></i><?= e($settings['site_address'] ?? '') ?></p>
            </div>
        </div>
        <hr class="border-secondary opacity-25">
        <p class="text-center text-white-50 small mb-0">&copy; <?= date('Y') ?> <?= e($siteTitle) ?>. Tüm hakları saklıdır.</p>
    </div>
</footer>
