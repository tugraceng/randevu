<?php

return [
    'host' => getenv('MAIL_HOST') ?: 'smtp.gmail.com',
    'port' => (int) (getenv('MAIL_PORT') ?: 587),
    'username' => getenv('MAIL_USER') ?: '',
    'password' => getenv('MAIL_PASS') ?: '',
    'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
    'from_email' => getenv('MAIL_FROM') ?: 'noreply@example.com',
    'from_name' => getenv('MAIL_FROM_NAME') ?: 'RandevuTakip',
];
