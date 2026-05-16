/* ============================================================
 * RandevuTakip - Admin JS
 * SaaS-tarzı yönetim paneli etkileşimleri.
 *
 * Bölümler:
 *   1. Helpers (toast, fetch, qs)
 *   2. Sidebar toggle (mobile overlay)
 *   3. Confirm dialogs (data-confirm)
 *   4. Filter bar reset
 *   5. Template variable inserter (data-insert-var | data-template-var)
 *   6. Dashboard charts (Chart.js)
 *   7. Customer packages dynamic loader
 *   8. Appointment slots loader (admin create / edit)
 *   9. Appointment live summary card
 *  10. Inline AJAX customer create (appointment create)
 *  11. Booking summary on customer.show actions
 * ============================================================ */

(function () {
    'use strict';

    const APP_BASE = (window.APP_BASE || '').replace(/\/+$/, '');
    const ADMIN_BASE = (window.ADMIN_BASE || '').replace(/\/+$/, '');

    /* 1. Helpers
     * ------------------------------------------------------- */
    const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
    const $  = (sel, ctx = document) => ctx.querySelector(sel);

    const fmtMoney = (n) => {
        try { return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(n); }
        catch { return (n || 0) + ' ₺'; }
    };

    function ensureToastArea() {
        let area = $('#toast-area');
        if (!area) {
            area = document.createElement('div');
            area.id = 'toast-area';
            area.className = 'toast-area';
            document.body.appendChild(area);
        }
        return area;
    }

    function toast(message, type = 'success', timeout = 4500) {
        const area = ensureToastArea();
        const el = document.createElement('div');
        el.className = 'toast-msg ' + type;
        const icon = type === 'success' ? 'bi-check-circle'
                   : type === 'danger'  ? 'bi-x-circle'
                   : type === 'warning' ? 'bi-exclamation-triangle'
                                        : 'bi-info-circle';
        el.innerHTML = `
            <i class="bi ${icon} icon"></i>
            <div class="flex-grow-1 small">${message}</div>
            <button type="button" class="btn-close btn-close-sm" aria-label="Close"></button>
        `;
        el.querySelector('.btn-close').addEventListener('click', () => el.remove());
        area.appendChild(el);
        if (timeout) setTimeout(() => el.remove(), timeout);
        return el;
    }
    window.adminToast = toast;

    /* 2. Sidebar toggle (mobile overlay)
     * ------------------------------------------------------- */
    function initSidebar() {
        const sidebar = $('#adminSidebar');
        const overlay = $('#sidebarOverlay');
        const open    = $('#sidebarOpen');
        const close   = $('#sidebarClose');
        if (!sidebar) return;

        const openFn = () => {
            sidebar.classList.add('open');
            overlay?.classList.add('show');
            document.body.style.overflow = 'hidden';
        };
        const closeFn = () => {
            sidebar.classList.remove('open');
            overlay?.classList.remove('show');
            document.body.style.overflow = '';
        };

        open?.addEventListener('click', openFn);
        close?.addEventListener('click', closeFn);
        overlay?.addEventListener('click', closeFn);
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) closeFn();
        });
    }

    /* 3. Confirm dialogs
     * ------------------------------------------------------- */
    function initConfirm() {
        document.addEventListener('click', e => {
            const el = e.target.closest('[data-confirm]');
            if (!el) return;
            const msg = el.dataset.confirm || 'Bu işlemi yapmak istediğinize emin misiniz?';
            if (!window.confirm(msg)) {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
        }, true);
    }

    /* 4. Filter bar reset
     * ------------------------------------------------------- */
    function initFilterReset() {
        $$('[data-filter-reset]').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const target = btn.dataset.filterReset || 'form';
                const form = btn.closest(target);
                if (!form) return;
                form.querySelectorAll('input, select').forEach(input => {
                    if (input.type === 'hidden') return;
                    if (input.tagName === 'SELECT') input.selectedIndex = 0;
                    else input.value = '';
                });
                if (btn.dataset.submit !== 'false') form.submit();
            });
        });
    }

    /* 5. Template variable inserter
     * ------------------------------------------------------- */
    function initVarInserter() {
        document.addEventListener('click', e => {
            const btn = e.target.closest('[data-insert-var], [data-template-var]');
            if (!btn) return;
            e.preventDefault();
            const variable = btn.dataset.insertVar || btn.dataset.templateVar || '';
            const targetSel = btn.dataset.target || '';
            let target = targetSel ? document.querySelector(targetSel) : null;
            if (!target) {
                const wrap = btn.closest('form, .template-card');
                target = wrap?.querySelector('textarea, [contenteditable], input[name="body"], input[name="content"]');
            }
            if (!target) return;
            const v = variable.startsWith('{') ? variable : ('{' + variable + '}');
            if ('value' in target) {
                const start = target.selectionStart ?? target.value.length;
                const end = target.selectionEnd ?? target.value.length;
                target.value = target.value.substring(0, start) + v + target.value.substring(end);
                target.focus();
                target.selectionStart = target.selectionEnd = start + v.length;
            } else {
                target.textContent = (target.textContent || '') + v;
            }
        });
    }

    /* 6. Dashboard charts (Chart.js)
     * ------------------------------------------------------- */
    async function loadDashboardCharts() {
        if (!document.getElementById('dashboardCharts') || !window.Chart) return;
        try {
            const url = (ADMIN_BASE || '') + '?route=api/charts';
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            renderChart('chartDaily', 'line',
                (data.daily || []).map(r => r.d),
                (data.daily || []).map(r => r.cnt),
                'Günlük Randevu');
            renderChart('chartService', 'doughnut',
                (data.by_service || []).map(r => r.label),
                (data.by_service || []).map(r => r.cnt));
            renderChart('chartStaff', 'bar',
                (data.by_staff || []).map(r => r.label),
                (data.by_staff || []).map(r => r.cnt));
            renderChart('chartPayment', 'pie',
                (data.payments || []).map(r => r.label),
                (data.payments || []).map(r => r.cnt));
            renderChart('chartPackage', 'bar',
                (data.packages || []).map(r => r.label),
                (data.packages || []).map(r => r.cnt),
                'Paket Satış');
        } catch (e) {
            console.warn('Chart yükleme hatası', e);
        }
    }

    function renderChart(id, type, labels, values, label = '') {
        const el = document.getElementById(id);
        if (!el || !window.Chart) return;
        const palette = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316'];
        const single = type === 'doughnut' || type === 'pie';
        const ctx = el.getContext ? el.getContext('2d') : el;
        if (el._chartInstance) el._chartInstance.destroy();
        el._chartInstance = new Chart(ctx, {
            type,
            data: {
                labels,
                datasets: [{
                    label: label || id,
                    data: values,
                    backgroundColor: single ? palette : 'rgba(79,70,229,.4)',
                    borderColor: single ? '#fff' : '#4f46e5',
                    borderWidth: single ? 2 : 2,
                    fill: type === 'line',
                    tension: .35,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4f46e5'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: single, position: 'bottom', labels: { font: { size: 11 } } }
                },
                scales: single ? {} : {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.06)' }, ticks: { precision: 0 } }
                }
            }
        });
    }

    /* 7. Customer packages dynamic loader
     * ------------------------------------------------------- */
    function initCustomerPackages() {
        const customerSelect = document.getElementById('customer_id');
        if (!customerSelect || !customerSelect.dataset.packagesUrl) return;

        async function load() {
            const pkg = document.getElementById('customer_package_id');
            if (!pkg || !customerSelect.value) return;
            try {
                const url = customerSelect.dataset.packagesUrl + '&customer_id=' + encodeURIComponent(customerSelect.value);
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                pkg.innerHTML = '<option value="">Paket kullanma</option>';
                (data.packages || []).forEach(p => {
                    const o = document.createElement('option');
                    o.value = p.id;
                    o.textContent = `${p.package_name} — ${p.remaining_sessions} seans kaldı`;
                    o.dataset.serviceId = p.service_id;
                    o.dataset.remainingSessions = p.remaining_sessions;
                    pkg.appendChild(o);
                });
                updateSummary();
            } catch (e) {
                console.warn('Paket yüklenemedi', e);
            }
        }

        customerSelect.addEventListener('change', () => { load(); updateSummary(); });

        const pkgSel = document.getElementById('customer_package_id');
        pkgSel?.addEventListener('change', () => {
            const opt = pkgSel.selectedOptions[0];
            const sid = opt?.dataset.serviceId;
            if (sid) {
                const svc = document.getElementById('service_id');
                if (svc) {
                    const o = Array.from(svc.options).find(o => o.value === sid);
                    if (o) { svc.value = sid; svc.dispatchEvent(new Event('change')); }
                }
            }
            updateSummary();
        });

        if (customerSelect.value) load();
    }

    /* 8. Appointment slots loader (admin)
     * ------------------------------------------------------- */
    async function loadAdminSlots() {
        const service = document.getElementById('service_id');
        const staff   = document.getElementById('staff_id');
        const date    = document.getElementById('appointment_date');
        const container = document.getElementById('slots-container');
        if (!service?.value || !date?.value || !container) return;
        container.innerHTML = '<small class="text-muted">Saatler yükleniyor...</small>';
        const params = new URLSearchParams({
            service_id: service.value,
            date: date.value,
            staff_id: staff?.value || ''
        });
        try {
            const res = await fetch(`${APP_BASE}/api/slots?${params.toString()}`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            container.innerHTML = '';
            if (!data.slots || data.slots.length === 0) {
                container.innerHTML = '<p class="text-muted small mb-0">Bu tarihte uygun saat bulunamadı.</p>';
                return;
            }
            data.slots.forEach(slot => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'slot-btn';
                btn.textContent = slot.start;
                btn.addEventListener('click', () => {
                    container.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    const startTime = document.getElementById('start_time');
                    if (startTime) startTime.value = slot.start;
                    updateSummary();
                });
                container.appendChild(btn);
            });
        } catch (e) {
            container.innerHTML = '<p class="text-danger small mb-0">Saatler alınamadı.</p>';
        }
    }

    function initAdminSlots() {
        const service = document.getElementById('service_id');
        const staff   = document.getElementById('staff_id');
        const date    = document.getElementById('appointment_date');
        if (!service && !staff && !date) return;
        service?.addEventListener('change', () => { loadAdminSlots(); updateSummary(); });
        staff?.addEventListener('change', () => { loadAdminSlots(); updateSummary(); });
        date?.addEventListener('change', () => { loadAdminSlots(); updateSummary(); });
    }

    /* 9. Appointment live summary card
     * ------------------------------------------------------- */
    function updateSummary() {
        const summary = document.getElementById('appointment-summary');
        if (!summary) return;
        const customer = document.getElementById('customer_id');
        const service  = document.getElementById('service_id');
        const staff    = document.getElementById('staff_id');
        const date     = document.getElementById('appointment_date');
        const startTime = document.getElementById('start_time');
        const pkg      = document.getElementById('customer_package_id');
        const deposit  = document.querySelector('input[name="deposit_amount"]');
        const payReq   = document.querySelector('input[name="payment_required"]');

        const cOpt = customer?.selectedOptions[0];
        const sOpt = service?.selectedOptions[0];
        const stOpt = staff?.selectedOptions[0];
        const pOpt = pkg?.selectedOptions[0];

        const customerLabel = cOpt && cOpt.value ? cOpt.textContent.trim() : '—';
        const serviceLabel  = sOpt && sOpt.value ? sOpt.textContent.trim() : '—';
        const staffLabel    = stOpt && stOpt.value ? stOpt.textContent.trim() : 'Farketmez';
        const pkgLabel      = pOpt && pOpt.value ? pOpt.textContent.trim() : 'Yok';
        const remaining     = pOpt?.dataset.remainingSessions || '—';
        const paymentLabel  = payReq?.checked
            ? 'Ödeme bekliyor' + (deposit?.value ? ` · Kapora ${fmtMoney(parseFloat(deposit.value))}` : '')
            : 'Ödeme gerekmiyor';

        const rows = [
            ['Müşteri',  customerLabel],
            ['Hizmet',   serviceLabel],
            ['Personel', staffLabel],
            ['Tarih',    date?.value || '—'],
            ['Saat',     startTime?.value || '—'],
            ['Paket',    pkgLabel],
            ['Kalan Seans', pOpt && pOpt.value ? remaining + ' seans' : '—'],
            ['Ödeme',    paymentLabel]
        ];
        summary.innerHTML = `
            <h6 class="fw-semibold mb-3 text-muted small text-uppercase">Canlı Özet</h6>
            ${rows.map(r => `
                <div class="d-flex justify-content-between py-1 border-bottom border-light small">
                    <span class="text-muted">${r[0]}</span>
                    <span class="fw-semibold text-end">${r[1]}</span>
                </div>
            `).join('')}
        `;
    }

    function initSummary() {
        if (!document.getElementById('appointment-summary')) return;
        document.querySelectorAll('#appointment-form input, #appointment-form select')
            .forEach(el => el.addEventListener('change', updateSummary));
        document.querySelectorAll('#appointment-form input[type="checkbox"], #appointment-form input[type="number"]')
            .forEach(el => el.addEventListener('input', updateSummary));
        updateSummary();
    }

    /* 10. Inline AJAX customer create
     * ------------------------------------------------------- */
    function initInlineCustomerCreate() {
        const form = document.getElementById('admin-customer-create-form');
        if (!form || !window.ADMIN_CUSTOMER_CREATE_URL) return;

        form.addEventListener('submit', async e => {
            e.preventDefault();
            const btn = document.getElementById('btn-create-customer');
            const msg = document.getElementById('customer-create-msg');
            btn.disabled = true;
            if (msg) { msg.textContent = 'Kaydediliyor...'; msg.className = 'ms-2 small text-muted'; }
            try {
                const res = await fetch(window.ADMIN_CUSTOMER_CREATE_URL, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (!data.success) {
                    const txt = data.message || 'Müşteri eklenemedi.';
                    if (msg) { msg.textContent = txt; msg.className = 'ms-2 small text-danger'; }
                    toast(txt, 'danger');
                    return;
                }
                const sel = document.getElementById('customer_id');
                if (sel && data.customer) {
                    const opt = document.createElement('option');
                    opt.value = data.customer.id;
                    opt.textContent = data.customer.label;
                    opt.selected = true;
                    sel.appendChild(opt);
                    sel.dispatchEvent(new Event('change'));
                }
                let text = data.message || 'Müşteri eklendi.';
                if (data.customer?.temp_password) text += ' Şifre: ' + data.customer.temp_password;
                if (msg) { msg.textContent = text; msg.className = 'ms-2 small text-success'; }
                toast('Müşteri başarıyla eklendi.', 'success');
                form.reset();
                updateSummary();
                document.getElementById('customer_id')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } catch (err) {
                if (msg) { msg.textContent = 'Bağlantı hatası.'; msg.className = 'ms-2 small text-danger'; }
                toast('Bağlantı hatası.', 'danger');
            } finally {
                btn.disabled = false;
            }
        });
    }

    /* 11. Boot
     * ------------------------------------------------------- */
    document.addEventListener('DOMContentLoaded', () => {
        initSidebar();
        initConfirm();
        initFilterReset();
        initVarInserter();
        loadDashboardCharts();
        initCustomerPackages();
        initAdminSlots();
        initSummary();
        initInlineCustomerCreate();

        $$('.alert-dismissible').forEach(a => {
            setTimeout(() => a.classList.add('show'), 0);
        });
    });
})();
