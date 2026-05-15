<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Faq;
use App\Models\Gallery;
use App\Models\PageSection;
use App\Models\Review;

class AdminContentController
{
    public function index(): void
    {
        require_admin();
        view('admin/content/index', [
            'title' => 'Sayfa İçerikleri',
            'sections' => (new PageSection())->all(),
            'faqs' => (new Faq())->all(),
            'gallery' => (new Gallery())->all(),
            'reviews' => (new Review())->all(),
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

    public function saveFaq(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        (new Faq())->save([
            'question' => trim($_POST['question'] ?? ''),
            'answer' => trim($_POST['answer'] ?? ''),
            'status' => (int) ($_POST['status'] ?? 1),
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        ], !empty($_POST['id']) ? (int) $_POST['id'] : null);
        flash('success', 'SSS kaydedildi.');
        redirect(admin_url('?route=content#faq'));
    }

    public function saveGallery(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        $image = $_POST['existing_image'] ?? '';
        if (!empty($_FILES['image']['name'])) {
            $image = upload_image($_FILES['image'], 'gallery');
        }
        if (!$image) {
            flash('error', 'Görsel gerekli.');
            redirect(admin_url('?route=content'));
        }
        (new Gallery())->save([
            'title' => trim($_POST['title'] ?? ''),
            'image' => $image,
            'category' => trim($_POST['category'] ?? ''),
            'status' => (int) ($_POST['status'] ?? 1),
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        ], !empty($_POST['id']) ? (int) $_POST['id'] : null);
        flash('success', 'Galeri kaydedildi.');
        redirect(admin_url('?route=content#gallery'));
    }

    public function saveReview(): void
    {
        require_admin();
        if (!verify_csrf()) {
            csrf_abort();
        }
        (new Review())->save([
            'customer_name' => trim($_POST['customer_name'] ?? ''),
            'comment' => trim($_POST['comment'] ?? ''),
            'rating' => (int) ($_POST['rating'] ?? 5),
            'status' => (int) ($_POST['status'] ?? 1),
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        ], !empty($_POST['id']) ? (int) $_POST['id'] : null);
        flash('success', 'Yorum kaydedildi.');
        redirect(admin_url('?route=content#reviews'));
    }
}
