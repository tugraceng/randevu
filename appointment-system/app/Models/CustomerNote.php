<?php

declare(strict_types=1);

namespace App\Models;

class CustomerNote extends BaseModel
{
    public function forCustomer(int $customerId): array
    {
        $stmt = $this->db->prepare(
            'SELECT n.*, a.name AS admin_name FROM customer_notes n
             LEFT JOIN admins a ON a.id = n.admin_id
             WHERE n.customer_id = ? ORDER BY n.id DESC'
        );
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    }

    public function create(int $customerId, string $note, ?int $adminId = null): int
    {
        $stmt = $this->db->prepare('INSERT INTO customer_notes (customer_id, admin_id, note) VALUES (?,?,?)');
        $stmt->execute([$customerId, $adminId, $note]);
        return (int) $this->db->lastInsertId();
    }
}
