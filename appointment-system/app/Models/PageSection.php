<?php

declare(strict_types=1);

namespace App\Models;

class PageSection extends BaseModel
{
    public function allActive(): array
    {
        $rows = $this->db->query('SELECT * FROM page_sections WHERE status = 1 ORDER BY sort_order')->fetchAll();
        $indexed = [];
        foreach ($rows as $row) {
            $indexed[$row['section_key']] = $row;
        }
        return $indexed;
    }

    public function findByKey(string $key): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM page_sections WHERE section_key = ?');
        $stmt->execute([$key]);
        return $stmt->fetch() ?: null;
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM page_sections ORDER BY sort_order')->fetchAll();
    }

    public function upsert(string $key, array $d): void
    {
        $existing = $this->findByKey($key);
        if ($existing) {
            $stmt = $this->db->prepare(
                'UPDATE page_sections SET title=?, subtitle=?, content=?, image=?, status=?, sort_order=? WHERE section_key=?'
            );
            $stmt->execute([
                $d['title'] ?? null, $d['subtitle'] ?? null, $d['content'] ?? null,
                $d['image'] ?? $existing['image'], $d['status'] ?? 1, $d['sort_order'] ?? 0, $key,
            ]);
        } else {
            $stmt = $this->db->prepare(
                'INSERT INTO page_sections (section_key, title, subtitle, content, image, status, sort_order) VALUES (?,?,?,?,?,?,?)'
            );
            $stmt->execute([
                $key, $d['title'] ?? null, $d['subtitle'] ?? null, $d['content'] ?? null,
                $d['image'] ?? null, $d['status'] ?? 1, $d['sort_order'] ?? 0,
            ]);
        }
    }
}
