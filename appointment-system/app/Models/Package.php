<?php

declare(strict_types=1);

namespace App\Models;

class Package extends BaseModel
{
    public function allActive(): array
    {
        return $this->db->query(
            'SELECT p.*, s.name AS service_name FROM packages p JOIN services s ON s.id = p.service_id WHERE p.status = 1 ORDER BY p.price'
        )->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query(
            'SELECT p.*, s.name AS service_name FROM packages p JOIN services s ON s.id = p.service_id ORDER BY p.id DESC'
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, s.name AS service_name FROM packages p JOIN services s ON s.id = p.service_id WHERE p.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $d): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO packages (service_id, name, description, session_count, price, validity_days, status) VALUES (?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $d['service_id'], $d['name'], $d['description'] ?? null, $d['session_count'],
            $d['price'], $d['validity_days'] ?? 180, $d['status'] ?? 1,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): void
    {
        $stmt = $this->db->prepare(
            'UPDATE packages SET service_id=?, name=?, description=?, session_count=?, price=?, validity_days=?, status=? WHERE id=?'
        );
        $stmt->execute([
            $d['service_id'], $d['name'], $d['description'] ?? null, $d['session_count'],
            $d['price'], $d['validity_days'], $d['status'], $id,
        ]);
    }
}
