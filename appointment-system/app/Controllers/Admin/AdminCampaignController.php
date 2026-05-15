<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Campaign;
use App\Models\Customer;
use App\Services\NotificationService;

class AdminCampaignController
{
    public function index(): void
    {
        require_admin();
        view('admin/campaigns/index', [
            'title' => 'Kampanya Yönetimi',
            'campaigns' => (new Campaign())->all(),
            'customers' => (new Customer())->paginate(1, 200)['data'],
        ]);
    }

    public function send(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $ids = array_map('intval', $_POST['customer_ids'] ?? []);
        $campaignId = (int) ($_POST['campaign_id'] ?? 0);
        (new NotificationService())->sendCampaign($ids, $campaignId);
        flash('success', 'Kampanya gönderimi başlatıldı.');
        redirect(admin_url('?route=campaigns'));
    }
}
