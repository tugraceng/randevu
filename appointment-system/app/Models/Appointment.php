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
        if (!empty($filters['date_from'])) {
            $where[] = 'a.appointment_date >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = 'a.appointment_date <= ?';
            $params[] = $filters['date_to'];
        }
        if (!empty($filters['service_id'])) {
            $where[] = 'a.service_id = ?';
            $params[] = $filters['service_id'];
        }
        if (!empty($filters['staff_id'])) {
            $where[] = 'a.staff_id = ?';
            $params[] = $filters['staff_id'];
        }
        if (!empty($filters['payment_status'])) {
            $where[] = 'a.payment_status = ?';
            $params[] = $filters['payment_status'];
        }
        if (isset($filters['package_only']) && $filters['package_only'] !== '') {
            $where[] = $filters['package_only'] ? 'a.customer_package_id IS NOT NULL' : 'a.customer_package_id IS NULL';
        }
        if (!empty($filters['search'])) {
            $where[] = '(c.first_name LIKE ? OR c.last_name LIKE ? OR c.phone LIKE ? OR c.email LIKE ?)';
            $q = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$q, $q, $q, $q]);
        }
        $whereSql = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;
        $count = $this->db->prepare("SELECT COUNT(*) FROM appointments a JOIN customers c ON c.id = a.customer_id WHERE $whereSql");
        $count->execute($params);
        $total = (int) $count->fetchColumn();

        $sql = "SELECT a.*, CONCAT(c.first_name,' ',c.last_name) AS customer_name, c.phone AS customer_phone,
                s.name AS service_name, st.name AS staff_name,
                cp.remaining_sessions, pk.name AS package_name
                FROM appointments a
                JOIN customers c ON c.id = a.customer_id
                JOIN services s ON s.id = a.service_id
                LEFT JOIN staff st ON st.id = a.staff_id
                LEFT JOIN customer_packages cp ON cp.id = a.customer_package_id
                LEFT JOIN packages pk ON pk.id = cp.package_id
                WHERE $whereSql ORDER BY a.appointment_date DESC, a.start_time DESC LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return ['data' => $stmt->fetchAll(), 'total' => $total, 'page' => $page, 'per_page' => $perPage];
    }

    public function countToday(): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE()");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function countTodayByStatus(string $status): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE() AND status = ?");
        $stmt->execute([$status]);
        return (int) $stmt->fetchColumn();
    }

    public function chartDaily(int $days = 7): array
    {
        $stmt = $this->db->prepare(
            "SELECT appointment_date AS d, COUNT(*) AS cnt FROM appointments
             WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             GROUP BY appointment_date ORDER BY appointment_date"
        );
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }

    public function chartByService(): array
    {
        return $this->db->query(
            "SELECT s.name AS label, COUNT(*) AS cnt FROM appointments a
             JOIN services s ON s.id = a.service_id
             WHERE a.appointment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY s.id ORDER BY cnt DESC LIMIT 8"
        )->fetchAll();
    }

    public function chartByStaff(): array
    {
        return $this->db->query(
            "SELECT COALESCE(st.name,'Atanmamış') AS label, COUNT(*) AS cnt FROM appointments a
             LEFT JOIN staff st ON st.id = a.staff_id
             WHERE a.appointment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY a.staff_id ORDER BY cnt DESC LIMIT 8"
        )->fetchAll();
    }

    public function forCalendar(string $month): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.id, a.appointment_date, a.start_time, a.status,
                    CONCAT(c.first_name,' ',c.last_name) AS title, s.name AS service_name
             FROM appointments a
             JOIN customers c ON c.id = a.customer_id
             JOIN services s ON s.id = a.service_id
             WHERE DATE_FORMAT(a.appointment_date, '%Y-%m') = ?
             ORDER BY a.appointment_date, a.start_time"
        );
        $stmt->execute([$month]);
        return $stmt->fetchAll();
    }

    public function update(int $id, array $d): void
    {
        $stmt = $this->db->prepare(
            'UPDATE appointments SET customer_id=?, service_id=?, staff_id=?, customer_package_id=?,
             appointment_date=?, start_time=?, end_time=?, status=?, payment_required=?, payment_status=?,
             deposit_amount=?, notes=? WHERE id=?'
        );
        $stmt->execute([
            $d['customer_id'], $d['service_id'], $d['staff_id'] ?? null, $d['customer_package_id'] ?? null,
            $d['appointment_date'], $d['start_time'], $d['end_time'], $d['status'],
            $d['payment_required'] ?? 0, $d['payment_status'] ?? 'not_required',
            $d['deposit_amount'] ?? 0, $d['notes'] ?? null, $id,
        ]);
    }

    public function updateNotes(int $id, string $notes): void
    {
        $stmt = $this->db->prepare('UPDATE appointments SET notes = ? WHERE id = ?');
        $stmt->execute([$notes, $id]);
    }
}
