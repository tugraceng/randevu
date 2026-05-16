<?php require APP_PATH . '/Views/admin/partials/header.php'; $s = $settings; ?>

<div class="row g-4">
    <div class="col-lg-3">
        <div class="panel">
            <div class="panel-body p-2">
                <ul class="nav nav-pills flex-column gap-1" role="tablist">
                    <?php
                    $tabs = [
                        'general'   => ['Genel Ayarlar',     'bi-sliders'],
                        'theme'     => ['Tema &amp; Renkler', 'bi-palette'],
                        'rules'     => ['Randevu Kuralları', 'bi-calendar-week'],
                        'mail'      => ['SMTP / Mail',       'bi-envelope'],
                        'netgsm'    => ['NetGSM SMS',        'bi-chat-dots'],
                        'whatsapp'  => ['WhatsApp Business', 'bi-whatsapp'],
                        'payment'   => ['Ödeme Sağlayıcıları','bi-credit-card'],
                        'seo'       => ['SEO &amp; Sosyal',  'bi-search'],
                    ];
                    $first = true;
                    foreach ($tabs as $key => [$label, $icon]):
                    ?>
                    <li class="nav-item">
                        <button class="nav-link text-start <?= $first ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#tab-<?= e($key) ?>" type="button">
                            <i class="bi <?= e($icon) ?> me-2"></i><?= $label ?>
                        </button>
                    </li>
                    <?php $first = false; endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="panel mt-3">
            <div class="panel-header"><h6><i class="bi bi-braces me-1"></i> Şablon Değişkenleri</h6></div>
            <div class="panel-body">
                <p class="small text-muted">Mesaj şablonlarınızda aşağıdaki değişkenleri kullanabilirsiniz:</p>
                <div class="template-vars">
                    <?php foreach (['{name}','{phone}','{email}','{date}','{time}','{service}','{staff}','{package}','{remaining_sessions}','{payment_amount}','{business_name}'] as $v): ?>
                    <span class="var"><?= e($v) ?></span>
                    <?php endforeach; ?>
                </div>
                <a href="<?= admin_url('?route=messages') ?>" class="btn btn-soft btn-sm mt-3 w-100">Şablonları Düzenle</a>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <form method="post" action="<?= admin_url('?route=settings/save') ?>">
            <?= csrf_field() ?>
            <div class="tab-content">

                <!-- GENERAL ============================================== -->
                <div class="tab-pane fade show active" id="tab-general">
                    <div class="panel">
                        <div class="panel-header"><h6>Genel Site Ayarları</h6></div>
                        <div class="panel-body settings-section">
                            <p class="settings-help">İşletmenizin temel bilgilerini ayarlayın. Bu alanlar tüm panelde ve frontend'de kullanılır.</p>
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">Site Başlığı</label><input class="form-control" name="settings[site_title]" value="<?= e($s['site_title'] ?? '') ?>"></div>
                                <div class="col-md-6"><label class="form-label">İşletme Adı</label><input class="form-control" name="settings[business_name]" value="<?= e($s['business_name'] ?? '') ?>"></div>
                                <div class="col-12"><label class="form-label">Slogan</label><input class="form-control" name="settings[site_tagline]" value="<?= e($s['site_tagline'] ?? '') ?>" placeholder="Kısa, dikkat çekici slogan"></div>
                                <div class="col-md-4"><label class="form-label">Telefon</label><input class="form-control" name="settings[site_phone]" value="<?= e($s['site_phone'] ?? '') ?>"></div>
                                <div class="col-md-4"><label class="form-label">E-posta</label><input class="form-control" name="settings[site_email]" value="<?= e($s['site_email'] ?? '') ?>"></div>
                                <div class="col-md-4"><label class="form-label">WhatsApp Numarası</label><input class="form-control" name="settings[whatsapp_number]" value="<?= e($s['whatsapp_number'] ?? '') ?>" placeholder="+905xxxxxxxxx"></div>
                                <div class="col-12"><label class="form-label">Adres</label><input class="form-control" name="settings[site_address]" value="<?= e($s['site_address'] ?? '') ?>"></div>
                                <div class="col-12"><label class="form-label">Harita Embed Kodu</label><textarea class="form-control" rows="3" name="settings[map_embed]" placeholder="Google Maps iframe etiketi"><?= e($s['map_embed'] ?? '') ?></textarea><small class="form-help">Boş bırakılırsa Google Maps adresten otomatik üretilir.</small></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- THEME ============================================== -->
                <div class="tab-pane fade" id="tab-theme">
                    <div class="panel">
                        <div class="panel-header"><h6>Tema &amp; Marka</h6></div>
                        <div class="panel-body settings-section">
                            <p class="settings-help">Frontend rengini sektörünüze göre özelleştirin. Renkler tüm tanıtım sitesine uygulanır.</p>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Birincil Renk</label>
                                    <input type="color" class="form-control form-control-color" name="settings[theme_primary]" value="<?= e($s['theme_primary'] ?? '#4f46e5') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">İkincil Renk</label>
                                    <input type="color" class="form-control form-control-color" name="settings[theme_secondary]" value="<?= e($s['theme_secondary'] ?? '#0ea5e9') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Floating WhatsApp</label>
                                    <select class="form-select" name="settings[whatsapp_float]">
                                        <option value="1" <?= ($s['whatsapp_float'] ?? '1') === '1' ? 'selected' : '' ?>>Açık</option>
                                        <option value="0" <?= ($s['whatsapp_float'] ?? '1') === '0' ? 'selected' : '' ?>>Kapalı</option>
                                    </select>
                                </div>
                                <div class="col-md-3"><label class="form-label">İstatistik: Mutlu Müşteri</label><input class="form-control" name="settings[stat_happy_clients]" value="<?= e($s['stat_happy_clients'] ?? '') ?>"></div>
                                <div class="col-md-3"><label class="form-label">İstatistik: Destek</label><input class="form-control" name="settings[stat_support]" value="<?= e($s['stat_support'] ?? '') ?>"></div>
                                <div class="col-md-3"><label class="form-label">İstatistik: Uzman</label><input class="form-control" name="settings[stat_experts]" value="<?= e($s['stat_experts'] ?? '') ?>"></div>
                                <div class="col-md-3"><label class="form-label">İstatistik: Ödül</label><input class="form-control" name="settings[stat_awards]" value="<?= e($s['stat_awards'] ?? '') ?>"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RULES ============================================== -->
                <div class="tab-pane fade" id="tab-rules">
                    <div class="panel">
                        <div class="panel-header"><h6>Randevu Kuralları</h6></div>
                        <div class="panel-body settings-section">
                            <p class="settings-help">Randevu alımı için zaman kısıtlamalarını belirleyin.</p>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Minimum saat sonra randevu</label>
                                    <input class="form-control" type="number" min="0" name="settings[appointment_min_hours]" value="<?= e($s['appointment_min_hours'] ?? '2') ?>">
                                    <small class="form-help">Müşteri en az kaç saat sonrası için randevu alabilir?</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Maksimum gün sonra randevu</label>
                                    <input class="form-control" type="number" min="1" name="settings[appointment_max_days]" value="<?= e($s['appointment_max_days'] ?? '60') ?>">
                                    <small class="form-help">Randevular en fazla kaç gün ileri için alınabilir?</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Hatırlatma (saat önce)</label>
                                    <input class="form-control" type="number" name="settings[reminder_hours]" value="<?= e($s['reminder_hours'] ?? '24') ?>">
                                    <small class="form-help">Randevu hatırlatma mesajı kaç saat önce gönderilsin?</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Sessiz Saat Başlangıcı</label>
                                    <input class="form-control" type="time" name="settings[silent_mode_start]" value="<?= e($s['silent_mode_start'] ?? '22:00') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Sessiz Saat Bitişi</label>
                                    <input class="form-control" type="time" name="settings[silent_mode_end]" value="<?= e($s['silent_mode_end'] ?? '08:00') ?>">
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="settings[automation_reminder_sms]" value="0">
                                        <input type="checkbox" class="form-check-input" id="autoRem" name="settings[automation_reminder_sms]" value="1" <?= !empty($s['automation_reminder_sms']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="autoRem">Otomatik SMS hatırlatma</label>
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="settings[automation_cancel_notify]" value="0">
                                        <input type="checkbox" class="form-check-input" id="autoCanc" name="settings[automation_cancel_notify]" value="1" <?= !empty($s['automation_cancel_notify']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="autoCanc">İptal bildirimi gönder</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MAIL ============================================== -->
                <div class="tab-pane fade" id="tab-mail">
                    <div class="integration-card">
                        <div class="head">
                            <div>
                                <h6 class="mb-1"><i class="bi bi-envelope me-1"></i> SMTP / E-posta</h6>
                                <small class="text-muted">PHPMailer ile SMTP üzerinden e-posta gönderimi.</small>
                            </div>
                            <span class="status-pill <?= !empty($s['mail_host']) ? 'connected' : 'warning' ?>">
                                <span class="dot"></span><?= !empty($s['mail_host']) ? 'Yapılandırılmış' : 'Eksik' ?>
                            </span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-8"><label class="form-label">SMTP Host</label><input class="form-control" name="settings[mail_host]" value="<?= e($s['mail_host'] ?? '') ?>" placeholder="smtp.example.com"></div>
                            <div class="col-md-4"><label class="form-label">Port</label><input class="form-control" name="settings[mail_port]" value="<?= e($s['mail_port'] ?? '587') ?>"></div>
                            <div class="col-md-6"><label class="form-label">Kullanıcı Adı</label><input class="form-control" name="settings[mail_username]" value="<?= e($s['mail_username'] ?? '') ?>"></div>
                            <div class="col-md-6"><label class="form-label">Şifre</label><input class="form-control" type="password" name="settings[mail_password]" value="<?= e($s['mail_password'] ?? '') ?>"></div>
                            <div class="col-md-4"><label class="form-label">Şifreleme</label><select class="form-select" name="settings[mail_encryption]"><option value="tls" <?= ($s['mail_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option><option value="ssl" <?= ($s['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option><option value="">Yok</option></select></div>
                            <div class="col-md-4"><label class="form-label">Gönderen E-posta</label><input class="form-control" name="settings[mail_from_email]" value="<?= e($s['mail_from_email'] ?? '') ?>"></div>
                            <div class="col-md-4"><label class="form-label">Gönderen Adı</label><input class="form-control" name="settings[mail_from_name]" value="<?= e($s['mail_from_name'] ?? '') ?>"></div>
                        </div>
                    </div>
                </div>

                <!-- NETGSM ============================================== -->
                <div class="tab-pane fade" id="tab-netgsm">
                    <div class="integration-card">
                        <div class="head">
                            <div>
                                <h6 class="mb-1"><i class="bi bi-chat-dots me-1"></i> NetGSM SMS</h6>
                                <small class="text-muted">NetGSM XML API üzerinden SMS gönderimi.</small>
                            </div>
                            <span class="status-pill <?= ($s['netgsm_status'] ?? 0) ? 'connected' : 'warning' ?>">
                                <span class="dot"></span><?= ($s['netgsm_status'] ?? 0) ? 'Aktif' : 'Pasif' ?>
                            </span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Kullanıcı Kodu</label><input class="form-control" name="settings[netgsm_usercode]" value="<?= e($s['netgsm_usercode'] ?? '') ?>"></div>
                            <div class="col-md-6"><label class="form-label">Şifre</label><input class="form-control" type="password" name="settings[netgsm_password]" value="<?= e($s['netgsm_password'] ?? '') ?>"></div>
                            <div class="col-md-6"><label class="form-label">Mesaj Başlığı (Header)</label><input class="form-control" name="settings[netgsm_header]" value="<?= e($s['netgsm_header'] ?? '') ?>"></div>
                            <div class="col-md-6"><label class="form-label">Endpoint</label><input class="form-control" name="settings[netgsm_endpoint]" value="<?= e($s['netgsm_endpoint'] ?? '') ?>" placeholder="https://api.netgsm.com.tr/sms/send/xml"></div>
                            <div class="col-md-4">
                                <label class="form-label">Durum</label>
                                <select class="form-select" name="settings[netgsm_status]">
                                    <option value="1" <?= ($s['netgsm_status'] ?? 0) ? 'selected' : '' ?>>Aktif</option>
                                    <option value="0" <?= !($s['netgsm_status'] ?? 0) ? 'selected' : '' ?>>Pasif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WHATSAPP ============================================== -->
                <div class="tab-pane fade" id="tab-whatsapp">
                    <div class="integration-card">
                        <div class="head">
                            <div>
                                <h6 class="mb-1"><i class="bi bi-whatsapp me-1"></i> WhatsApp Business Cloud API</h6>
                                <small class="text-muted">Meta Cloud API üzerinden WhatsApp şablon mesajları.</small>
                            </div>
                            <span class="status-pill <?= ($s['whatsapp_status'] ?? 0) ? 'connected' : 'warning' ?>">
                                <span class="dot"></span><?= ($s['whatsapp_status'] ?? 0) ? 'Bağlı' : 'Yapılandırma Gerekli' ?>
                            </span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Phone Number ID</label><input class="form-control" name="settings[whatsapp_phone_number_id]" value="<?= e($s['whatsapp_phone_number_id'] ?? '') ?>"></div>
                            <div class="col-md-6"><label class="form-label">Business Account ID</label><input class="form-control" name="settings[whatsapp_business_account_id]" value="<?= e($s['whatsapp_business_account_id'] ?? '') ?>"></div>
                            <div class="col-12"><label class="form-label">Access Token</label><input class="form-control" type="password" name="settings[whatsapp_access_token]" value="<?= e($s['whatsapp_access_token'] ?? '') ?>"></div>
                            <div class="col-md-4"><label class="form-label">Verify Token (webhook)</label><input class="form-control" name="settings[whatsapp_verify_token]" value="<?= e($s['whatsapp_verify_token'] ?? '') ?>"></div>
                            <div class="col-md-4"><label class="form-label">API Versiyonu</label><input class="form-control" name="settings[whatsapp_api_version]" value="<?= e($s['whatsapp_api_version'] ?? 'v18.0') ?>"></div>
                            <div class="col-md-4"><label class="form-label">Varsayılan Dil</label><input class="form-control" name="settings[whatsapp_default_language]" value="<?= e($s['whatsapp_default_language'] ?? 'tr') ?>"></div>
                            <div class="col-md-4">
                                <label class="form-label">Durum</label>
                                <select class="form-select" name="settings[whatsapp_status]">
                                    <option value="1" <?= ($s['whatsapp_status'] ?? 0) ? 'selected' : '' ?>>Aktif</option>
                                    <option value="0" <?= !($s['whatsapp_status'] ?? 0) ? 'selected' : '' ?>>Pasif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PAYMENT ============================================== -->
                <div class="tab-pane fade" id="tab-payment">
                    <div class="integration-card">
                        <div class="head">
                            <div>
                                <h6 class="mb-1"><i class="bi bi-credit-card me-1"></i> Ödeme Sağlayıcı</h6>
                                <small class="text-muted">İyzico veya PayTR seçin, anahtarlarınızı girin.</small>
                            </div>
                            <span class="status-pill <?= ($s['payment_status'] ?? 0) ? 'connected' : 'warning' ?>">
                                <span class="dot"></span><?= ($s['payment_status'] ?? 0) ? 'Aktif' : 'Pasif' ?>
                            </span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Sağlayıcı</label>
                                <select class="form-select" name="settings[payment_provider]">
                                    <option value="iyzico" <?= ($s['payment_provider'] ?? '') === 'iyzico' ? 'selected' : '' ?>>iyzico</option>
                                    <option value="paytr" <?= ($s['payment_provider'] ?? '') === 'paytr' ? 'selected' : '' ?>>PayTR</option>
                                    <option value="manual" <?= ($s['payment_provider'] ?? '') === 'manual' ? 'selected' : '' ?>>Manuel</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Durum</label>
                                <select class="form-select" name="settings[payment_status]">
                                    <option value="1" <?= ($s['payment_status'] ?? 0) ? 'selected' : '' ?>>Aktif</option>
                                    <option value="0" <?= !($s['payment_status'] ?? 0) ? 'selected' : '' ?>>Pasif</option>
                                </select>
                            </div>
                            <div class="col-12"><hr><small class="text-muted">iyzico anahtarları</small></div>
                            <div class="col-md-6"><label class="form-label">iyzico API Key</label><input class="form-control" name="settings[iyzico_api_key]" value="<?= e($s['iyzico_api_key'] ?? '') ?>"></div>
                            <div class="col-md-6"><label class="form-label">iyzico Secret</label><input class="form-control" type="password" name="settings[iyzico_secret_key]" value="<?= e($s['iyzico_secret_key'] ?? '') ?>"></div>
                            <div class="col-12"><label class="form-label">iyzico Base URL</label><input class="form-control" name="settings[iyzico_base_url]" value="<?= e($s['iyzico_base_url'] ?? '') ?>" placeholder="https://api.iyzipay.com"></div>
                            <div class="col-12"><hr><small class="text-muted">PayTR anahtarları</small></div>
                            <div class="col-md-4"><label class="form-label">PayTR Merchant ID</label><input class="form-control" name="settings[paytr_merchant_id]" value="<?= e($s['paytr_merchant_id'] ?? '') ?>"></div>
                            <div class="col-md-4"><label class="form-label">PayTR Merchant Key</label><input class="form-control" name="settings[paytr_merchant_key]" value="<?= e($s['paytr_merchant_key'] ?? '') ?>"></div>
                            <div class="col-md-4"><label class="form-label">PayTR Merchant Salt</label><input class="form-control" name="settings[paytr_merchant_salt]" value="<?= e($s['paytr_merchant_salt'] ?? '') ?>"></div>
                        </div>
                    </div>
                </div>

                <!-- SEO ============================================== -->
                <div class="tab-pane fade" id="tab-seo">
                    <div class="panel">
                        <div class="panel-header"><h6>SEO &amp; Sosyal Medya</h6></div>
                        <div class="panel-body settings-section">
                            <p class="settings-help">Arama motorları ve sosyal medya için meta bilgilerinizi ayarlayın.</p>
                            <div class="row g-3">
                                <div class="col-12"><label class="form-label">SEO Başlığı</label><input class="form-control" name="settings[seo_title]" value="<?= e($s['seo_title'] ?? '') ?>"></div>
                                <div class="col-12"><label class="form-label">SEO Açıklaması</label><textarea class="form-control" rows="2" name="settings[seo_description]"><?= e($s['seo_description'] ?? '') ?></textarea></div>
                                <div class="col-md-6"><label class="form-label">Facebook URL</label><input class="form-control" name="settings[social_facebook]" value="<?= e($s['social_facebook'] ?? '') ?>"></div>
                                <div class="col-md-6"><label class="form-label">Instagram URL</label><input class="form-control" name="settings[social_instagram]" value="<?= e($s['social_instagram'] ?? '') ?>"></div>
                                <div class="col-md-6"><label class="form-label">Twitter URL</label><input class="form-control" name="settings[social_twitter]" value="<?= e($s['social_twitter'] ?? '') ?>"></div>
                                <div class="col-md-6"><label class="form-label">YouTube URL</label><input class="form-control" name="settings[social_youtube]" value="<?= e($s['social_youtube'] ?? '') ?>"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check2 me-1"></i> Tüm Ayarları Kaydet</button>
            </div>
        </form>
    </div>
</div>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
