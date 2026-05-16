<?php require APP_PATH . '/Views/admin/partials/header.php';
$from = $_GET['from'] ?? date('Y-m-01');
$to   = $_GET['to']   ?? date('Y-m-d');
$totalRevenue = 0;
foreach ($revenue ?? [] as $r) $totalRevenue += (float)$r['total'];
$statusCounts = [];
foreach ($appointments ?? [] as $a) $statusCounts[$a['status']] = (int)$a['cnt'];
$totalApp = array_sum($statusCounts);
?>

<div class="section-title-bar">
    <div>
        <h5>Raporlar</h5>
        <small class="text-muted">Performans ve gelir analizleriniz</small>
    </div>
    <a href="<?= admin_url('?route=reports/export&from=' . urlencode($from) . '&to=' . urlencode($to)) ?>" class="btn btn-primary">
        <i class="bi bi-download me-1"></i> CSV Dışa Aktar
    </a>
</div>

<form class="filter-bar row g-3 align-items-end" method="get">
    <input type="hidden" name="route" value="reports">
    <div class="col-md-3">
        <label>Başlangıç</label>
        <input type="date" class="form-control form-control-sm" name="from" value="<?= e($from) ?>">
    </div>
    <div class="col-md-3">
        <label>Bitiş</label>
        <input type="date" class="form-control form-control-sm" name="to" value="<?= e($to) ?>">
    </div>
    <div class="col-md-3 filter-actions">
        <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i> Filtrele</button>
        <a href="<?= admin_url('?route=reports') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card tone-success">
            <span class="stat-icon"><i class="bi bi-cash-stack"></i></span>
            <div class="stat-label">Dönem Geliri</div>
            <div class="stat-value"><?= format_money($totalRevenue) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <span class="stat-icon"><i class="bi bi-calendar-check"></i></span>
            <div class="stat-label">Randevu</div>
            <div class="stat-value"><?= number_format($totalApp) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card tone-info">
            <span class="stat-icon"><i class="bi bi-check2-all"></i></span>
            <div class="stat-label">Tamamlanan</div>
            <div class="stat-value"><?= number_format($statusCounts['completed'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card tone-danger">
            <span class="stat-icon"><i class="bi bi-x-circle"></i></span>
            <div class="stat-label">İptal</div>
            <div class="stat-value"><?= number_format($statusCounts['cancelled'] ?? 0) ?></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="chart-card">
            <div class="chart-head">
                <h6>Aylık Gelir Trendi</h6>
                <small><?= count($revenue ?? []) ?> ay</small>
            </div>
            <div style="height: 260px;"><canvas id="reportRevenue"></canvas></div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="chart-card">
            <div class="chart-head">
                <h6>Randevu Durum Dağılımı</h6>
            </div>
            <div style="height: 260px;"><canvas id="reportStatus"></canvas></div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-header"><h6><i class="bi bi-cash-coin me-1"></i> Aylık Gelir Tablosu</h6></div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Ay</th><th class="text-end">Toplam</th></tr></thead>
                    <tbody>
                        <?php foreach ($revenue ?? [] as $r): ?>
                        <tr><td><?= e($r['month']) ?></td><td class="text-end"><strong><?= format_money((float)$r['total']) ?></strong></td></tr>
                        <?php endforeach; ?>
                        <?php if (empty($revenue)): ?>
                        <tr><td colspan="2"><div class="empty-state py-3"><div class="icon"><i class="bi bi-cash"></i></div><h6>Veri yok</h6></div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-header"><h6><i class="bi bi-clock-history me-1"></i> Sistem Logları</h6></div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead><tr><th>Tarih</th><th>İşlem</th><th>Açıklama</th></tr></thead>
                    <tbody>
                        <?php foreach ($logs ?? [] as $log): ?>
                        <tr>
                            <td class="text-muted small"><?= e($log['created_at']) ?></td>
                            <td><span class="chip"><?= e($log['action']) ?></span></td>
                            <td class="small"><?= e($log['description'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($logs)): ?>
                        <tr><td colspan="3"><div class="empty-state py-3"><div class="icon"><i class="bi bi-clock"></i></div><h6>Log yok</h6></div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (!window.Chart) return;
    const palette = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];

    const revenueData = <?= json_encode(array_values($revenue ?? []), JSON_UNESCAPED_UNICODE) ?>;
    const rEl = document.getElementById('reportRevenue');
    if (rEl) new Chart(rEl, {
        type: 'line',
        data: {
            labels: revenueData.map(r => r.month),
            datasets: [{ label: 'Gelir', data: revenueData.map(r => parseFloat(r.total)), borderColor: '#4f46e5', backgroundColor: 'rgba(79,70,229,.18)', fill: true, tension: .35 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    const statusData = <?= json_encode($statusCounts) ?>;
    const sEl = document.getElementById('reportStatus');
    if (sEl) new Chart(sEl, {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData),
            datasets: [{ data: Object.values(statusData), backgroundColor: palette }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });
});
</script>

<?php require APP_PATH . '/Views/admin/partials/footer.php'; ?>
