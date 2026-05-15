CREATE DATABASE IF NOT EXISTS appointment_system
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE appointment_system;

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin','admin','staff') DEFAULT 'admin',
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(30),
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email_verified_at DATETIME NULL,
    verification_token VARCHAR(255),
    sms_permission TINYINT DEFAULT 0,
    whatsapp_permission TINYINT DEFAULT 0,
    marketing_permission TINYINT DEFAULT 0,
    is_blacklisted TINYINT DEFAULT 0,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(150) NOT NULL UNIQUE,
    setting_value TEXT NULL
);

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    description TEXT,
    duration_minutes INT NOT NULL DEFAULT 30,
    price DECIMAL(10,2) DEFAULT 0.00,
    deposit_price DECIMAL(10,2) DEFAULT 0.00,
    image VARCHAR(255),
    status TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    title VARCHAR(150),
    bio TEXT,
    phone VARCHAR(30),
    email VARCHAR(150),
    photo VARCHAR(255),
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE staff_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    service_id INT NOT NULL,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

CREATE TABLE packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    session_count INT NOT NULL,
    price DECIMAL(10,2) DEFAULT 0.00,
    validity_days INT DEFAULT 180,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id)
);

CREATE TABLE customer_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    package_id INT NOT NULL,
    total_sessions INT NOT NULL,
    used_sessions INT DEFAULT 0,
    remaining_sessions INT NOT NULL,
    purchase_date DATE NOT NULL,
    expiry_date DATE NULL,
    payment_id INT NULL,
    payment_status ENUM('pending','paid','failed','refunded','manual') DEFAULT 'pending',
    status ENUM('active','completed','expired','cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (package_id) REFERENCES packages(id)
);

CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    service_id INT NOT NULL,
    staff_id INT NULL,
    customer_package_id INT NULL,
    appointment_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('pending','approved','cancelled','completed','no_show') DEFAULT 'pending',
    source ENUM('website','admin') DEFAULT 'website',
    payment_required TINYINT DEFAULT 0,
    payment_status ENUM('not_required','pending','paid','failed','refunded') DEFAULT 'not_required',
    deposit_amount DECIMAL(10,2) DEFAULT 0.00,
    notes TEXT,
    created_by_admin_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (staff_id) REFERENCES staff(id),
    FOREIGN KEY (customer_package_id) REFERENCES customer_packages(id),
    FOREIGN KEY (created_by_admin_id) REFERENCES admins(id)
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    appointment_id INT NULL,
    customer_package_id INT NULL,
    provider ENUM('iyzico','paytr','param','manual') DEFAULT 'iyzico',
    payment_type ENUM('appointment','package','deposit','manual') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'TRY',
    status ENUM('pending','paid','failed','cancelled','refunded') DEFAULT 'pending',
    provider_payment_id VARCHAR(255),
    provider_conversation_id VARCHAR(255),
    callback_payload LONGTEXT,
    paid_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (appointment_id) REFERENCES appointments(id),
    FOREIGN KEY (customer_package_id) REFERENCES customer_packages(id)
);

CREATE TABLE message_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    channel ENUM('email','sms','whatsapp') NOT NULL,
    template_key VARCHAR(100) NOT NULL,
    title VARCHAR(150) NOT NULL,
    subject VARCHAR(255) NULL,
    body TEXT NOT NULL,
    provider_template_name VARCHAR(150) NULL,
    language_code VARCHAR(20) DEFAULT 'tr',
    status TINYINT DEFAULT 1,
    UNIQUE(channel, template_key)
);

CREATE TABLE message_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NULL,
    appointment_id INT NULL,
    channel ENUM('email','sms','whatsapp') NOT NULL,
    recipient VARCHAR(150) NOT NULL,
    template_key VARCHAR(100),
    message TEXT,
    provider VARCHAR(50),
    provider_message_id VARCHAR(255),
    status ENUM('pending','sent','failed','delivered','read') DEFAULT 'pending',
    response_payload LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (appointment_id) REFERENCES appointments(id)
);

CREATE TABLE package_session_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_package_id INT NOT NULL,
    appointment_id INT NULL,
    action ENUM('used','restored','manual_add','manual_remove') NOT NULL,
    session_count INT NOT NULL DEFAULT 1,
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_package_id) REFERENCES customer_packages(id),
    FOREIGN KEY (appointment_id) REFERENCES appointments(id)
);

CREATE TABLE page_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(255),
    subtitle VARCHAR(255),
    content TEXT,
    image VARCHAR(255),
    status TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0
);

CREATE TABLE campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    start_date DATE,
    end_date DATE,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE campaign_recipients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT NOT NULL,
    customer_id INT NOT NULL,
    channel ENUM('sms','whatsapp','email') NOT NULL,
    sent_status ENUM('pending','sent','failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

CREATE TABLE working_hours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NULL,
    day_of_week TINYINT NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_closed TINYINT DEFAULT 0,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE
);

CREATE TABLE holidays (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150),
    holiday_date DATE NOT NULL,
    description TEXT,
    status TINYINT DEFAULT 1
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(150),
    comment TEXT,
    rating TINYINT DEFAULT 5,
    status TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0
);

CREATE TABLE gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150),
    image VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    status TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0
);

CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(255) NOT NULL,
    answer TEXT NOT NULL,
    status TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0
);

CREATE TABLE system_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('admin','customer','system') DEFAULT 'system',
    user_id INT NULL,
    action VARCHAR(150) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
