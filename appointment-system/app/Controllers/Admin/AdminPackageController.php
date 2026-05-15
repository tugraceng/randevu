<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Package;
use App\Models\Service;

class AdminPackageController
{
    public function index(): void
    {
        require_admin();
        view('admin/packages/index', [
            'title' => 'Paket Yönetimi',
            'packages' => (new Package())->all(),
            'services' => (new Service())->allActive(),
        ]);
    }

    public function save(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $model = new Package();
        $data = [
            'service_id' => (int) $_POST['service_id'],
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'session_count' => (int) $_POST['session_count'],
            'price' => (float) $_POST['price'],
            'validity_days' => (int) ($_POST['validity_days'] ?? 180),
            'status' => (int) ($_POST['status'] ?? 1),
        ];
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $model->update($id, $data);
        } else {
            $model->create($data);
        }
        flash('success', 'Paket kaydedildi.');
        redirect(admin_url('?route=packages'));
    }
}
