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

class HomeController
{
    public function index(): void
    {
        $settings = (new Setting())->all();
        view('frontend/home', [
            'settings' => $settings,
            'sections' => (new PageSection())->allActive(),
            'services' => (new Service())->allActive(),
            'packages' => (new Package())->allActive(),
            'staff' => (new Staff())->allActive(),
            'campaigns' => (new Campaign())->allActive(),
            'reviews' => $this->reviews(),
            'gallery' => $this->gallery(),
            'faqs' => $this->faqs(),
        ]);
    }

    private function reviews(): array
    {
        return db()->query('SELECT * FROM reviews WHERE status = 1 ORDER BY sort_order')->fetchAll();
    }

    private function gallery(): array
    {
        return db()->query('SELECT * FROM gallery WHERE status = 1 ORDER BY sort_order')->fetchAll();
    }

    private function faqs(): array
    {
        return db()->query('SELECT * FROM faqs WHERE status = 1 ORDER BY sort_order')->fetchAll();
    }
}
