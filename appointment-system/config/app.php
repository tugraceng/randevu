<?php

return [
    'name' => 'RandevuTakip',
    'url' => getenv('APP_URL') ?: 'http://localhost/randevu/appointment-system/public',
    'admin_url' => getenv('ADMIN_URL') ?: 'http://localhost/randevu/appointment-system/admin',
    'customer_url' => getenv('CUSTOMER_URL') ?: 'http://localhost/randevu/appointment-system/customer',
    'timezone' => 'Europe/Istanbul',
    'session_name' => 'RANDEVU_SESSION',
    'admin_session_key' => 'admin_user',
    'customer_session_key' => 'customer_user',
    'upload_max_size' => 5 * 1024 * 1024,
    'allowed_image_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
    'rate_limit_attempts' => 10,
    'rate_limit_window' => 300,
];
