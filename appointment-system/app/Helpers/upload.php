<?php

declare(strict_types=1);

function upload_image(array $file, string $subdir = 'general'): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }
    $cfg = config('app');
    if (($file['size'] ?? 0) > ($cfg['upload_max_size'] ?? 5242880)) {
        throw new RuntimeException('Dosya boyutu çok büyük.');
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $cfg['allowed_image_types'] ?? [], true)) {
        throw new RuntimeException('Geçersiz dosya türü.');
    }
    $map = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];
    $ext = $map[$mime] ?? 'bin';
    $dir = PUBLIC_PATH . '/uploads/' . trim($subdir, '/');
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        throw new RuntimeException('Yükleme klasörü oluşturulamadı.');
    }
    $name = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = $dir . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new RuntimeException('Dosya yüklenemedi.');
    }
    return 'uploads/' . trim($subdir, '/') . '/' . $name;
}
