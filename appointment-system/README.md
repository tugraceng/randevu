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

## Yeni Modüller (Frontend & Admin Geliştirmesi)

- **Modern tanıtım sitesi:** `app/Views/frontend/` partials, `public/assets/frontend/` CSS/JS, adım adım randevu modalı, galeri, harita, floating WhatsApp, mobil CTA
- **Gelişmiş admin panel:** Sidebar, breadcrumb, Chart.js dashboard, randevu CRUD (liste, oluştur, detay, düzenle, takvim)
- **Müşteri detay sekmeleri:** Genel, randevular, paketler, ödemeler, mesajlar, notlar
- **Paket/seans yönetimi:** Detay sayfası, manuel seans ekleme/çıkarma, seans logları
- **İçerik CMS:** SSS, galeri, yorumlar + `page_sections`
- **Rapor CSV dışa aktarma:** `?route=reports/export&from=&to=`
- **SQL güncelleme:** `database/update_front_admin_improvements.sql` (customer_notes tablosu, varsayılan ayarlar)

## Admin Panel Kullanım Özeti

| Modül | Route | Açıklama |
|-------|-------|----------|
| Dashboard | `/` | İstatistik kartları, Chart.js grafikleri |
| Randevular | `appointments` | Filtreleme, hızlı onay, detay işlemleri |
| Müşteriler | `customers/show&id=` | Sekmeli detay, paket atama, not |
| Paketler | `packages/show&id=` | Seans logları, manuel düzenleme |
| İçerik | `content` | Hero, SSS, galeri, yorumlar |
| Raporlar | `reports/export` | CSV indirme |

## Müşteri Panel Kullanım Özeti

- **Dashboard:** Yaklaşan randevular, aktif paketler, ödemeler
- **Randevu al:** Hizmet → personel → tarih/saat (AJAX slotlar)
- **Paketlerim / Ödemelerim / Profil:** Mevcut modüller

## Entegrasyon Ayarları

Admin → Ayarlar: Genel, SMTP, NetGSM, WhatsApp, ödeme, randevu kuralları, tema/SEO.

## Cron Hatırlatma

```bash
php cron/reminders.php
```

Saatlik çalıştırın; yaklaşan randevular için şablon mesajları gönderir.

## Lisans

Özel proje - ticari kullanım için geliştirilmiştir.
