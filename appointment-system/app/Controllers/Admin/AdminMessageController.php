<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\MessageTemplate;
use App\Services\NetgsmSmsService;

class AdminMessageController
{
    public function index(): void
    {
        require_admin();
        view('admin/messages/index', [
            'title' => 'Bildirim ve Şablonlar',
            'templates' => (new MessageTemplate())->all(),
            'channel' => $_GET['channel'] ?? 'sms',
        ]);
    }

    public function save(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        (new MessageTemplate())->update((int) $_POST['id'], [
            'title' => $_POST['title'],
            'subject' => $_POST['subject'] ?? null,
            'body' => $_POST['body'],
            'provider_template_name' => $_POST['provider_template_name'] ?? null,
            'language_code' => $_POST['language_code'] ?? 'tr',
            'status' => (int) ($_POST['status'] ?? 1),
        ]);
        flash('success', 'Şablon kaydedildi.');
        redirect(admin_url('?route=messages'));
    }
}
