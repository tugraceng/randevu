-- Randevu sistemi: frontend + admin geliştirmeleri
-- Mevcut veritabanına uygulayın: mysql -u root appointment_system < database/update_front_admin_improvements.sql

USE appointment_system;

-- Müşteri notları
CREATE TABLE IF NOT EXISTS customer_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    admin_id INT NULL,
    note TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
);

-- Aşağıdaki ALTER satırları zaten varsa hata verebilir; yok sayın veya tek tek çalıştırın.
-- ALTER TABLE staff ADD COLUMN specialty VARCHAR(255) NULL AFTER title;
-- ALTER TABLE staff ADD COLUMN sort_order INT DEFAULT 0 AFTER status;
-- ALTER TABLE appointments ADD COLUMN admin_note TEXT NULL AFTER notes;
-- ALTER TABLE payments ADD COLUMN admin_note TEXT NULL AFTER callback_payload;

-- Varsayılan tema / SEO ayarları (settings tablosuna)
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
('theme_primary', '#4f46e5'),
('theme_secondary', '#0ea5e9'),
('seo_title', 'RandevuTakip - Online Randevu'),
('seo_description', 'Modern randevu ve paket yönetim sistemi'),
('whatsapp_float', '1'),
('whatsapp_number', ''),
('map_embed', ''),
('map_latitude', ''),
('map_longitude', ''),
('appointment_min_hours', '2'),
('appointment_max_days', '60'),
('stat_happy_clients', '15k+'),
('stat_support', '7/24'),
('stat_experts', '45+'),
('stat_awards', '12');
