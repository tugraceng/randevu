<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Service;
use App\Models\Staff;
use App\Models\WorkingHours;

class AdminStaffController
{
    public function index(): void
    {
        require_admin();
        $staffModel = new Staff();
        $editId = (int) ($_GET['edit'] ?? 0);
        $edit = $editId ? $staffModel->find($editId) : null;
        $hoursModel = new WorkingHours();

        view('admin/staff/index', [
            'title' => 'Personel Yönetimi',
            'breadcrumb' => [
                ['label' => 'Kontrol Paneli', 'url' => admin_url('?route=')],
                ['label' => 'Personel'],
            ],
            'staff' => $staffModel->all(),
            'services' => (new Service())->allActive(),
            'edit' => $edit,
            'edit_service_ids' => $edit ? $staffModel->serviceIds($editId) : [],
            'edit_hours' => $edit ? $hoursModel->forStaff($editId) : $hoursModel->forStaff(null),
            'day_labels' => WorkingHours::DAYS,
        ]);
    }

    public function save(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }

        $id = (int) ($_POST['id'] ?? 0);
        $staffModel = new Staff();
        $existing = $id ? $staffModel->find($id) : null;

        $photo = $existing['photo'] ?? null;
        if (!empty($_FILES['photo']['name'])) {
            try {
                $photo = upload_image($_FILES['photo'], 'staff');
            } catch (\Throwable $e) {
                flash('error', $e->getMessage());
                redirect(admin_url('?route=staff' . ($id ? '&edit=' . $id : '')));
            }
        } elseif (!empty($_POST['remove_photo'])) {
            $photo = null;
        }

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'title' => trim($_POST['title'] ?? ''),
            'bio' => trim($_POST['bio'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'photo' => $photo,
            'status' => (int) ($_POST['status'] ?? 1),
        ];

        if ($id) {
            $staffModel->update($id, $data);
        } else {
            $id = $staffModel->create($data);
        }

        $staffModel->syncServices($id, $_POST['service_ids'] ?? []);
        (new WorkingHours())->syncForStaff($id, $_POST['hours'] ?? []);

        flash('success', 'Personel kaydedildi.');
        redirect(admin_url('?route=staff'));
    }
}
