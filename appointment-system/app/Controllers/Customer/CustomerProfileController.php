<?php

declare(strict_types=1);

namespace App\Controllers\Customer;

use App\Models\Customer;

class CustomerProfileController
{
    public function index(): void
    {
        require_customer();
        $customer = (new Customer())->find((int) customer_user()['id']);
        view('customer/profile/index', ['title' => 'Profilim', 'customer' => $customer]);
    }

    public function update(): void
    {
        require_customer();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $id = (int) customer_user()['id'];
        (new Customer())->updateProfile($id, [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'sms_permission' => (int) ($_POST['sms_permission'] ?? 0),
            'whatsapp_permission' => (int) ($_POST['whatsapp_permission'] ?? 0),
            'marketing_permission' => (int) ($_POST['marketing_permission'] ?? 0),
        ]);
        $customer = (new Customer())->find($id);
        login_customer($customer);
        flash('success', 'Profil güncellendi.');
        redirect(customer_url('?route=profile'));
    }

    public function password(): void
    {
        require_customer();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $customer = (new Customer())->find((int) customer_user()['id']);
        if (!password_verify($_POST['current_password'] ?? '', $customer['password'])) {
            flash('error', 'Mevcut şifre hatalı.');
            redirect(customer_url('?route=profile'));
        }
        (new Customer())->updatePassword((int) $customer['id'], password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT));
        flash('success', 'Şifre değiştirildi.');
        redirect(customer_url('?route=profile'));
    }
}
