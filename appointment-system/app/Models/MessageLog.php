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
}
