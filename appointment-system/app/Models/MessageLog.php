<?php

declare(strict_types=1);

namespace App\Models;

class MessageLog extends BaseModel
{
    public function create(array $d): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO message_logs (customer_id, appointment_id, channel, recipient, template_key, message, provider, provider_message_id, status, response_payload)
             VALUES (?,?,?,?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $d['customer_id'] ?? null, $d['appointment_id'] ?? null, $d['channel'],
            $d['recipient'], $d['template_key'] ?? null, $d['message'] ?? null,
            $d['provider'] ?? null, $d['provider_message_id'] ?? null,
            $d['status'] ?? 'pending', $d['response_payload'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status, ?string $payload = null, ?string $providerId = null): void
    {
        $stmt = $this->db->prepare(
            'UPDATE message_logs SET status=?, response_payload=?, provider_message_id=COALESCE(?, provider_message_id) WHERE id=?'
        );
        $stmt->execute([$status, $payload, $providerId, $id]);
    }

    public function paginate(int $page = 1, int $perPage = 20, ?string $channel = null): array
    {
        $where = '1=1';
        $params = [];
        if ($channel) {
            $where .= ' AND channel = ?';
            $params[] = $channel;
        }
        $offset = ($page - 1) * $perPage;
        $count = $this->db->prepare("SELECT COUNT(*) FROM message_logs WHERE $where");
        $count->execute($params);
        $total = (int) $count->fetchColumn();
        $stmt = $this->db->prepare("SELECT * FROM message_logs WHERE $where ORDER BY id DESC LIMIT $perPage OFFSET $offset");
        $stmt->execute($params);
        return ['data' => $stmt->fetchAll(), 'total' => $total];
    }

    public function countByChannel(string $channel, ?string $since = null): int
    {
        $sql = "SELECT COUNT(*) FROM message_logs WHERE channel = ? AND status IN ('sent','delivered')";
        $params = [$channel];
        if ($since) {
            $sql .= ' AND created_at >= ?';
            $params[] = $since;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function countThisMonth(): array
    {
        $rows = $this->db->query(
            "SELECT channel, COUNT(*) AS cnt FROM message_logs
             WHERE MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE())
             AND status IN ('sent','delivered') GROUP BY channel"
        )->fetchAll();
        $out = ['email' => 0, 'sms' => 0, 'whatsapp' => 0];
        foreach ($rows as $r) {
            $out[$r['channel']] = (int) $r['cnt'];
        }
        return $out;
    }
}
