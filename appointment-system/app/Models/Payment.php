<?php

declare(strict_types=1);

namespace App\Models;

class Payment extends BaseModel
{
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $d): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO payments (customer_id, appointment_id, customer_package_id, provider, payment_type, amount, currency, status, provider_payment_id, provider_conversation_id)
             VALUES (?,?,?,?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $d['customer_id'], $d['appointment_id'] ?? null, $d['customer_package_id'] ?? null,
            $d['provider'], $d['payment_type'], $d['amount'], $d['currency'] ?? 'TRY',
            $d['status'] ?? 'pending', $d['provider_payment_id'] ?? null, $d['provider_conversation_id'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function markPaid(int $id, ?string $providerPaymentId = null, ?string $payload = null): void
    {
        $stmt = $this->db->prepare(
            'UPDATE payments SET status=?, provider_payment_id=?, callback_payload=?, paid_at=NOW() WHERE id=?'
        );
        $stmt->execute(['paid', $providerPaymentId, $payload, $id]);
    }

    public function markFailed(int $id, ?string $payload = null): void
    {
        $stmt = $this->db->prepare('UPDATE payments SET status=?, callback_payload=? WHERE id=?');
        $stmt->execute(['failed', $payload, $id]);
    }

    public function forCustomer(int $customerId): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, a.appointment_date, s.name AS service_name, pk.name AS package_name
             FROM payments p
             LEFT JOIN appointments a ON a.id = p.appointment_id
             LEFT JOIN services s ON s.id = a.service_id
             LEFT JOIN customer_packages cp ON cp.id = p.customer_package_id
             LEFT JOIN packages pk ON pk.id = cp.package_id
             WHERE p.customer_id = ? ORDER BY p.id DESC'
        );
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    }

    public function recent(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, CONCAT(c.first_name," ",c.last_name) AS customer_name
             FROM payments p JOIN customers c ON c.id = p.customer_id
             ORDER BY p.id DESC LIMIT ?'
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function monthlyRevenue(): float
    {
        return (float) $this->db->query(
            "SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='paid' AND MONTH(paid_at)=MONTH(CURRENT_DATE()) AND YEAR(paid_at)=YEAR(CURRENT_DATE())"
        )->fetchColumn();
    }
}
