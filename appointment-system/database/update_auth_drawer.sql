-- =====================================================================
-- Auth Drawer (one-page auth experience) migration
-- =====================================================================

CREATE TABLE IF NOT EXISTS customer_password_resets (
    id           INT(11)      NOT NULL AUTO_INCREMENT,
    customer_id  INT(11)      NOT NULL,
    token        VARCHAR(128) NOT NULL,
    expires_at   DATETIME     NOT NULL,
    used_at      DATETIME     NULL,
    ip_address   VARCHAR(64)  NULL,
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_cpr_token (token),
    KEY idx_cpr_customer (customer_id),
    KEY idx_cpr_expires  (expires_at),
    CONSTRAINT fk_cpr_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Şablon eklemeleri (varsa atlanır)
INSERT IGNORE INTO message_templates (channel, template_key, title, subject, body, status)
VALUES
('email', 'password_reset', 'Şifre Sıfırlama',
 'Şifre sıfırlama talebiniz',
 'Merhaba {name},\n\nŞifre sıfırlama talebinde bulundunuz. Aşağıdaki bağlantıya tıklayarak yeni şifrenizi belirleyebilirsiniz:\n\n{reset_link}\n\nBu bağlantı 60 dakika geçerlidir. Eğer bu talebi siz yapmadıysanız bu e-postayı yok sayabilirsiniz.\n\n{business_name}', 1);
