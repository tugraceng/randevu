USE appointment_system;

INSERT INTO admins (name, email, password, role, status) VALUES
('Süper Admin', 'admin@randevu.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 1);

INSERT INTO settings (setting_key, setting_value) VALUES
('site_title', 'RandevuTakip'),
('site_tagline', 'Modern Bakım, Profesyonel Hizmet'),
('site_phone', '+90 555 000 00 00'),
('site_whatsapp', '905550000000'),
('site_email', 'info@example.com'),
('site_address', 'İstanbul, Türkiye'),
('business_name', 'RandevuTakip Klinik'),
('netgsm_usercode', ''),
('netgsm_password', ''),
('netgsm_header', ''),
('netgsm_endpoint', 'https://api.netgsm.com.tr/sms/send/get'),
('netgsm_status', '0'),
('whatsapp_phone_number_id', ''),
('whatsapp_business_account_id', ''),
('whatsapp_access_token', ''),
('whatsapp_verify_token', 'randevu_webhook_secret'),
('whatsapp_api_version', 'v20.0'),
('whatsapp_default_language', 'tr'),
('whatsapp_status', '0'),
('payment_provider', 'iyzico'),
('payment_status', '0'),
('iyzico_api_key', ''),
('iyzico_secret_key', ''),
('iyzico_base_url', 'https://sandbox-api.iyzipay.com'),
('paytr_merchant_id', ''),
('paytr_merchant_key', ''),
('paytr_merchant_salt', ''),
('mail_host', 'smtp.gmail.com'),
('mail_port', '587'),
('mail_username', ''),
('mail_password', ''),
('mail_encryption', 'tls'),
('mail_from_email', 'noreply@example.com'),
('mail_from_name', 'RandevuTakip'),
('reminder_hours', '24'),
('silent_mode_start', '22:00'),
('silent_mode_end', '08:00'),
('automation_reminder_sms', '1'),
('automation_cancel_notify', '1'),
('stat_happy_clients', '15000'),
('stat_support', '7/24'),
('stat_experts', '45'),
('stat_awards', '12');

INSERT INTO page_sections (section_key, title, subtitle, content, status, sort_order) VALUES
('hero', 'Modern Bakım, Profesyonel Hizmet', 'RandevuTakip Klinik', 'Teknoloji ve uzman kadromuzla yaşam kalitenizi yükseltiyoruz. Online randevu alın, paketlerimizi keşfedin.', 1, 1),
('about', 'Hakkımızda', 'Güvenilir Sağlık Partneriniz', 'Diş kliniği, güzellik merkezi, spa ve danışmanlık alanlarında çok sektörlü hizmet sunuyoruz.', 1, 2),
('contact', 'İletişim', 'Bize Ulaşın', 'Sorularınız için bize yazın veya hemen randevu alın.', 1, 10);

INSERT INTO services (name, slug, description, duration_minutes, price, deposit_price, status, sort_order) VALUES
('Diş Temizliği', 'dis-temizligi', 'Profesyonel diş temizliği ve bakım.', 45, 450.00, 100.00, 1, 1),
('Derin Doku Masajı', 'derin-doku-masaji', 'Rahatlatıcı derin doku masajı.', 60, 750.00, 150.00, 1, 2),
('Bireysel Danışmanlık', 'bireysel-danismanlik', 'Uzman danışmanlık seansı.', 50, 1200.00, 200.00, 1, 3);

INSERT INTO staff (name, title, bio, status) VALUES
('Dr. Ayşe Yılmaz', 'Kıdemli Diş Hekimi', '15 yıllık deneyim.', 1),
('Zeynep Kaya', 'Yaşam Koçu', 'Bireysel ve kurumsal danışmanlık.', 1);

INSERT INTO staff_services (staff_id, service_id) VALUES (1, 1), (1, 2), (2, 3);

INSERT INTO packages (service_id, name, description, session_count, price, validity_days, status) VALUES
(2, 'Başlangıç Paketi', '5 seans masaj paketi', 5, 1200.00, 90, 1),
(2, 'Premium Paket', '10 seans masaj paketi - En çok tercih edilen', 10, 2500.00, 180, 1),
(2, 'Platinum Plus', '20 seans masaj paketi', 20, 4800.00, 365, 1);

INSERT INTO working_hours (staff_id, day_of_week, start_time, end_time, is_closed) VALUES
(NULL, 1, '09:00:00', '18:00:00', 0),
(NULL, 2, '09:00:00', '18:00:00', 0),
(NULL, 3, '09:00:00', '18:00:00', 0),
(NULL, 4, '09:00:00', '18:00:00', 0),
(NULL, 5, '09:00:00', '18:00:00', 0),
(NULL, 6, '10:00:00', '14:00:00', 0),
(NULL, 0, '00:00:00', '00:00:00', 1);

INSERT INTO reviews (customer_name, comment, rating, status, sort_order) VALUES
('Mehmet A.', 'Harika bir deneyim, kesinlikle tavsiye ederim.', 5, 1, 1),
('Elif K.', 'Personel çok ilgili, randevu sistemi çok pratik.', 5, 1, 2);

INSERT INTO faqs (question, answer, status, sort_order) VALUES
('Online randevu nasıl alınır?', 'Kayıt olup e-postanızı doğruladıktan sonra müşteri panelinden veya ana sayfadaki formdan randevu alabilirsiniz.', 1, 1),
('Paket seansları nasıl kullanılır?', 'Aktif paketiniz varsa randevu oluştururken paketinizi seçebilirsiniz. Tamamlanan randevularda seans otomatik düşer.', 1, 2);

INSERT INTO message_templates (channel, template_key, title, subject, body, provider_template_name, language_code, status) VALUES
('sms', 'appointment_created', 'Randevu Oluşturuldu', NULL, 'Sayın {name}, {date} saat {time} için {service} randevunuz oluşturulmuştur. - {business_name}', NULL, 'tr', 1),
('sms', 'appointment_reminder', 'Randevu Hatırlatma', NULL, 'Sayın {name}, {date} saat {time} randevunuz bulunmaktadır. - {business_name}', NULL, 'tr', 1),
('sms', 'appointment_cancelled', 'Randevu İptal', NULL, 'Sayın {name}, {date} saat {time} randevunuz iptal edilmiştir.', NULL, 'tr', 1),
('sms', 'otp', 'OTP Doğrulama', NULL, 'Doğrulama kodunuz: {otp}', NULL, 'tr', 1),
('sms', 'campaign_message', 'Kampanya SMS', NULL, 'Sayın {name}, {campaign}', NULL, 'tr', 1),
('sms', 'package_remaining', 'Kalan Seans', NULL, 'Sayın {name}, {package} paketinizde {remaining_sessions} seans kaldı.', NULL, 'tr', 1),
('whatsapp', 'appointment_created', 'WA Randevu Oluşturuldu', NULL, 'Merhaba {name}, {date} saat {time} için {service} randevunuz oluşturuldu.', 'appointment_created', 'tr', 1),
('whatsapp', 'appointment_approved', 'WA Randevu Onaylandı', NULL, 'Merhaba {name}, {date} saat {time} randevunuz onaylandı.', 'appointment_approved', 'tr', 1),
('whatsapp', 'appointment_cancelled', 'WA Randevu İptal', NULL, 'Merhaba {name}, randevunuz iptal edildi.', 'appointment_cancelled', 'tr', 1),
('whatsapp', 'appointment_reminder', 'WA Hatırlatma', NULL, 'Merhaba {name}, yarın saat {time} randevunuz var.', 'appointment_reminder', 'tr', 1),
('whatsapp', 'campaign_message', 'WA Kampanya', NULL, 'Merhaba {name}, kampanyamız: {campaign}', 'campaign_message', 'tr', 1),
('whatsapp', 'package_remaining', 'WA Kalan Seans', NULL, 'Merhaba {name}, {package} paketinde {remaining_sessions} seans kaldı.', 'package_remaining', 'tr', 1),
('email', 'verify_email', 'E-posta Doğrulama', 'E-posta Adresinizi Doğrulayın', 'Merhaba {name}, e-posta adresinizi doğrulamak için şu bağlantıya tıklayın: {verification_link}', NULL, 'tr', 1),
('email', 'appointment_created', 'Randevu Oluşturuldu', 'Randevunuz Oluşturuldu', 'Merhaba {name}, {date} saat {time} için {service} randevunuz oluşturuldu.', NULL, 'tr', 1),
('email', 'appointment_approved', 'Randevu Onaylandı', 'Randevunuz Onaylandı', 'Merhaba {name}, {date} saat {time} randevunuz onaylandı.', NULL, 'tr', 1),
('email', 'appointment_cancelled', 'Randevu İptal', 'Randevunuz İptal Edildi', 'Merhaba {name}, randevunuz iptal edildi.', NULL, 'tr', 1),
('email', 'appointment_reminder', 'Randevu Hatırlatma', 'Randevu Hatırlatması', 'Merhaba {name}, {date} saat {time} randevunuzu hatırlatırız.', NULL, 'tr', 1),
('email', 'payment_success', 'Ödeme Başarılı', 'Ödemeniz Alındı', 'Merhaba {name}, {amount} tutarındaki ödemeniz başarıyla alındı.', NULL, 'tr', 1);
