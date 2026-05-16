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

## UI/UX Revamp (Premium SaaS Edition)

Sistem "çalışan proje" seviyesinden çıkarılıp **premium SaaS / ticari ürün** seviyesine taşındı. Backend mantığı korunarak yalnızca frontend / admin / customer UI katmanı sıfırdan inşa edildi.

### Tasarım Dili

- **Modern SaaS · Premium · Minimal ama güçlü**
- Yumuşak gölgeler, geniş köşe radius'ları (12–28px), ferah boşluk kullanımı
- Gradient CTA / hero / KPI alanları, glassmorphism (navbar + topbar)
- Hover animasyonları + smooth transition (cubic-bezier easing)
- **Inter** font sistemi, güçlü heading hiyerarşisi
- Tüm renkler `--primary` / `--secondary` CSS değişkenleri ile admin Ayarlar üzerinden değiştirilebilir
- Mobile-first responsive (575 / 767 / 991 / 1199 breakpoint'leri)

### CSS Mimarisi — Yeniden Yazıldı

`public/assets/css/frontend.css`, `admin.css`, `customer.css` dosyalarının üçü de **bölüm bölüm yorumlanmış**, sürdürülebilir ve okunabilir bir mimariye geçti.

Bölüm yapısı (örnek admin.css):
```
01 Root Variables       08 Badges & Status Pills   15 Payment Module
02 Reset & Base          09 Tables                   16 Reports
03 Typography            10 Modals                   17 Settings (Tabs)
04 Layout (Shell)        11 Dashboard (KPI/Charts)   18 Messages / Templates
05 Buttons               12 Appointment Module       19 Empty / Loading / Skeleton
06 Forms                 13 Package Module           20 Toast / Alert / Confirm
07 Cards / Panels        14 Customer Module (CRM)    21 Auth · 22 Utility · 23 Responsive
```

Her dosyada **root variables + spacing scale + radius scale + shadow scale + transition tokens + utility helpers** tanımlandı.

### Premium Frontend (Landing)

- **Hero**: Gradient blur shapes (`hero-float` animasyon), eğik glassmorphism preview card, "Bugün **X** kişi randevu aldı" canlı strip (`data-live-count`), trust badge'ler, çift CTA (Online Randevu / WhatsApp)
- **Animated counters**: `data-counter="15000"` ile hero istatistikleri görünür olunca animasyonlu sayar
- **Hizmet kartları**: Görsel zoom + hover gradient overlay
- **Paket kartları**: Featured (`Popüler`) için gradient arka plan, scale + glow, benefit listesi
- **Booking modal (`#appointmentModal`)** – tamamen yeniden tasarlandı:
  - 2 sütunlu layout (Ana panel + sticky özet kart)
  - 6 adımlı stepper (Hizmet → Personel → Tarih → Saat → Not → Onay)
  - `choice-tile` (büyük seçim kutuları), `slot-grid` (saat kartları)
  - **Skeleton loader** ile AJAX slot yükleme
  - Canlı `data-sum="..."` summary card (hizmet/personel/tarih/saat/tutar)
  - Geri / İleri / Randevu Oluştur akışı (`appointment.js`)
- **Footer**: Gradient dark + brand icon + sosyal medya + KVKK linkleri
- **Floating WhatsApp**: çift halka animasyonu (`wa-pulse` + `wa-ring`)
- **Mobil**: alt yapışkan CTA bar (`Hızlı Randevu Al`)
- **Lightbox** galeri (kapatma `Esc` ile)
- **Scroll-reveal** `[data-reveal]` etkilenir

### Premium Admin Panel (SaaS Dashboard)

- **Layout**: Dark navy sticky sidebar (radial gradient highlight) + light content + **collapsible sidebar** (`localStorage` ile hatırlanan) + mobil overlay sidebar
- **Topbar**: Glassmorphism (blur), arama kutusu, hızlı randevu kısayolu, bildirim dropdown'u (kategorili), kullanıcı dropdown'u
- **KPI Cards**: Gradient top border, ikon kutucuk, trend pill (↑ %12 / ↓ %3), `data-counter` animasyonlu sayar — `k-success`, `k-warning`, `k-danger`, `k-info` ton varyasyonları
- **Chart.js dashboard**: Line (gradient fill), doughnut, bar grafikler — admin.js içinden `?route=dashboard/chart-data&type=...` ile lazy load
- **Tablolar (`.table-rounded`)**: Avatar + müşteri bloğu, hover row, `.status-pill` (renkli noktalı) durum badge'leri, `.btn-icon` quick actions, mobilde `.list-card` görünüm
- **Empty states**: İkon dairesel + başlık + açıklama + CTA
- **Settings ekranı (`.settings-shell`)**: Sol dikey tab listesi (sticky) + sağda `.settings-card`. SMTP / NetGSM / WhatsApp / Payment için `data-test-endpoint` ile **bağlantı testi** butonu (admin.js)
- **Mesaj şablonları**: `.var-panel` + `.var-btn` ile değişken ekleme (`{name}`, `{date}`, ...) — textarea cursor pozisyonuna ekleniyor
- **Auth ekranı**: `.auth-shell` + `.auth-card` premium gradient background

### Premium Customer Portal

- Sidebar + topbar + ferah içerik alanı
- Dashboard'da **`loyalty-card`** (gradient + dekoratif daireler + paket progress)
- KPI stat kartlar (`.c-stat` gradient top border)
- Stepper'lı randevu oluşturma akışı + AJAX slot
- Modernize tablolar (geçmiş randevular / ödemeler)

### JavaScript Modülleri — Yeniden Yazıldı

| Dosya | İçerik |
|-------|--------|
| `public/assets/js/frontend.js` | Sticky navbar, smooth scroll, scroll-spy, scroll-reveal, animated counters, mobile CTA, lightbox, live hero count |
| `public/assets/js/appointment.js` | 6 adımlı stepper, choice-tile binding, skeleton-loaded slot grid, live summary card, AJAX slot loader, hidden input bridging |
| `public/assets/js/admin.js` | Toast sistemi (success/error/warning/info), Confirm modal (Promise tabanlı), Sidebar collapse + mobile overlay, [data-confirm] generic handler, Filter reset / auto-filter, Template variable inserter, Chart.js dashboard renderer, Quick customer create AJAX, Customer package loader, Form submit loaders, Settings test buttons, Animated counters |

### Toast / Confirm / Loading

- **Toast**: `Admin.toast(message, type, timeout)` — sağ üstte slide-in animasyonu, kategorize ikonlar
- **Confirm modal**: `Admin.confirm(message)` Promise döner, tüm `data-confirm="..."` elementleri otomatik bağlanır
- **Skeleton loader**: `.skeleton`, `.skeleton-line`, `.skeleton-block`, `.skeleton-circle` — shimmer animasyonu
- **Button loading**: `btn[data-loading="true"]` → spinner overlay (text gizlenir, animasyon görünür)
- **Empty state**: `.empty-state .icon` + başlık + açıklama + CTA

### Mobil Deneyim

- 991px altı: Sidebar mobil overlay'e dönüşür, topbar arama gizlenir, kullanıcı adı kısalır
- 767px altı: Sayfa padding azalır, tablolar yan kaydırılır, hero ortalanır
- Frontend mobilde: alt yapışkan CTA bar, WhatsApp floating button konum güncellenir

### Yardımcı (`status_badge`)

Helper artık `<span class="status-pill status-{status}">` üretir; renk her durum için CSS'te tanımlı.

---

## UI/UX Polish v2 — Unified Design Language

Tüm ekranlar (frontend, admin, customer) **tek bir design language** altında birleştirildi. v1'de bırakılan tutarsızlıklar tek tek kapatıldı:

### Design system v2 (yeni eklenenler)

| Token / Helper | Ne yapar |
|----------------|----------|
| `.page-section` | Tutarlı vertical rhythm — section'lar arası standardize boşluk |
| `.section-divider`, `.shimmer-divider` | Hafif ayraçlar, sayfa içinde nefes |
| `.hover-lift` | Tek class ile premium hover (transform + shadow). Tüm panel/kpi/stat/table-rounded'a JS ile otomatik atanır |
| `[data-reveal]` + global IntersectionObserver | Admin + customer paneldeki kartlar görünür olduğunda fade-up animasyonu |
| `.fade-up` | `.admin-content` ve `.customer-content` sayfa açılışında smooth fade-up |
| `.glass-card` | Yumuşak glassmorphism (frontend + admin) |
| `.bottom-sheet` | Mobil için aşağıdan kayan action sheet (`active` class ile) |
| `.floating-action` | Mobilde sağ-alt floating "Yeni Randevu" butonu (admin + customer panel) |
| `.customer-hero-card` | Müşteri dashboard hoş geldin banner'ı — gradient + blur orb decoration |
| `.service-cover` + `.service-price-tag` | Hizmet kartı görselinde köşede fiyat etiketi, hover'da görsel zoom |
| `.campaign-cover` | Kampanya kartı için gradient cover image alanı |

### Legacy alias katmanı

v1'de bırakılan eski class isimleri silinmedi → **CSS'te otomatik alias'landı**. Böylece tüm view'lar bozulmadan yeni design language'i kazandı:

- `.stat-card` + `.tone-success / .tone-warning / .tone-danger / .tone-info` → KPI card stiline alias
- `.table-card` → `.table-rounded` alias
- `.chart-card .chart-head` → standart chart head alias
- `.form-fieldset` → tüm formlarda tutarlı gruplandırma
- `.upload-card` → dropzone tarzı dosya yükleme görseli
- `.slots-grid .slot` → admin randevu saat slotları (frontend `.slot-grid` ile aynı dil)
- `.customer-hero` (`.avatar`, `.meta`, `.stat`) → CRM müşteri detay hero kartı (gradient dark + blur orb)
- `.timeline .timeline-item` → admin paket logları için soft timeline (gradient nokta + bağlantı çizgisi)
- `.template-card` → mesaj şablonu kartı hover
- `.package-card` → paket kataloğu hover-lift
- `.btn-soft`, `.panel-foot`, `.filter-actions` → küçük helper'lar

### JS modülasyonu — admin.js'e eklenen yeni bölümler

| Bölüm | Açıklama |
|-------|----------|
| **13 Scroll reveal** | `.kpi-card`, `.stat-card`, `.panel`, `.table-rounded`, `.chart-card`, `.settings-card` → görünür olunca staggered fade-in |
| **14 Hover-lift global** | Yukarıdaki kartlara `.hover-lift` class'ı otomatik eklenir |
| **15 Page transition** | `.admin-content` sayfa açılışında `.fade-up` |
| **16 Mobile floating action** | Mobilde sağ-alt "Yeni Randevu" butonu; randevu/create sayfasında gizlenir |

### `app.js` — customer panel için micro-interactions

- `.customer-content` fade-up entrance
- `.c-card`, `.c-stat`, `.c-card-lg`, `.service-select-card`, `.loyalty-card` → otomatik hover-lift
- IntersectionObserver staggered reveal
- Form submit → buton spinner state

### Mobil UX iyileştirmeleri

- `767px` altı: admin content padding küçülür, floating action butonu görünür, toast'lar viewport genişliğinde, kpi/stat değer fontları küçülür
- `575px` altı: topbar daralır, breadcrumb küçülür, panel-header wrap olur, fieldset padding düşer
- Customer panelde aynı breakpoint'ler eşlenir
- Tablolar tüm panellerde `overflow-x: auto` (yatay kaydırılabilir)

### Tüm ekran tutarlılığı garantileri

- Tüm "durum" bilgisi artık `status_badge()` helper'ı üzerinden (admin + customer): aynı renk dili, aynı dot indicator
- Tüm liste ekranlarında premium empty state: dairesel ikon + başlık + açıklama + CTA
- Tüm form ekranlarında `fieldset` / `.form-fieldset` ile mantıksal gruplandırma + legend kıvılcımı (`text-transform: uppercase; color: primary`)
- Tüm kartlar `hover-lift` (JS ile veya class ile)
- Tüm `<table>` thead → büyük harf, küçük letter-spacing, soft alt çizgi
- Bootstrap `nav-tabs` / `pagination` da custom alias'larla design system'e bağlandı

> Sonuç: Frontend, admin paneli ve müşteri paneli artık **aynı ürünün üç farklı yüzü** gibi hissediliyor. Bootstrap base'i hâlâ kullanılıyor ama görsel olarak hiçbir yerde "default bootstrap" hissi kalmadı.

---

## One-Page Auth (Drawer) — Giriş / Kayıt / Şifre / Doğrulama

Müşteri kimlik doğrulaması artık ayrı sayfa açmadan **sağdan açılan modern drawer** ile çalışır. Tek bir partial + AJAX iskeleti tüm akışı yönetir; mobilde fullscreen sheet olur.

### Bileşenler

| Dosya | İşi |
|-------|-----|
| `app/Views/frontend/partials/auth-drawer.php` | Drawer iskeleti — sol tarafta güven mesajları (`trust pane`), sağda Giriş / Kayıt / Şifremi Unuttum / E-posta Doğrulama / Hesap panelleri |
| `public/assets/css/frontend.css` → `25a AUTH DRAWER` | Tüm drawer stilleri (sağdan kayan panel, blur backdrop, gradient sol pane, mobil fullscreen) |
| `public/assets/js/auth.js` | Aç/kapat, sekme/pane geçişleri, AJAX form submit, **pending action queue**, toast, password toggle, ESC kapat, `?auth=` URL trigger |
| `app/Controllers/AuthController.php` | AJAX endpoint'leri (`login`, `register`, `forgotPassword`, `resendVerification`, `authStatus`, `logout`, `resetPassword`) |
| `app/Models/PasswordReset.php` | `customer_password_resets` tablosu için CRUD + rate limiting |
| `app/Views/frontend/auth/reset-password.php` | Token bağlantısıyla açılan standalone reset sayfası |
| `database/update_auth_drawer.sql` | `customer_password_resets` tablosu + `password_reset` mail şablonu |

### Akış

1. Navbar'daki "Giriş" / "Üye Ol" → `data-auth-open="login|register"` → drawer açılır.
2. Sayfadaki herhangi bir **Randevu Al** butonu (`[data-book-start]` veya legacy `[data-bs-target="#appointmentModal"]`) `Auth.require(openBookingModal, { requireVerified: true })` çağrısı yapar.
3. Kullanıcı login değilse drawer açılır + üstte **"Devam etmek için giriş yapın"** banner görünür (`auth-pending-banner`).
4. AJAX login/register başarılı:
   - `verified=true` → drawer kapanır, **booking modal otomatik açılır** (pending action queue resume).
   - `verified=false` (kayıt sonrası) → drawer otomatik **Verify pane**'e geçer; tek tıkla **resend verification** butonu (`/?route=resend-verification`).
5. "Şifremi unuttum" → `forgot` pane → e-posta enumeration koruması (her zaman generic success mesajı).
6. Mail'deki link → `?route=reset-password&token=...` → standalone `reset-password.php` sayfası (token expired/invalid kontrolü dahil).
7. ESC tuşu, backdrop'a tık veya `[data-auth-close]` → smooth kapanış (350ms).
8. `customer/?route=login` ve `customer/?route=register` gibi eski yer imleri otomatik `?auth=login|register` ile drawer'a yönlendirilir.

### Public API (JS)

```js
// Anywhere in your code, before performing an action that needs auth:
window.Auth.require(() => doProtectedThing(), {
    requireVerified: true,   // also requires email verification
    label: 'Devam etmek için giriş yapın.'
});

window.Auth.open('login');    // open drawer at specific pane
window.Auth.close();          // close drawer
```

### Tasarım dili

- One-page ile aynı renk paleti (`--primary`, `--secondary`, `--primary-soft`)
- `border-radius: 28px 0 0 28px` rounded edge, `28px` blur backdrop
- Gradient sol pane (trust messages) + glassmorphism trust list cards
- Smooth `cubic-bezier(.22, .61, .36, 1)` slide-in (420ms)
- Mobile (`<768px`): trust pane gizlenir, drawer fullscreen olur, inputs `1rem` font + `1rem` padding (büyük dokunmatik hedef), CTA `1.05rem` padding ile sticky
- Toast bildirimleri: `Admin.toast()` varsa onu, yoksa inline fallback toast'u kullanır

### Güvenlik

- CSRF token tüm AJAX formlarda zorunlu
- `rate_limit_check()` her endpoint için (login, register, forgot, resend)
- Şifre sıfırlama linkleri 60 dk geçerli, tek kullanımlık (`used_at`)
- `forgot-password`: e-posta enumeration'a karşı her durumda aynı generic cevap
- Aynı kullanıcı için 10 dk içinde max 3 reset isteği (rate-limit ek katman)

### DB migration

```bash
mysql -u root appointment_system < database/update_auth_drawer.sql
```

---

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
