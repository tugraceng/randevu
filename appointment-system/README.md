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

## UI/UX Büyük Güncelleme

Frontend, admin paneli ve müşteri paneli baştan sona "satılabilir, modern ve premium" anlayışla yeniden tasarlandı. Bootstrap 5 temel alındı, fakat klasik Bootstrap görünümünden uzaklaşmak için tüm bileşenler özel CSS değişkenleri ile kapsamlı şekilde özelleştirildi.

### Tasarım Dili

- **Tipografi:** `Inter` (Google Fonts)
- **Renk sistemi:** `--primary`, `--secondary`, `--accent` değişkenleri admin → Ayarlar üzerinden tema renkleriyle özelleştirilebilir
- **Yumuşak gölgeler + 14-20px köşe radius'u** (premium hissi)
- **Bol beyaz alan, hava + büyük başlıklar** (multi-sektör uyum)
- **Mobil öncelik:** Tüm modüller mobilde rahat çalışacak şekilde grid/flex ile yeniden tasarlandı

### Frontend (Tanıtım Sitesi)

- **One-page yapı:** Hero, güven şeridi, hizmetler, paketler, randevu teaser, ekip, kampanyalar, galeri, yorumlar, SSS, iletişim
- **Yapışkan navbar:** Scroll-spy, mobil hamburger, "Randevu Al" CTA
- **Çok adımlı randevu modal'ı:** Hizmet → Personel → Tarih/Saat → Onay (AJAX slot, canlı özet kartı, üye değilse login/register/doğrulama yönlendirmesi)
- **Floating WhatsApp düğmesi + mobil tam genişlik CTA**
- **Lightbox galeri, scroll-reveal animasyonları, smooth-scroll**
- **Asset düzeni:** `public/assets/css/frontend.css` (bölümlere ayrılmış), `public/assets/js/frontend.js`, `public/assets/js/appointment.js`

### Admin Paneli (SaaS Dashboard)

- **Layout:** Dark navy kalıcı sidebar + topbar + breadcrumb + kullanıcı menüsü + mobil overlay
- **Dashboard:** Bugünkü randevular, durum kartları, müşteri/paket/seans uyarıları, aylık ciro, ödeme bekleyen, SMS/WhatsApp/Mail sayaçları, Chart.js grafikleri
- **Randevular:** Tarih aralığı + durum + hizmet + personel + ödeme + paket filtreleri, hızlı aksiyon butonları, mobil kart görünümü
- **Yeni Randevu:** Yeni müşteriyi AJAX ile aynı sayfada oluşturma, aktif paketleri otomatik listeleme, canlı özet kartı, bildirim seçenekleri (Mail/SMS/WhatsApp)
- **Müşteri CRM:** Sekmeli detay (genel, randevular, paketler, ödemeler, mesajlar, notlar timeline), hızlı paket ataması, kara liste, not ekleme
- **Paketler:** Seans progress bar, manuel seans ekle/çıkar, seans aktivite logları timeline, paket-randevu eşleştirme
- **Personel:** Fotoğraf yükleme, uzmanlık etiketleri, gün-gün çalışma saatleri tablosu
- **Hizmetler:** Görsel kartlar, inline create/edit modal'ları, image upload
- **Ayarlar:** 8 sekmeli yapı (Genel, Tema, Randevu, SMTP, NetGSM, WhatsApp, Ödeme, SEO) + şablon değişken cheat sheet
- **Bildirimler:** Şablon yönetimi (SMS/WhatsApp/Mail) + değişken paneli (`{ad}`, `{tarih}`, `{saat}` vs.) + mesaj log tablosu
- **Raporlar:** Tarih filtresi, KPI kartları, ciro/durum grafikleri, CSV dışa aktarma

### Müşteri Paneli

- Yeniden tasarlanmış sidebar + topbar + hoşgeldin hero
- Dashboard'da yaklaşan randevu, aktif paket, ödeme bekleyen KPI kartları
- Loyalty / paket sadakat kartı (progress bar ile kalan seans)
- Stepper'lı randevu oluşturma akışı

### Asset / Helper

- `public/assets/css/frontend.css`, `admin.css`, `customer.css` → bölümlere ayrılmış (1 = değişkenler, 2 = layout, 3 = components, …)
- `public/assets/js/frontend.js`, `appointment.js`, `admin.js` → yorum bloklu / sectioned modüler yapı
- `app/Helpers/layout.php` → `render_status_badge()`, `render_breadcrumb()`, `render_pagination()`
- `database/update_front_admin_improvements.sql` → yeni tablolar (`customer_notes`, `working_hours`), `staff` & `customers` ek alanları, default tema/SEO ayarları

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

- **Dashboard:** Yaklaşan randevular, aktif paketler, ödemeler (KPI kartları + loyalty card)
- **Randevu al:** Hizmet → personel → tarih/saat (AJAX slotlar, stepper)
- **Paketlerim:** Aktif paket progress bar + satın alınabilir paketler
- **Ödemelerim / Profilim:** Modern kart + tablo + ayrı şifre değiştirme formu

## Entegrasyon Ayarları

Admin → Ayarlar: Genel, SMTP, NetGSM, WhatsApp, ödeme, randevu kuralları, tema/SEO.

## Cron Hatırlatma

```bash
php cron/reminders.php
```

Saatlik çalıştırın; yaklaşan randevular için şablon mesajları gönderir.

## Lisans

Özel proje - ticari kullanım için geliştirilmiştir.
