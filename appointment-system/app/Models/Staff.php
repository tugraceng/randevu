<?php

declare(strict_types=1);

namespace App\Models;

class Staff extends BaseModel
{
    public function allActive(): array
    {
        return $this->db->query('SELECT * FROM staff WHERE status = 1 ORDER BY name')->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM staff ORDER BY name')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM staff WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function forService(int $serviceId): array
    {
        $stmt = $this->db->prepare(
            'SELECT s.* FROM staff s
             INNER JOIN staff_services ss ON ss.staff_id = s.id
             WHERE ss.service_id = ? AND s.status = 1 ORDER BY s.name'
        );
        $stmt->execute([$serviceId]);
        return $stmt->fetchAll();
    }

    public function syncServices(int $staffId, array $serviceIds): void
    {
        $this->db->prepare('DELETE FROM staff_services WHERE staff_id = ?')->execute([$staffId]);
        $stmt = $this->db->prepare('INSERT INTO staff_services (staff_id, service_id) VALUES (?, ?)');
        foreach ($serviceIds as $sid) {
            $stmt->execute([$staffId, (int) $sid]);
        }
    }

    public function serviceIds(int $staffId): array
    {
        $stmt = $this->db->prepare('SELECT service_id FROM staff_services WHERE staff_id = ?');
        $stmt->execute([$staffId]);
        return array_column($stmt->fetchAll(), 'service_id');
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO staff (name, title, bio, phone, email, photo, status) VALUES (?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $data['name'], $data['title'] ?? null, $data['bio'] ?? null,
            $data['phone'] ?? null, $data['email'] ?? null, $data['photo'] ?? null,
            $data['status'] ?? 1,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE staff SET name=?, title=?, bio=?, phone=?, email=?, photo=?, status=? WHERE id=?'
        );
        $stmt->execute([
            $data['name'], $data['title'] ?? null, $data['bio'] ?? null,
            $data['phone'] ?? null, $data['email'] ?? null, $data['photo'] ?? null,
            $data['status'] ?? 1, $id,
        ]);
    }
}
