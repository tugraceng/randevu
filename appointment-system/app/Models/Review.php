<?php

declare(strict_types=1);

namespace App\Models;

class Review extends BaseModel
{
    public function allActive(): array
    {
        return $this->db->query('SELECT * FROM reviews WHERE status = 1 ORDER BY sort_order, id')->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM reviews ORDER BY sort_order, id')->fetchAll();
    }

    public function save(array $d, ?int $id = null): int
    {
        if ($id) {
            $stmt = $this->db->prepare('UPDATE reviews SET customer_name=?, comment=?, rating=?, status=?, sort_order=? WHERE id=?');
            $stmt->execute([$d['customer_name'], $d['comment'], $d['rating'] ?? 5, $d['status'] ?? 1, $d['sort_order'] ?? 0, $id]);
            return $id;
        }
        $stmt = $this->db->prepare('INSERT INTO reviews (customer_name, comment, rating, status, sort_order) VALUES (?,?,?,?,?)');
        $stmt->execute([$d['customer_name'], $d['comment'], $d['rating'] ?? 5, $d['status'] ?? 1, $d['sort_order'] ?? 0]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM reviews WHERE id = ?');
        $stmt->execute([$id]);
    }
}
