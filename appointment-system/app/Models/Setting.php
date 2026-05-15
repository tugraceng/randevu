<?php

declare(strict_types=1);

namespace App\Models;

class Setting extends BaseModel
{
    private static ?array $cache = null;

    public function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }
        $rows = $this->db->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
        self::$cache = [];
        foreach ($rows as $row) {
            self::$cache[$row['setting_key']] = $row['setting_value'];
        }
        return self::$cache;
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $all = $this->all();
        return $all[$key] ?? $default;
    }

    public function set(string $key, ?string $value): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
        );
        $stmt->execute([$key, $value]);
        self::$cache = null;
    }

    public function setMany(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            $this->set($key, $value);
        }
    }

    public static function clearCache(): void
    {
        self::$cache = null;
    }
}
