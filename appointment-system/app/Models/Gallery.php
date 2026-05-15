<?php

declare(strict_types=1);

namespace App\Models;

class Gallery extends BaseModel
{
    public function allActive(): array
    {
        return $this->db->query('SELECT * FROM gallery WHERE status = 1 ORDER BY sort_order, id')->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM gallery ORDER BY sort_order, id')->fetchAll();
    }

    public function save(array $d, ?int $id = null): int
    {
        if ($id) {
            $stmt = $this->db->prepare('UPDATE gallery SET title=?, image=?, category=?, status=?, sort_order=? WHERE id=?');
            $stmt->execute([$d['title'] ?? '', $d['image'], $d['category'] ?? '', $d['status'] ?? 1, $d['sort_order'] ?? 0, $id]);
            return $id;
        }
        $stmt = $this->db->prepare('INSERT INTO gallery (title, image, category, status, sort_order) VALUES (?,?,?,?,?)');
        $stmt->execute([$d['title'] ?? '', $d['image'], $d['category'] ?? '', $d['status'] ?? 1, $d['sort_order'] ?? 0]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM gallery WHERE id = ?');
        $stmt->execute([$id]);
    }
}
