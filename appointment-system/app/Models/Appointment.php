<?php

declare(strict_types=1);

namespace App\Models;

class Appointment extends BaseModel
{
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, c.first_name, c.last_name, c.email, c.phone, s.name AS service_name, st.name AS staff_name
             FROM appointments a
             LEFT JOIN customers c ON c.id = a.customer_id
             LEFT JOIN services s ON s.id = a.service_id
             LEFT JOIN staff st ON st.id = a.staff_id
             WHERE a.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function forCustomer(int $customerId): array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, s.name AS service_name, st.name AS staff_name
             FROM appointments a
             LEFT JOIN services s ON s.id = a.service_id
             LEFT JOIN staff st ON st.id = a.staff_id
             WHERE a.customer_id = ? ORDER BY a.appointment_date DESC, a.start_time DESC'
        );
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    }

    public function upcoming(int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.*, CONCAT(c.first_name,' ',c.last_name) AS customer_name, s.name AS service_name
             FROM appointments a
             JOIN customers c ON c.id = a.customer_id
             JOIN services s ON s.id = a.service_id
             WHERE a.appointment_date >= CURDATE() AND a.status IN ('pending','approved')
             ORDER BY a.appointment_date, a.start_time LIMIT ?"
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(array $d): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO appointments (customer_id, service_id, staff_id, customer_package_id, appointment_date, start_time, end_time, status, source, payment_required, payment_status, deposit_amount, notes, created_by_admin_id)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $d['customer_id'], $d['service_id'], $d['staff_id'] ?? null, $d['customer_package_id'] ?? null,
            $d['appointment_date'], $d['start_time'], $d['end_time'], $d['status'] ?? 'pending',
            $d['source'] ?? 'website', $d['payment_required'] ?? 0, $d['payment_status'] ?? 'not_required',
            $d['deposit_amount'] ?? 0, $d['notes'] ?? null, $d['created_by_admin_id'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE appointments SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    public function updatePaymentStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE appointments SET payment_status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    public function hasConflict(?int $staffId, string $date, string $start, string $end, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM appointments
                WHERE appointment_date = ? AND status NOT IN ('cancelled')
                AND ((start_time < ? AND end_time > ?) OR (start_time >= ? AND start_time < ?))";
        $params = [$date, $end, $start, $start, $end];
        if ($staffId) {
            $sql .= ' AND staff_id = ?';
            $params[] = $staffId;
        }
        if ($excludeId) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM appointments')->fetchColumn();
    }

    public function countByStatus(string $status): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM appointments WHERE status = ?');
        $stmt->execute([$status]);
        return (int) $stmt->fetchColumn();
    }

    public function filter(array $filters, int $page = 1, int $perPage = 20): array
    {
        $where = ['1=1'];
        $params = [];
        if (!empty($filters['status'])) {
            $where[] = 'a.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['date'])) {
            $where[] = 'a.appointment_date = ?';
            $params[] = $filters['date'];
        }
        $whereSql = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;
        $count = $this->db->prepare("SELECT COUNT(*) FROM appointments a WHERE $whereSql");
        $count->execute($params);
        $total = (int) $count->fetchColumn();

        $sql = "SELECT a.*, CONCAT(c.first_name,' ',c.last_name) AS customer_name, s.name AS service_name, st.name AS staff_name
                FROM appointments a
                JOIN customers c ON c.id = a.customer_id
                JOIN services s ON s.id = a.service_id
                LEFT JOIN staff st ON st.id = a.staff_id
                WHERE $whereSql ORDER BY a.appointment_date DESC LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return ['data' => $stmt->fetchAll(), 'total' => $total];
    }
}
