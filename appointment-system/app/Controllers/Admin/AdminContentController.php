<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\PageSection;

class AdminContentController
{
    public function index(): void
    {
        require_admin();
        view('admin/content/index', [
            'title' => 'Sayfa İçerikleri',
            'sections' => (new PageSection())->all(),
        ]);
    }

    public function save(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        foreach ($_POST['sections'] ?? [] as $key => $data) {
            (new PageSection())->upsert($key, [
                'title' => $data['title'] ?? null,
                'subtitle' => $data['subtitle'] ?? null,
                'content' => $data['content'] ?? null,
                'status' => (int) ($data['status'] ?? 1),
                'sort_order' => (int) ($data['sort_order'] ?? 0),
            ]);
        }
        flash('success', 'İçerikler güncellendi.');
        redirect(admin_url('?route=content'));
    }
}
