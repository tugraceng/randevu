<?php

declare(strict_types=1);

namespace App\Models;

class Campaign extends BaseModel
{
    public function allActive(): array
    {
        return $this->db->query(
            "SELECT * FROM campaigns WHERE status = 1 AND (start_date IS NULL OR start_date <= CURDATE()) AND (end_date IS NULL OR end_date >= CURDATE()) ORDER BY id DESC"
        )->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM campaigns ORDER BY id DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM campaigns WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}
