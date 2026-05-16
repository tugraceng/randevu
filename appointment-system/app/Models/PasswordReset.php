<?php

declare(strict_types=1);

namespace App\Models;

class PasswordReset extends BaseModel
{
    public function create(int $customerId, string $token, int $minutes = 60, ?string $ip = null): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO customer_password_resets (customer_id, token, expires_at, ip_address)
             VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? MINUTE), ?)'
        );
        $stmt->execute([$customerId, $token, $minutes, $ip]);
    }

    public function findValid(string $token): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM customer_password_resets
             WHERE token = ? AND used_at IS NULL AND expires_at > NOW()
             LIMIT 1'
        );
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    public function markUsed(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE customer_password_resets SET used_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function deleteExpired(): void
    {
        $this->db->exec('DELETE FROM customer_password_resets WHERE expires_at < NOW() AND used_at IS NULL');
    }

    public function countRecentRequests(int $customerId, int $minutes = 10): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM customer_password_resets
             WHERE customer_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)'
        );
        $stmt->execute([$customerId, $minutes]);
        return (int) $stmt->fetchColumn();
    }
}
