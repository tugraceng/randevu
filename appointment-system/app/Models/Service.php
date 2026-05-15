<?php

declare(strict_types=1);

namespace App\Models;

class Service extends BaseModel
{
    public function allActive(): array
    {
        return $this->db->query('SELECT * FROM services WHERE status = 1 ORDER BY sort_order, name')->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM services ORDER BY sort_order, name')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM services WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $d): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO services (name, slug, description, duration_minutes, price, deposit_price, image, status, sort_order)
             VALUES (?,?,?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $d['name'], $d['slug'], $d['description'] ?? null, $d['duration_minutes'],
            $d['price'], $d['deposit_price'] ?? 0, $d['image'] ?? null, $d['status'] ?? 1, $d['sort_order'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): void
    {
        $stmt = $this->db->prepare(
            'UPDATE services SET name=?, slug=?, description=?, duration_minutes=?, price=?, deposit_price=?, image=?, status=?, sort_order=? WHERE id=?'
        );
        $stmt->execute([
            $d['name'], $d['slug'], $d['description'] ?? null, $d['duration_minutes'],
            $d['price'], $d['deposit_price'] ?? 0, $d['image'] ?? null, $d['status'] ?? 1, $d['sort_order'] ?? 0, $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM services WHERE id = ?');
        $stmt->execute([$id]);
    }
}
