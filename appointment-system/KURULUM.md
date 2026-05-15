# Kurulum Durumu

## Veritabanı — Tamamlandı

- Veritabanı: `appointment_system` (utf8mb4)
- Tablolar ve örnek veriler (`seed.sql`) yüklendi
- **Admin:** `admin@randevu.local` / `password`

## PHPMailer — Manuel kuruldu

Composer, XAMPP PHP sürümü nedeniyle çalışmadı. PHPMailer `vendor/phpmailer/` altına indirildi.

Tam Composer kurulumu için **PHP 8.0+** gerekir (proje de PHP 8+ kullanır).

### XAMPP PHP 8 yükseltme (önerilen)

1. [Apache Friends](https://www.apachefriends.org/) üzerinden XAMPP 8.2+ indirin **veya**
2. Sadece PHP 8 klasörünü indirip `C:\xampp\php` yedekleyip değiştirin
3. `php.ini` içinde `extension=pdo_mysql` ve `extension=curl` açık olsun
4. Sonra proje klasöründe:
   ```bash
   php composer.phar install
   ```

## Hızlı test

- Site: http://localhost/randevu/appointment-system/public/
- Admin: http://localhost/randevu/appointment-system/admin/?route=login
- Müşteri: http://localhost/randevu/appointment-system/customer/?route=login
