<?php

declare(strict_types=1);

namespace App\Models;

class CustomerPackage extends BaseModel
{
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT cp.*, p.name AS package_name, p.service_id, s.name AS service_name,
                    CONCAT(c.first_name," ",c.last_name) AS customer_name, c.email
             FROM customer_packages cp
             JOIN packages p ON p.id = cp.package_id
             JOIN services s ON s.id = p.service_id
             JOIN customers c ON c.id = cp.customer_id
             WHERE cp.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function forCustomer(int $customerId, bool $activeOnly = true): array
    {
        $sql = 'SELECT cp.*, p.name AS package_name, s.name AS service_name
                FROM customer_packages cp
                JOIN packages p ON p.id = cp.package_id
                JOIN services s ON s.id = p.service_id
                WHERE cp.customer_id = ?';
        if ($activeOnly) {
            $sql .= " AND cp.status = 'active' AND cp.remaining_sessions > 0 AND (cp.expiry_date IS NULL OR cp.expiry_date >= CURDATE())";
        }
        $sql .= ' ORDER BY cp.id DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    }

    public function create(array $d): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO customer_packages (customer_id, package_id, total_sessions, used_sessions, remaining_sessions, purchase_date, expiry_date, payment_id, payment_status, status)
             VALUES (?,?,?,?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $d['customer_id'], $d['package_id'], $d['total_sessions'], $d['used_sessions'] ?? 0,
            $d['remaining_sessions'], $d['purchase_date'], $d['expiry_date'] ?? null,
            $d['payment_id'] ?? null, $d['payment_status'] ?? 'pending', $d['status'] ?? 'active',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateSessions(int $id, int $used, int $remaining, string $status): void
    {
        $stmt = $this->db->prepare(
            'UPDATE customer_packages SET used_sessions=?, remaining_sessions=?, status=? WHERE id=?'
        );
        $stmt->execute([$used, $remaining, $status, $id]);
    }

    public function activate(int $id, ?int $paymentId = null): void
    {
        $stmt = $this->db->prepare(
            "UPDATE customer_packages SET payment_status='paid', status='active', payment_id=? WHERE id=?"
        );
        $stmt->execute([$paymentId, $id]);
    }

    public function countActive(): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM customer_packages WHERE status='active' AND remaining_sessions > 0"
        )->fetchColumn();
    }

    public function criticalCount(): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM customer_packages WHERE status='active' AND remaining_sessions <= 1 AND remaining_sessions > 0"
        )->fetchColumn();
    }

    public function withCustomers(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $total = (int) $this->db->query('SELECT COUNT(DISTINCT customer_id) FROM customer_packages')->fetchColumn();
        $stmt = $this->db->query(
            "SELECT c.id, c.first_name, c.last_name, c.email,
                    (SELECT p.name FROM customer_packages cp2 JOIN packages p ON p.id=cp2.package_id WHERE cp2.customer_id=c.id AND cp2.status='active' ORDER BY cp2.id DESC LIMIT 1) AS active_package,
                    (SELECT SUM(remaining_sessions) FROM customer_packages cp3 WHERE cp3.customer_id=c.id AND cp3.status='active') AS remaining_sessions,
                    (SELECT payment_status FROM customer_packages cp4 WHERE cp4.customer_id=c.id ORDER BY cp4.id DESC LIMIT 1) AS last_payment_status,
                    c.created_at
             FROM customers c ORDER BY c.id DESC LIMIT $perPage OFFSET $offset"
        );
        return ['data' => $stmt->fetchAll(), 'total' => $total];
    }
}
