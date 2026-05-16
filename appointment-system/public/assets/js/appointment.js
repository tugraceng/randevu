/* ============================================================
 * RandevuTakip - Appointment Booking JS
 * Frontend modal stepper + AJAX slot loading + live summary.
 *
 * Bölümler:
 *   1. Helpers
 *   2. State + cached selectors
 *   3. Step navigation
 *   4. Slot loader (AJAX)
 *   5. Live summary card render
 *   6. Submit handler
 *   7. Bootstrap on modal show
 * ============================================================ */

(function () {
    'use strict';

    const APP_BASE = (window.APP_BASE || '').replace(/\/+$/, '');
    const csrfToken = window.CSRF || '';

    let modalEl = null;
    let form = null;
    let summaryEl = null;
    let stepIndicators = [];
    let panels = [];
    let prevBtn = null;
    let nextBtn = null;
    let submitBtn = null;
    let serviceSel = null;
    let staffSel = null;
    let dateInput = null;
    let slotsContainer = null;
    let startTimeInput = null;
    let pkgSel = null;
    let currentStep = 1;
    const totalSteps = 4;

    /* 1. Helpers
     * ------------------------------------------------------- */
    const $$ = (sel, ctx = document) => ctx.querySelectorAll(sel);
    const $  = (sel, ctx = document) => ctx.querySelector(sel);

    const fmtMoney = (n) => {
        try { return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(n); }
        catch { return n + ' ₺'; }
    };

    /* 2. State + cached selectors
     * ------------------------------------------------------- */
    function cache() {
        modalEl = document.getElementById('appointmentModal');
        if (!modalEl) return false;
        form           = $('#appointment-form', modalEl);
        if (!form) return false;
        summaryEl      = $('#appointment-summary', modalEl);
        stepIndicators = $$('.stepper-bar .step', modalEl);
        panels         = $$('.step-panel', modalEl);
        prevBtn        = $('#btn-prev', modalEl);
        nextBtn        = $('#btn-next', modalEl);
        submitBtn      = $('#btn-submit', modalEl);
        serviceSel     = $('#service_id', modalEl);
        staffSel       = $('#staff_id', modalEl);
        dateInput      = $('#appointment_date', modalEl);
        slotsContainer = $('#slots-container', modalEl);
        startTimeInput = $('#start_time', modalEl);
        pkgSel         = $('#customer_package_id', modalEl);
        return true;
    }

    /* 3. Step navigation
     * ------------------------------------------------------- */
    function showStep(step) {
        currentStep = Math.max(1, Math.min(step, totalSteps));
        panels.forEach(p => {
            p.classList.toggle('d-none', parseInt(p.dataset.panel, 10) !== currentStep);
        });
        stepIndicators.forEach(ind => {
            const n = parseInt(ind.dataset.step, 10);
            ind.classList.toggle('active', n === currentStep);
            ind.classList.toggle('done', n < currentStep);
        });
        if (prevBtn) prevBtn.disabled = currentStep === 1;
        if (nextBtn) nextBtn.classList.toggle('d-none', currentStep === totalSteps);
        if (submitBtn) submitBtn.classList.toggle('d-none', currentStep !== totalSteps);

        if (currentStep === totalSteps) renderSummary();
    }

    function validateStep() {
        if (currentStep === 1 && !serviceSel?.value) {
            alert('Lütfen bir hizmet seçin.');
            return false;
        }
        if (currentStep === 3) {
            if (!dateInput?.value) { alert('Lütfen tarih seçin.'); return false; }
            if (!startTimeInput?.value) { alert('Lütfen uygun bir saat seçin.'); return false; }
        }
        return true;
    }

    /* 4. Slot loader (AJAX)
     * ------------------------------------------------------- */
    async function loadSlots() {
        if (!serviceSel?.value || !dateInput?.value || !slotsContainer) return;
        slotsContainer.innerHTML = '<div class="text-muted small">Saatler yükleniyor...</div>';
        const params = new URLSearchParams({
            service_id: serviceSel.value,
            date: dateInput.value,
            staff_id: staffSel?.value || ''
        });
        try {
            const res = await fetch(`${APP_BASE}/api/slots?${params.toString()}`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            slotsContainer.innerHTML = '';
            if (!data.slots || data.slots.length === 0) {
                slotsContainer.innerHTML = '<p class="text-muted small mb-0">Bu tarihte uygun saat bulunamadı.</p>';
                return;
            }
            data.slots.forEach(slot => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'slot-btn';
                btn.textContent = slot.start;
                btn.dataset.time = slot.start;
                btn.addEventListener('click', () => {
                    slotsContainer.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    if (startTimeInput) startTimeInput.value = slot.start;
                });
                slotsContainer.appendChild(btn);
            });
        } catch (e) {
            slotsContainer.innerHTML = '<p class="text-danger small">Saatler alınamadı.</p>';
        }
    }

    /* 5. Live summary render
     * ------------------------------------------------------- */
    function renderSummary() {
        if (!summaryEl) return;
        const svcOpt = serviceSel?.selectedOptions[0];
        const staffOpt = staffSel?.selectedOptions[0];
        const pkgOpt = pkgSel?.selectedOptions[0];

        const rows = [
            { label: 'Hizmet',  value: svcOpt && svcOpt.value ? svcOpt.textContent.trim() : '—' },
            { label: 'Personel', value: staffOpt && staffOpt.value ? staffOpt.textContent.trim() : 'Farketmez' },
            { label: 'Tarih',    value: dateInput?.value || '—' },
            { label: 'Saat',     value: startTimeInput?.value || '—' }
        ];
        if (pkgOpt && pkgOpt.value) {
            rows.push({ label: 'Paket', value: pkgOpt.textContent.trim() });
        } else {
            const price = svcOpt?.dataset.price;
            if (price) rows.push({ label: 'Tutar', value: fmtMoney(parseFloat(price)) });
        }

        summaryEl.innerHTML = `
            <h6>Randevu Özeti</h6>
            ${rows.map(r => `
                <div class="row-line">
                    <span class="label">${r.label}</span>
                    <span class="value">${r.value}</span>
                </div>
            `).join('')}
            <p class="text-muted small mb-0 mt-3">
                Onayladığınızda hesabınızdan onaylı/bekleyen randevu olarak listelenecektir.
            </p>
        `;
    }

    /* 6. Submit handler
     * ------------------------------------------------------- */
    async function submitForm(e) {
        e.preventDefault();
        if (!validateStep()) return;
        const fd = new FormData(form);
        submitBtn.disabled = true;
        try {
            const res = await fetch(`${APP_BASE}/api/appointment`, {
                method: 'POST',
                body: fd,
                headers: { 'Accept': 'application/json', 'X-CSRF-Token': csrfToken }
            });
            const data = await res.json();
            if (!data.success) {
                alert(data.message || 'Randevu oluşturulamadı.');
                return;
            }
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                alert(data.message || 'Randevu oluşturuldu.');
                window.location.reload();
            }
        } catch {
            alert('Bağlantı hatası.');
        } finally {
            submitBtn.disabled = false;
        }
    }

    /* 7. Bootstrap on modal show
     * ------------------------------------------------------- */
    function attach() {
        if (!cache()) return;

        showStep(1);
        nextBtn?.addEventListener('click', () => {
            if (!validateStep()) return;
            showStep(currentStep + 1);
        });
        prevBtn?.addEventListener('click', () => showStep(currentStep - 1));
        form?.addEventListener('submit', submitForm);

        [serviceSel, staffSel, dateInput].forEach(el => {
            el?.addEventListener('change', loadSlots);
        });

        modalEl?.addEventListener('shown.bs.modal', () => showStep(1));
    }

    if (document.readyState !== 'loading') attach();
    else document.addEventListener('DOMContentLoaded', attach);
})();
