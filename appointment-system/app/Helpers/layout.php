<?php

declare(strict_types=1);

function admin_breadcrumb(array $items): string
{
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">';
    foreach ($items as $i => $item) {
        $active = $i === count($items) - 1;
        if ($active) {
            $html .= '<li class="breadcrumb-item active">' . e($item['label']) . '</li>';
        } else {
            $html .= '<li class="breadcrumb-item"><a href="' . e($item['url'] ?? '#') . '">' . e($item['label']) . '</a></li>';
        }
    }
    return $html . '</ol></nav>';
}

function status_badge(string $status): string
{
    $map = [
        'pending' => 'warning',
        'approved' => 'primary',
        'completed' => 'success',
        'cancelled' => 'secondary',
        'no_show' => 'danger',
        'paid' => 'success',
        'failed' => 'danger',
        'refunded' => 'info',
        'not_required' => 'light',
    ];
    $labels = [
        'pending' => 'Bekliyor',
        'approved' => 'Onaylı',
        'completed' => 'Tamamlandı',
        'cancelled' => 'İptal',
        'no_show' => 'Gelmedi',
        'paid' => 'Ödendi',
        'failed' => 'Başarısız',
        'refunded' => 'İade',
        'not_required' => 'Gerekmez',
    ];
    $color = $map[$status] ?? 'secondary';
    $label = $labels[$status] ?? $status;
    return '<span class="badge bg-' . $color . '">' . e($label) . '</span>';
}

function pagination_links(int $total, int $page, int $perPage, string $baseUrl): string
{
    $pages = (int) ceil($total / max(1, $perPage));
    if ($pages <= 1) {
        return '';
    }
    $html = '<nav><ul class="pagination pagination-sm mb-0">';
    for ($p = 1; $p <= $pages; $p++) {
        $sep = str_contains($baseUrl, '?') ? '&' : '?';
        $active = $p === $page ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . e($baseUrl . $sep . 'page=' . $p) . '">' . $p . '</a></li>';
    }
    return $html . '</ul></nav>';
}
