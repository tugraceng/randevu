<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Campaign;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Package;
use App\Models\PageSection;
use App\Models\Review;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\CustomerPackage;

class HomeController
{
    public function index(): void
    {
        $settings = (new Setting())->all();
        $customerPackages = [];
        if (is_customer_logged_in()) {
            $customerPackages = (new CustomerPackage())->forCustomer((int) customer_user()['id']);
        }

        view('frontend/home', [
            'settings' => $settings,
            'sections' => (new PageSection())->allActive(),
            'services' => (new Service())->allActive(),
            'packages' => (new Package())->allActive(),
            'staff' => (new Staff())->allActive(),
            'campaigns' => (new Campaign())->allActive(),
            'reviews' => (new Review())->allActive(),
            'gallery' => (new Gallery())->allActive(),
            'faqs' => (new Faq())->allActive(),
            'customer_packages' => $customerPackages,
        ]);
    }
}
