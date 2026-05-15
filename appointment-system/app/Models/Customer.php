<?php

declare(strict_types=1);

namespace App\Models;

class Customer extends BaseModel
{
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM customers WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM customers WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function findByToken(string $token): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM customers WHERE verification_token = ? LIMIT 1');
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO customers (first_name, last_name, phone, email, password, verification_token, sms_permission, whatsapp_permission, marketing_permission)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['phone'] ?? null,
            $data['email'],
            $data['password'],
            $data['verification_token'],
            $data['sms_permission'] ?? 0,
            $data['whatsapp_permission'] ?? 0,
            $data['marketing_permission'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function verifyEmail(int $id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE customers SET email_verified_at = NOW(), verification_token = NULL WHERE id = ?'
        );
        $stmt->execute([$id]);
    }

    public function updateProfile(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE customers SET first_name=?, last_name=?, phone=?, sms_permission=?, whatsapp_permission=?, marketing_permission=? WHERE id=?'
        );
        $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['phone'],
            $data['sms_permission'],
            $data['whatsapp_permission'],
            $data['marketing_permission'],
            $id,
        ]);
    }

    public function updatePassword(int $id, string $hash): void
    {
        $stmt = $this->db->prepare('UPDATE customers SET password = ? WHERE id = ?');
        $stmt->execute([$hash, $id]);
    }

    public function paginate(int $page = 1, int $perPage = 15, ?string $search = null): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = 'WHERE 1=1';
        if ($search) {
            $where .= ' AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)';
            $q = '%' . $search . '%';
            $params = [$q, $q, $q, $q];
        }
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM customers $where");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT * FROM customers $where ORDER BY id DESC LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return ['data' => $stmt->fetchAll(), 'total' => $total, 'page' => $page, 'per_page' => $perPage];
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM customers')->fetchColumn();
    }

    public function countNewThisMonth(): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM customers WHERE MONTH(created_at)=MONTH(CURRENT_DATE()) AND YEAR(created_at)=YEAR(CURRENT_DATE())"
        )->fetchColumn();
    }
}
