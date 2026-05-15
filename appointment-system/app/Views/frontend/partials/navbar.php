<?php $siteTitle = $settings['site_title'] ?? 'RandevuTakip'; ?>
<nav class="navbar navbar-expand-lg site-navbar fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="#home">
            <span class="brand-icon"><i class="bi bi-calendar2-check"></i></span>
            <span><?= e($siteTitle) ?></span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-label="Menü">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link" href="#about">Hakkımızda</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Hizmetler</a></li>
                <li class="nav-item"><a class="nav-link" href="#packages">Paketler</a></li>
                <li class="nav-item"><a class="nav-link" href="#team">Ekibimiz</a></li>
                <li class="nav-item"><a class="nav-link" href="#gallery">Galeri</a></li>
                <li class="nav-item"><a class="nav-link" href="#faq">SSS</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">İletişim</a></li>
                <?php if (is_customer_logged_in()): ?>
                <li class="nav-item"><a class="nav-link" href="<?= customer_url('?route=') ?>">Panelim</a></li>
                <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="<?= customer_url('?route=login') ?>">Giriş</a></li>
                <?php endif; ?>
                <li class="nav-item ms-lg-2">
                    <button type="button" class="btn btn-cta" data-bs-toggle="modal" data-bs-target="#appointmentModal">
                        <i class="bi bi-calendar-plus me-1"></i> Randevu Al
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>
