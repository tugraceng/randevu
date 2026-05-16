<?php
$loggedIn = is_customer_logged_in();
$user     = $loggedIn ? customer_user() : null;
$verified = $loggedIn && !empty($user['email_verified_at']);
$siteTitle = $settings['site_title'] ?? 'RandevuTakip';
?>
<div class="auth-drawer" id="authDrawer" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="auth-drawer__backdrop" data-auth-close></div>

    <aside class="auth-drawer__panel" tabindex="-1">

        <!-- LEFT: trust pane (desktop only) ============================ -->
        <div class="auth-drawer__trust">
            <div class="trust-brand">
                <span class="trust-icon"><i class="bi bi-calendar2-check"></i></span>
                <span><?= e($siteTitle) ?></span>
            </div>
            <h3 class="trust-headline">Saniyeler içinde<br>randevu alın.</h3>
            <p class="trust-sub">Tüm randevularınız, paketleriniz ve ödeme geçmişiniz tek bir yerde.</p>

            <ul class="trust-list">
                <li><span class="ti"><i class="bi bi-shield-check"></i></span>
                    <div><strong>Güvenli</strong><small>KVKK uyumlu, şifreli</small></div>
                </li>
                <li><span class="ti"><i class="bi bi-bell"></i></span>
                    <div><strong>Otomatik hatırlatma</strong><small>SMS, WhatsApp, e-posta</small></div>
                </li>
                <li><span class="ti"><i class="bi bi-clock-history"></i></span>
                    <div><strong>7/24 erişim</strong><small>Tatil-mesai dinlemeyen kayıt</small></div>
                </li>
                <li><span class="ti"><i class="bi bi-credit-card-2-back"></i></span>
                    <div><strong>Online ödeme</strong><small>3D Secure ile koruma</small></div>
                </li>
            </ul>

            <div class="trust-foot">
                <i class="bi bi-stars me-1"></i> Binlerce mutlu müşteri tarafından tercih ediliyor.
            </div>
        </div>

        <!-- RIGHT: forms ============================================ -->
        <div class="auth-drawer__body">

            <header class="auth-drawer__head">
                <div class="auth-tabs" role="tablist">
                    <button type="button" class="auth-tab active" data-auth-tab="login" role="tab">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Giriş Yap
                    </button>
                    <button type="button" class="auth-tab" data-auth-tab="register" role="tab">
                        <i class="bi bi-person-plus me-1"></i> Kayıt Ol
                    </button>
                </div>
                <button type="button" class="auth-close" data-auth-close aria-label="Kapat">
                    <i class="bi bi-x-lg"></i>
                </button>
            </header>

            <!-- Pending action banner (e.g. "Login required to continue booking") -->
            <div class="auth-pending-banner" data-auth-pending hidden>
                <i class="bi bi-info-circle me-2"></i>
                <span data-auth-pending-text>Devam etmek için giriş yapın.</span>
            </div>

            <div class="auth-drawer__scroll">

                <!-- LOGIN PANEL =================================== -->
                <section class="auth-pane active" data-auth-pane="login" role="tabpanel">
                    <h4>Hoş geldiniz</h4>
                    <p class="auth-sub">Devam etmek için giriş yapın.</p>

                    <form method="post" action="<?= base_url('?route=login') ?>" data-auth-form="login" novalidate>
                        <?= csrf_field() ?>
                        <div class="auth-field">
                            <label class="auth-label">E-posta</label>
                            <div class="auth-input">
                                <i class="bi bi-envelope auth-input__icon"></i>
                                <input type="email" name="email" autocomplete="email" required placeholder="ornek@mail.com">
                            </div>
                        </div>
                        <div class="auth-field">
                            <label class="auth-label">Şifre</label>
                            <div class="auth-input">
                                <i class="bi bi-lock auth-input__icon"></i>
                                <input type="password" name="password" autocomplete="current-password" required placeholder="••••••••">
                                <button type="button" class="auth-input__suffix" data-pwd-toggle aria-label="Şifreyi göster"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>

                        <div class="auth-row">
                            <label class="auth-check">
                                <input type="checkbox" name="remember" value="1">
                                <span>Beni hatırla</span>
                            </label>
                            <button type="button" class="auth-link" data-auth-switch="forgot">
                                Şifremi unuttum
                            </button>
                        </div>

                        <button type="submit" class="auth-cta">
                            <span class="lbl">Giriş Yap</span>
                            <i class="bi bi-arrow-right ms-1"></i>
                        </button>

                        <p class="auth-foot-line">
                            Hesabınız yok mu?
                            <button type="button" class="auth-link" data-auth-tab-trigger="register">Hemen üye olun</button>
                        </p>
                    </form>
                </section>

                <!-- REGISTER PANEL =================================== -->
                <section class="auth-pane" data-auth-pane="register" role="tabpanel">
                    <h4>Hesap oluşturun</h4>
                    <p class="auth-sub">30 saniyede üye olun, randevularınızı yönetin.</p>

                    <form method="post" action="<?= base_url('?route=register') ?>" data-auth-form="register" novalidate>
                        <?= csrf_field() ?>
                        <div class="auth-grid-2">
                            <div class="auth-field">
                                <label class="auth-label">Ad *</label>
                                <div class="auth-input">
                                    <i class="bi bi-person auth-input__icon"></i>
                                    <input name="first_name" autocomplete="given-name" required>
                                </div>
                            </div>
                            <div class="auth-field">
                                <label class="auth-label">Soyad *</label>
                                <div class="auth-input">
                                    <i class="bi bi-person auth-input__icon"></i>
                                    <input name="last_name" autocomplete="family-name" required>
                                </div>
                            </div>
                        </div>

                        <div class="auth-field">
                            <label class="auth-label">Telefon</label>
                            <div class="auth-input">
                                <i class="bi bi-telephone auth-input__icon"></i>
                                <input name="phone" autocomplete="tel" placeholder="05XX XXX XX XX">
                            </div>
                        </div>

                        <div class="auth-field">
                            <label class="auth-label">E-posta *</label>
                            <div class="auth-input">
                                <i class="bi bi-envelope auth-input__icon"></i>
                                <input type="email" name="email" autocomplete="email" required>
                            </div>
                        </div>

                        <div class="auth-field">
                            <label class="auth-label">Şifre * <span class="auth-hint">(en az 8 karakter)</span></label>
                            <div class="auth-input">
                                <i class="bi bi-lock auth-input__icon"></i>
                                <input type="password" name="password" autocomplete="new-password" minlength="8" required>
                                <button type="button" class="auth-input__suffix" data-pwd-toggle aria-label="Şifreyi göster"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>

                        <div class="auth-perms">
                            <label class="auth-check"><input type="checkbox" name="sms_permission" value="1" checked> <span>SMS bildirim almak istiyorum</span></label>
                            <label class="auth-check"><input type="checkbox" name="whatsapp_permission" value="1"> <span>WhatsApp bildirim almak istiyorum</span></label>
                            <label class="auth-check"><input type="checkbox" name="kvkk" value="1" required> <span><strong>KVKK</strong> &amp; <strong>Üyelik Sözleşmesi</strong>'ni okudum, kabul ediyorum.</span></label>
                        </div>

                        <button type="submit" class="auth-cta">
                            <span class="lbl">Hesap Oluştur</span>
                            <i class="bi bi-arrow-right ms-1"></i>
                        </button>

                        <p class="auth-foot-line">
                            Zaten bir hesabınız var mı?
                            <button type="button" class="auth-link" data-auth-tab-trigger="login">Giriş yapın</button>
                        </p>
                    </form>
                </section>

                <!-- FORGOT PASSWORD PANEL =================================== -->
                <section class="auth-pane" data-auth-pane="forgot" role="tabpanel">
                    <button type="button" class="auth-back" data-auth-tab-trigger="login">
                        <i class="bi bi-arrow-left me-1"></i> Geri
                    </button>
                    <h4>Şifrenizi mi unuttunuz?</h4>
                    <p class="auth-sub">E-posta adresinize sıfırlama bağlantısı gönderelim.</p>

                    <form method="post" action="<?= base_url('?route=forgot-password') ?>" data-auth-form="forgot" novalidate>
                        <?= csrf_field() ?>
                        <div class="auth-field">
                            <label class="auth-label">E-posta</label>
                            <div class="auth-input">
                                <i class="bi bi-envelope auth-input__icon"></i>
                                <input type="email" name="email" autocomplete="email" required placeholder="ornek@mail.com">
                            </div>
                        </div>

                        <button type="submit" class="auth-cta">
                            <span class="lbl">Sıfırlama Bağlantısı Gönder</span>
                            <i class="bi bi-send ms-1"></i>
                        </button>

                        <p class="auth-foot-line">
                            Hatırladınız mı?
                            <button type="button" class="auth-link" data-auth-tab-trigger="login">Giriş yapın</button>
                        </p>
                    </form>
                </section>

                <!-- VERIFY EMAIL PANEL =================================== -->
                <section class="auth-pane" data-auth-pane="verify" role="tabpanel">
                    <div class="auth-verify">
                        <div class="auth-verify__icon"><i class="bi bi-envelope-paper-heart"></i></div>
                        <h4>E-postanızı doğrulayın</h4>
                        <p class="auth-sub">
                            <strong data-auth-user-email>...</strong> adresine bir doğrulama bağlantısı gönderdik.
                            Randevu oluşturabilmek için bu bağlantıya tıklayın.
                        </p>

                        <form method="post" action="<?= base_url('?route=resend-verification') ?>" data-auth-form="resend">
                            <?= csrf_field() ?>
                            <button type="submit" class="auth-cta auth-cta--ghost">
                                <i class="bi bi-arrow-clockwise me-1"></i> Doğrulama linkini tekrar gönder
                            </button>
                        </form>

                        <p class="auth-verify__hint">
                            <i class="bi bi-info-circle me-1"></i>
                            E-posta gelmediyse spam klasörünüzü kontrol edin.
                        </p>

                        <hr>

                        <a class="auth-link d-block text-center" data-auth-logout href="<?= base_url('?route=logout') ?>">
                            <i class="bi bi-box-arrow-left me-1"></i> Farklı bir hesapla giriş yap
                        </a>
                    </div>
                </section>

                <!-- LOGGED IN PANEL (yet verified or not, with quick links) ====== -->
                <section class="auth-pane" data-auth-pane="account" role="tabpanel">
                    <div class="auth-account">
                        <div class="auth-account__hello">
                            <span class="dot"></span>
                            Aktif oturum: <strong data-auth-user-name>—</strong>
                        </div>
                        <h4>Hoş geldiniz!</h4>
                        <p class="auth-sub">Panelinize gidin veya hızlıca yeni bir randevu oluşturun.</p>

                        <div class="auth-account__actions">
                            <a href="<?= customer_url('?route=') ?>" class="auth-cta">
                                <i class="bi bi-grid me-1"></i> Müşteri Paneli
                            </a>
                            <button type="button" class="auth-cta auth-cta--ghost" data-auth-resume>
                                <i class="bi bi-calendar-plus me-1"></i> Randevuya Devam Et
                            </button>
                            <a href="<?= base_url('?route=logout') ?>" class="auth-link d-inline-block">
                                <i class="bi bi-box-arrow-left me-1"></i> Çıkış Yap
                            </a>
                        </div>
                    </div>
                </section>

            </div>

            <!-- Mobile sticky submit area (handled inline above; reserved for future) -->
        </div>
    </aside>
</div>

<!-- Initial state bridge for JS -->
<script>
    window.AUTH_STATE = {
        loggedIn: <?= $loggedIn ? 'true' : 'false' ?>,
        verified: <?= $verified ? 'true' : 'false' ?>,
        user: <?= json_encode([
            'first_name' => $user['first_name'] ?? '',
            'last_name'  => $user['last_name'] ?? '',
            'email'      => $user['email'] ?? '',
        ], JSON_UNESCAPED_UNICODE) ?>,
        urls: {
            login:    <?= json_encode(base_url('?route=login')) ?>,
            register: <?= json_encode(base_url('?route=register')) ?>,
            forgot:   <?= json_encode(base_url('?route=forgot-password')) ?>,
            resend:   <?= json_encode(base_url('?route=resend-verification')) ?>,
            logout:   <?= json_encode(base_url('?route=logout')) ?>,
            status:   <?= json_encode(base_url('?route=auth-status')) ?>
        }
    };
</script>
