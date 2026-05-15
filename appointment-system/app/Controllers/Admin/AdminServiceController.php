<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Service;

class AdminServiceController
{
    public function index(): void
    {
        require_admin();
        view('admin/services/index', [
            'title' => 'Hizmet Yönetimi',
            'services' => (new Service())->all(),
        ]);
    }

    public function save(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $model = new Service();
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'slug' => slugify($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'duration_minutes' => (int) ($_POST['duration_minutes'] ?? 30),
            'price' => (float) ($_POST['price'] ?? 0),
            'deposit_price' => (float) ($_POST['deposit_price'] ?? 0),
            'status' => (int) ($_POST['status'] ?? 1),
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        ];
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $model->update($id, $data);
        } else {
            $model->create($data);
        }
        flash('success', 'Hizmet kaydedildi.');
        redirect(admin_url('?route=services'));
    }
}
