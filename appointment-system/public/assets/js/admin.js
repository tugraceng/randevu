/* ============================================================
 * RandevuTakip — Admin Premium UX
 *   01  Helpers ($ / $$ / fetchJSON)
 *   02  Toast / Confirm / Loader system
 *   03  Sidebar (mobile overlay + desktop collapse)
 *   04  Generic confirm-on-click
 *   05  Filter Reset & AJAX submit
 *   06  Template Variable Inserter
 *   07  Dashboard charts (Chart.js)
 *   08  Customer create AJAX (Appointment screen)
 *   09  Customer package loader (Appointment screen)
 *   10  Form skeleton + button loading
 *   11  Settings test buttons
 *   12  Animated counters
 * ============================================================ */

(function () {
    'use strict';

    /* --------------------------------------------------------
     * 01 Helpers
     * -------------------------------------------------------- */
    const $  = (sel, ctx = document) => ctx.querySelector(sel);
    const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

    const CSRF = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

    async function fetchJSON(url, opts = {}) {
        const isPost = (opts.method || 'GET').toUpperCase() === 'POST';
        const headers = Object.assign({
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }, opts.headers || {});
        if (isPost && !(opts.body instanceof FormData)) {
            headers['Content-Type'] = 'application/json';
            if (typeof opts.body === 'object') opts.body = JSON.stringify(opts.body);
        }
        const res = await fetch(url, { ...opts, headers });
        const text = await res.text();
        try { return JSON.parse(text); } catch { return { ok: res.ok, text }; }
    }

    window.Admin = { fetchJSON, toast, confirm: confirmDialog, $, $$ };

    document.addEventListener('DOMContentLoaded', () => {
        initSidebar();
        initConfirms();
        initFilterReset();
        initVarInserter();
        initDashboardCharts();
        initCustomerCreate();
        initCustomerPackages();
        initFormSubmitLoaders();
        initAnimatedCounters();
        initSettingsTests();
    });


    /* --------------------------------------------------------
     * 02 Toast / Confirm / Loader
     * -------------------------------------------------------- */
    function toast(message, type = 'info', timeout = 4000) {
        let area = document.getElementById('toast-area');
        if (!area) {
            area = document.createElement('div');
            area.id = 'toast-area';
            area.className = 'toast-area';
            document.body.appendChild(area);
        }
        const icons = { success: 'bi-check-circle', error: 'bi-x-octagon', warning: 'bi-exclamation-triangle', info: 'bi-info-circle' };
        const el = document.createElement('div');
        el.className = `toast-item ${type}`;
        el.innerHTML = `
            <span class="ic"><i class="bi ${icons[type] || icons.info}"></i></span>
            <span style="flex:1">${message}</span>
            <button class="close-btn" aria-label="Kapat"><i class="bi bi-x"></i></button>
        `;
        area.appendChild(el);
        const remove = () => { el.style.opacity = '0'; el.style.transform = 'translateX(20px)'; setTimeout(() => el.remove(), 200); };
        el.querySelector('.close-btn').addEventListener('click', remove);
        if (timeout) setTimeout(remove, timeout);
    }

    function confirmDialog(message = 'Emin misiniz?') {
        return new Promise((resolve) => {
            let modal = document.getElementById('confirmModal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'confirmModal';
                modal.className = 'modal fade';
                modal.tabIndex = -1;
                modal.innerHTML = `
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-body p-4 text-center">
                          <div class="mb-3" style="font-size:2.4rem;color:var(--warning);"><i class="bi bi-exclamation-triangle"></i></div>
                          <h5 class="mb-2">Onay</h5>
                          <p class="text-muted mb-4" data-confirm-text></p>
                          <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-outline-secondary" data-confirm-cancel>Vazgeç</button>
                            <button type="button" class="btn btn-danger" data-confirm-ok>Onayla</button>
                          </div>
                        </div>
                      </div>
                    </div>`;
                document.body.appendChild(modal);
            }
            modal.querySelector('[data-confirm-text]').textContent = message;
            const bsm = bootstrap.Modal.getOrCreateInstance(modal);
            const onOk     = () => { bsm.hide(); cleanup(); resolve(true); };
            const onCancel = () => { bsm.hide(); cleanup(); resolve(false); };
            const okBtn    = modal.querySelector('[data-confirm-ok]');
            const cnBtn    = modal.querySelector('[data-confirm-cancel]');
            const cleanup  = () => {
                okBtn.removeEventListener('click', onOk);
                cnBtn.removeEventListener('click', onCancel);
            };
            okBtn.addEventListener('click', onOk);
            cnBtn.addEventListener('click', onCancel);
            bsm.show();
        });
    }


    /* --------------------------------------------------------
     * 03 Sidebar (mobile overlay + desktop collapse)
     * -------------------------------------------------------- */
    function initSidebar() {
        const shell    = $('.admin-shell');
        const sidebar  = $('#adminSidebar');
        const overlay  = $('#sidebarOverlay');
        const btnOpen  = $('#sidebarOpen');
        const btnClose = $('#sidebarClose');

        const isMobile = () => window.innerWidth < 992;

        btnOpen?.addEventListener('click', () => {
            if (isMobile()) {
                sidebar.classList.add('show');
                overlay?.classList.add('active');
            } else {
                shell.classList.toggle('sidebar-collapsed');
                localStorage.setItem('admin_sb_collapsed', shell.classList.contains('sidebar-collapsed') ? '1' : '0');
            }
        });
        btnClose?.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay?.classList.remove('active');
        });
        overlay?.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('active');
        });

        if (!isMobile() && localStorage.getItem('admin_sb_collapsed') === '1') {
            shell?.classList.add('sidebar-collapsed');
        }
        window.addEventListener('resize', () => {
            if (isMobile()) {
                sidebar.classList.remove('show');
                overlay?.classList.remove('active');
            }
        });
    }


    /* --------------------------------------------------------
     * 04 Generic confirm-on-click for [data-confirm] elements
     * -------------------------------------------------------- */
    function initConfirms() {
        document.body.addEventListener('click', async (e) => {
            const el = e.target.closest('[data-confirm]');
            if (!el) return;
            e.preventDefault();
            const message = el.getAttribute('data-confirm') || 'Devam edilsin mi?';
            const ok = await confirmDialog(message);
            if (!ok) return;
            if (el.tagName === 'FORM') { el.submit(); return; }
            const form = el.closest('form');
            if (form) { form.submit(); return; }
            if (el.tagName === 'A' && el.href) {
                window.location.href = el.href;
            }
        });
    }


    /* --------------------------------------------------------
     * 05 Filter reset + auto-submit on change (date/select)
     * -------------------------------------------------------- */
    function initFilterReset() {
        $$('[data-filter-reset]').forEach(btn => {
            btn.addEventListener('click', () => {
                const form = btn.closest('form');
                if (!form) return;
                form.querySelectorAll('input, select').forEach(el => {
                    if (el.type === 'hidden') return;
                    if (el.type === 'checkbox' || el.type === 'radio') el.checked = false;
                    else el.value = '';
                });
                form.submit();
            });
        });

        $$('form[data-auto-filter] select, form[data-auto-filter] input[type="date"]').forEach(el => {
            el.addEventListener('change', () => el.form.submit());
        });
    }


    /* --------------------------------------------------------
     * 06 Template variable inserter (Messages screen)
     * -------------------------------------------------------- */
    function initVarInserter() {
        document.body.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-insert-var], [data-template-var]');
            if (!btn) return;
            e.preventDefault();
            const variable = btn.getAttribute('data-insert-var') || btn.getAttribute('data-template-var');
            const targetSel = btn.getAttribute('data-target') || '[data-template-body]';
            const ta = document.querySelector(targetSel);
            if (!ta || !variable) return;
            const pos = ta.selectionStart || ta.value.length;
            ta.value = ta.value.slice(0, pos) + variable + ta.value.slice(pos);
            ta.focus();
            const newPos = pos + variable.length;
            ta.setSelectionRange(newPos, newPos);
            toast(`${variable} eklendi`, 'success', 1800);
        });
    }


    /* --------------------------------------------------------
     * 07 Dashboard charts
     * -------------------------------------------------------- */
    function initDashboardCharts() {
        if (!window.Chart) return;

        Chart.defaults.font.family = "Inter, sans-serif";
        Chart.defaults.color = '#64748b';
        Chart.defaults.plugins.legend.labels.usePointStyle = true;

        const baseEndpoint = (window.APP_BASE || '/') + 'admin/?route=dashboard/chart-data';
        const charts = {
            weeklyAppointments: { canvas: 'chartWeekly',  type: 'line' },
            serviceBreakdown:   { canvas: 'chartService', type: 'doughnut' },
            staffPerformance:   { canvas: 'chartStaff',   type: 'bar' },
            paymentStatus:      { canvas: 'chartPayment', type: 'doughnut' }
        };

        Object.entries(charts).forEach(([key, def]) => {
            const canvas = document.getElementById(def.canvas);
            if (!canvas) return;
            fetchJSON(`${baseEndpoint}&type=${key}`)
                .then(j => renderChart(canvas, def.type, j))
                .catch(() => {});
        });
    }

    function renderChart(canvas, type, data) {
        if (!data || (!data.labels && !data.datasets)) return;
        const ctx = canvas.getContext('2d');
        const colors = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];
        let cfg;
        if (type === 'doughnut') {
            cfg = {
                type, data: {
                    labels: data.labels || [],
                    datasets: [{
                        data: data.data || data.datasets?.[0]?.data || [],
                        backgroundColor: colors,
                        borderWidth: 0,
                    }]
                },
                options: { plugins: { legend: { position: 'bottom' } }, cutout: '66%' }
            };
        } else if (type === 'line') {
            const grad = ctx.createLinearGradient(0, 0, 0, 260);
            grad.addColorStop(0, 'rgba(79,70,229,.35)');
            grad.addColorStop(1, 'rgba(79,70,229,0)');
            cfg = {
                type, data: {
                    labels: data.labels || [],
                    datasets: [{
                        label: data.label || 'Randevular',
                        data: data.data || [],
                        borderColor: '#4f46e5',
                        backgroundColor: grad,
                        borderWidth: 3,
                        tension: .4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.04)' } }, x: { grid: { display: false } } }
                }
            };
        } else {
            cfg = {
                type, data: {
                    labels: data.labels || [],
                    datasets: [{
                        label: data.label || '',
                        data: data.data || [],
                        backgroundColor: colors[0],
                        borderRadius: 8,
                        maxBarThickness: 36
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.04)' } }, x: { grid: { display: false } } }
                }
            };
        }
        new Chart(canvas, cfg);
    }


    /* --------------------------------------------------------
     * 08 Quick customer create on appointment screen
     * -------------------------------------------------------- */
    function initCustomerCreate() {
        const form = document.getElementById('quickCustomerForm');
        if (!form) return;
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = form.querySelector('[type="submit"]');
            btn?.setAttribute('data-loading', 'true');
            try {
                const fd = new FormData(form);
                const j  = await fetchJSON(form.action, { method: 'POST', body: fd });
                if (j && j.ok) {
                    toast('Müşteri oluşturuldu', 'success');
                    const sel = document.getElementById('customerSelect');
                    if (sel) {
                        const opt = new Option(`${j.customer.first_name} ${j.customer.last_name} (${j.customer.phone || j.customer.email})`, j.customer.id, true, true);
                        sel.add(opt);
                        sel.dispatchEvent(new Event('change'));
                    }
                    form.reset();
                    const collapse = bootstrap.Collapse.getInstance(form.closest('.collapse'));
                    collapse?.hide();
                } else {
                    toast(j.message || 'Müşteri oluşturulamadı.', 'error');
                }
            } catch (err) {
                toast('Bir hata oluştu.', 'error');
            } finally {
                btn?.removeAttribute('data-loading');
            }
        });
    }


    /* --------------------------------------------------------
     * 09 Customer package loader (Appointment screen)
     * -------------------------------------------------------- */
    function initCustomerPackages() {
        const sel  = document.getElementById('customerSelect');
        const pkgC = document.getElementById('customerPackages');
        if (!sel || !pkgC) return;
        sel.addEventListener('change', async () => {
            const id = sel.value;
            if (!id) { pkgC.innerHTML = ''; return; }
            pkgC.innerHTML = '<div class="skeleton" style="height:48px;border-radius:12px"></div>';
            try {
                const base = (window.APP_BASE || '/') + 'admin/?route=ajax/customer-packages&id=' + id;
                const j = await fetchJSON(base);
                if (!j.packages?.length) {
                    pkgC.innerHTML = '<small class="text-muted">Bu müşterinin aktif paketi yok.</small>';
                    return;
                }
                pkgC.innerHTML = '<label class="form-label">Paket kullan (opsiyonel)</label>' +
                    '<select class="form-select" name="customer_package_id">' +
                    '<option value="">Paket kullanma</option>' +
                    j.packages.map(p => `<option value="${p.id}">${p.package_name} · ${p.remaining_sessions} seans kaldı</option>`).join('') +
                    '</select>';
            } catch {
                pkgC.innerHTML = '';
            }
        });
    }


    /* --------------------------------------------------------
     * 10 Form submit loaders — buttons get data-loading="true"
     * -------------------------------------------------------- */
    function initFormSubmitLoaders() {
        $$('form').forEach(form => {
            form.addEventListener('submit', () => {
                const btn = form.querySelector('button[type="submit"]');
                if (btn) btn.setAttribute('data-loading', 'true');
            });
        });
    }


    /* --------------------------------------------------------
     * 11 Settings — test buttons (SMTP/NetGSM/WhatsApp)
     * -------------------------------------------------------- */
    function initSettingsTests() {
        $$('[data-test-endpoint]').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                btn.setAttribute('data-loading', 'true');
                const url = btn.dataset.testEndpoint;
                try {
                    const j = await fetchJSON(url, { method: 'POST', body: { _csrf: CSRF() } });
                    if (j.ok) toast(j.message || 'Bağlantı başarılı', 'success');
                    else      toast(j.message || 'Bağlantı başarısız', 'error');
                } catch {
                    toast('Bağlantı testi başarısız', 'error');
                } finally {
                    btn.removeAttribute('data-loading');
                }
            });
        });
    }


    /* --------------------------------------------------------
     * 12 Animated counters [data-counter]
     * -------------------------------------------------------- */
    function initAnimatedCounters() {
        const items = $$('[data-counter]');
        if (!items.length || !('IntersectionObserver' in window)) {
            items.forEach(el => el.textContent = el.dataset.counter);
            return;
        }
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (!e.isIntersecting) return;
                const target = parseFloat(e.target.dataset.counter) || 0;
                animate(e.target, target);
                io.unobserve(e.target);
            });
        }, { threshold: 0.4 });
        items.forEach(el => io.observe(el));
    }
    function animate(el, target, duration = 1100) {
        const start = performance.now();
        const step = (now) => {
            const p = Math.min((now - start) / duration, 1);
            const v = target * (1 - Math.pow(1 - p, 3));
            el.textContent = (target % 1 === 0) ? Math.round(v).toLocaleString('tr-TR') : v.toFixed(1);
            if (p < 1) requestAnimationFrame(step);
            else el.textContent = (target % 1 === 0) ? Math.round(target).toLocaleString('tr-TR') : target.toFixed(1);
        };
        requestAnimationFrame(step);
    }
})();
