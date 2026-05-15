# RandevuTakip - Çok Sektörlü Online Randevu Sistemi

PHP 8+, MySQL, PDO, Bootstrap 5 ile geliştirilmiş randevu, paket/seans, ödeme ve bildirim platformu.

## Kurulum (XAMPP)

1. Projeyi `c:\xampp\htdocs\randevu\appointment-system` altına yerleştirin.
2. Composer bağımlılıklarını yükleyin:
   ```bash
   cd appointment-system
   composer install
   ```
3. MySQL'de veritabanını oluşturun:
   ```bash
   mysql -u root < database/appointment_system.sql
   mysql -u root appointment_system < database/seed.sql
   ```
4. `config/database.php` veya ortam değişkenleri ile DB bilgilerini ayarlayın.
5. `config/app.php` içindeki URL'leri güncelleyin:
   - `url` → `http://localhost/randevu/appointment-system/public`
   - `admin_url` → `http://localhost/randevu/appointment-system/admin`
   - `customer_url` → `http://localhost/randevu/appointment-system/customer`

## Giriş Bilgileri (Seed)

| Panel | E-posta | Şifre |
|-------|---------|-------|
| Admin | admin@randevu.local | password |

## Erişim URL'leri

- **Tanıtım sitesi:** `/public/`
- **Müşteri paneli:** `/customer/`
- **Admin paneli:** `/admin/`
- **Ödeme webhook:** `/webhook/payment.php`
- **WhatsApp webhook:** `/webhook/whatsapp.php`

## Modüller

- One page tanıtım sitesi (admin'den içerik yönetimi)
- Müşteri paneli (kayıt, e-posta doğrulama, randevu, paket, ödeme)
- Admin paneli (dashboard, hizmet, personel, müşteri, randevu, paket, kampanya, şablonlar, rapor)
- Paket/seans takibi (`PackageSessionService`)
- Bildirimler: `NotificationService` → Mail, NetGSM SMS, WhatsApp Cloud API
- Ödeme: `PaymentService` → iyzico / PayTR / Manuel (`PaymentGatewayInterface`)

## Cron - Randevu Hatırlatma

```bash
php cron/reminders.php
```

Windows Görev Zamanlayıcı veya Linux crontab ile saatlik çalıştırın.

## Güvenlik

- CSRF token (tüm POST formları)
- PDO prepared statements
- `password_hash` / `password_verify`
- Admin ve müşteri session ayrımı
- Rate limiting (giriş/kayıt)
- API anahtarları `settings` tablosunda (kod içinde değil)

## Entegrasyon Ayarları

Admin → Ayarlar sekmesinden:

- NetGSM (usercode, password, header)
- WhatsApp Business Cloud API (token, phone number id, verify token)
- iyzico / PayTR ödeme anahtarları
- SMTP (PHPMailer)

## Klasör Yapısı

```
appointment-system/
├── app/Controllers, Models, Services, Views
├── config/
├── database/
├── public/          # Ana site + assets
├── admin/           # Admin giriş noktası
├── customer/        # Müşteri giriş noktası
├── webhook/
└── cron/
```

## Lisans

Özel proje - ticari kullanım için geliştirilmiştir.
