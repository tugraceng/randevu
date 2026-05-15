<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\MessageTemplate;
use App\Models\Setting;
use App\Services\NetgsmSmsService;

class AdminSettingsController
{
    public function index(): void
    {
        require_admin();
        view('admin/settings/index', [
            'title' => 'Site ve Entegrasyon Ayarları',
            'settings' => (new Setting())->all(),
            'templates' => (new MessageTemplate())->all('sms'),
        ]);
    }

    public function save(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $keys = array_keys($_POST['settings'] ?? []);
        $pairs = [];
        foreach ($_POST['settings'] ?? [] as $key => $value) {
            if (is_string($key)) {
                $pairs[$key] = $value;
            }
        }
        (new Setting())->setMany($pairs);

        if (isset($_POST['test_sms_phone'])) {
            $phone = $_POST['test_sms_phone'];
            (new NetgsmSmsService())->send($phone, 'RandevuTakip test SMS mesajı.', null, null, 'test');
        }

        flash('success', 'Ayarlar kaydedildi.');
        redirect(admin_url('?route=settings'));
    }
}
