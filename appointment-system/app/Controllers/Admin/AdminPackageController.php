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

    public function show(): void
    {
        require_admin();
        $id = (int) ($_GET['id'] ?? 0);
        $cp = (new \App\Models\CustomerPackage())->find($id);
        if (!$cp) {
            flash('error', 'Paket kaydı bulunamadı.');
            redirect(admin_url('?route=packages'));
        }
        $logs = (new \App\Services\PackageSessionService())->sessionLogs($id);
        $aptStmt = db()->prepare(
            'SELECT a.*, s.name AS service_name FROM appointments a
             JOIN services s ON s.id = a.service_id WHERE a.customer_package_id = ? ORDER BY a.appointment_date DESC'
        );
        $aptStmt->execute([$id]);
        view('admin/packages/show', [
            'title' => 'Paket Detay',
            'package' => $cp,
            'logs' => $logs,
            'appointments' => $aptStmt->fetchAll(),
        ]);
    }

    public function adjustSession(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $cpId = (int) $_POST['customer_package_id'];
        $delta = (int) $_POST['delta'];
        $result = (new \App\Services\PackageSessionService())->manualAdjust($cpId, $delta, trim($_POST['note'] ?? ''));
        flash($result['success'] ? 'success' : 'error', $result['message'] ?? 'Seans güncellendi.');
        redirect(admin_url('?route=packages/show&id=' . $cpId));
    }
}
