<?php $siteTitle = $settings['site_title'] ?? 'RandevuTakip'; ?>
<nav class="site-nav" id="siteNav">
    <div class="container">
        <nav class="navbar navbar-expand-lg p-0">
            <a class="navbar-brand" href="#home">
                <span class="brand-icon"><i class="bi bi-calendar2-check"></i></span>
                <span><?= e($siteTitle) ?></span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Menü">
                <i class="bi bi-list fs-3 text-dark"></i>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1 mt-3 mt-lg-0">
                    <li class="nav-item"><a class="nav-link" href="#about">Hakkımızda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Hizmetler</a></li>
                    <li class="nav-item"><a class="nav-link" href="#packages">Paketler</a></li>
                    <li class="nav-item"><a class="nav-link" href="#team">Ekibimiz</a></li>
                    <li class="nav-item"><a class="nav-link" href="#gallery">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="#faq">SSS</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">İletişim</a></li>
                    <?php if (is_customer_logged_in()): ?>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-soft btn-sm" href="<?= customer_url('?route=') ?>">
                            <i class="bi bi-person-circle me-1"></i> Panelim
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('?route=logout') ?>" data-auth-logout>
                            <i class="bi bi-box-arrow-left me-1"></i> Çıkış
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item ms-lg-2">
                        <button type="button" class="nav-link btn btn-link" data-auth-open="login">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Giriş
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="btn btn-soft btn-sm" data-auth-open="register">
                            <i class="bi bi-person-plus me-1"></i> Üye Ol
                        </button>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item ms-lg-2">
                        <button type="button" class="btn btn-cta" data-book-start>
                            <i class="bi bi-calendar-plus me-1"></i> Randevu Al
                        </button>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</nav>
