<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Service;
use App\Models\Staff;

class AdminStaffController
{
    public function index(): void
    {
        require_admin();
        $staffModel = new Staff();
        view('admin/staff/index', [
            'title' => 'Personel Yönetimi',
            'staff' => $staffModel->all(),
            'services' => (new Service())->allActive(),
        ]);
    }

    public function save(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $id = (int) ($_POST['id'] ?? 0);
        $pdo = db();
        if ($id) {
            $stmt = $pdo->prepare('UPDATE staff SET name=?, title=?, bio=?, phone=?, email=?, status=? WHERE id=?');
            $stmt->execute([
                trim($_POST['name'] ?? ''), trim($_POST['title'] ?? ''), trim($_POST['bio'] ?? ''),
                trim($_POST['phone'] ?? ''), trim($_POST['email'] ?? ''), (int) ($_POST['status'] ?? 1), $id,
            ]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO staff (name, title, bio, phone, email, status) VALUES (?,?,?,?,?,?)');
            $stmt->execute([
                trim($_POST['name'] ?? ''), trim($_POST['title'] ?? ''), trim($_POST['bio'] ?? ''),
                trim($_POST['phone'] ?? ''), trim($_POST['email'] ?? ''), 1,
            ]);
            $id = (int) $pdo->lastInsertId();
        }
        (new Staff())->syncServices($id, $_POST['service_ids'] ?? []);
        flash('success', 'Personel kaydedildi.');
        redirect(admin_url('?route=staff'));
    }
}
