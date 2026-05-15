<?php

declare(strict_types=1);

namespace App\Models;

class MessageTemplate extends BaseModel
{
    public function findByKey(string $channel, string $key): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM message_templates WHERE channel = ? AND template_key = ? AND status = 1 LIMIT 1'
        );
        $stmt->execute([$channel, $key]);
        return $stmt->fetch() ?: null;
    }

    public function all(?string $channel = null): array
    {
        if ($channel) {
            $stmt = $this->db->prepare('SELECT * FROM message_templates WHERE channel = ? ORDER BY template_key');
            $stmt->execute([$channel]);
            return $stmt->fetchAll();
        }
        return $this->db->query('SELECT * FROM message_templates ORDER BY channel, template_key')->fetchAll();
    }

    public function update(int $id, array $d): void
    {
        $stmt = $this->db->prepare(
            'UPDATE message_templates SET title=?, subject=?, body=?, provider_template_name=?, language_code=?, status=? WHERE id=?'
        );
        $stmt->execute([
            $d['title'], $d['subject'] ?? null, $d['body'],
            $d['provider_template_name'] ?? null, $d['language_code'] ?? 'tr', $d['status'] ?? 1, $id,
        ]);
    }
}
