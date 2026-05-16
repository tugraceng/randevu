/* ============================================================
 * RandevuTakip — Premium Booking Stepper
 *   01  Setup / Selectors
 *   02  Step Navigation
 *   03  Choice Tiles (Service / Staff)
 *   04  Date + Skeleton-loaded Slot Grid
 *   05  Live Summary Card
 *   06  Submit
 * ============================================================ */
(function () {
    'use strict';

    const ROOT = document.getElementById('appointmentModal');
    if (!ROOT) return;

    const $  = (sel) => ROOT.querySelector(sel);
    const $$ = (sel) => Array.from(ROOT.querySelectorAll(sel));

    /* --------------------------------------------------------
     * 01 State
     * -------------------------------------------------------- */
    const state = {
        step: 1,
        service: null,
        staff:   null,
        date:    '',
        time:    '',
        note:    '',
        package: null
    };

    const STEPS = $$('.stepper-progress .step');
    const PANELS = $$('.stepper-panel');
    const btnPrev = $('[data-step-prev]');
    const btnNext = $('[data-step-next]');
    const btnSubmit = $('[data-step-submit]');

    /* --------------------------------------------------------
     * 02 Step Navigation
     * -------------------------------------------------------- */
    function goStep(n) {
        const total = PANELS.length;
        if (n < 1) n = 1;
        if (n > total) n = total;
        state.step = n;

        STEPS.forEach((el, i) => {
            el.classList.remove('active', 'done');
            const idx = i + 1;
            if (idx < n) el.classList.add('done');
            if (idx === n) el.classList.add('active');
        });
        PANELS.forEach((el, i) => el.classList.toggle('active', i + 1 === n));

        if (btnPrev)   btnPrev.style.visibility = n === 1 ? 'hidden' : 'visible';
        if (btnNext)   btnNext.style.display = n === total ? 'none' : 'inline-flex';
        if (btnSubmit) btnSubmit.style.display = n === total ? 'inline-flex' : 'none';
    }

    btnPrev?.addEventListener('click', () => goStep(state.step - 1));
    btnNext?.addEventListener('click', () => {
        if (!validateStep(state.step)) return;
        goStep(state.step + 1);
        if (state.step === 3) loadSlots();
    });

    function validateStep(n) {
        const target = ROOT.querySelector(`.stepper-panel[data-step="${n}"]`);
        if (!target) return true;
        if (n === 1 && !state.service) { flash(target, 'Lütfen bir hizmet seçin.'); return false; }
        if (n === 2 && !state.staff)   { flash(target, 'Personel seçin veya farketmez seçin.'); return false; }
        if (n === 3 && !state.date)    { flash(target, 'Tarih seçin.'); return false; }
        if (n === 4 && !state.time)    { flash(target, 'Saat seçin.'); return false; }
        return true;
    }

    function flash(panel, msg) {
        let el = panel.querySelector('.step-error');
        if (!el) {
            el = document.createElement('div');
            el.className = 'alert alert-danger step-error mt-2';
            panel.appendChild(el);
        }
        el.textContent = msg;
        setTimeout(() => el.remove(), 2200);
    }

    /* --------------------------------------------------------
     * 03 Choice tiles — services / staff
     * -------------------------------------------------------- */
    function bindChoices(selector, stateKey) {
        $$(selector).forEach(el => {
            el.addEventListener('click', () => {
                $$(selector).forEach(x => x.classList.remove('selected'));
                el.classList.add('selected');
                const id   = el.dataset.id || '';
                const name = el.dataset.name || el.textContent.trim();
                state[stateKey] = { id, name, price: el.dataset.price || '', duration: el.dataset.duration || '' };
                updateSummary();
            });
        });
    }
    bindChoices('[data-choose-service]', 'service');
    bindChoices('[data-choose-staff]',   'staff');

    /* --------------------------------------------------------
     * 04 Date input + slot grid (with skeleton loading)
     * -------------------------------------------------------- */
    const dateInput = $('[data-step-date]');
    dateInput?.addEventListener('change', () => {
        state.date = dateInput.value;
        state.time = '';
        updateSummary();
        loadSlots();
    });

    function loadSlots() {
        const grid = $('[data-slot-grid]');
        if (!grid) return;
        if (!state.service?.id || !state.date) {
            grid.innerHTML = '<small class="text-muted">Önce hizmet ve tarih seçin.</small>';
            return;
        }
        grid.innerHTML = '';
        // skeletons
        for (let i = 0; i < 8; i++) {
            const sk = document.createElement('span');
            sk.className = 'slot-tile skeleton';
            sk.style.minHeight = '40px';
            grid.appendChild(sk);
        }

        const base = window.APP_BASE || '/';
        const url  = `${base}?route=ajax/slots&service_id=${state.service.id}&date=${state.date}&staff_id=${state.staff?.id || ''}`;
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(j => renderSlots(grid, j.slots || []))
            .catch(() => renderSlots(grid, []));
    }

    function renderSlots(grid, slots) {
        grid.innerHTML = '';
        if (!slots.length) {
            grid.innerHTML = '<small class="text-muted">Bu güne uygun saat bulunamadı.</small>';
            return;
        }
        slots.forEach(t => {
            const el = document.createElement('div');
            el.className = 'slot-tile';
            el.textContent = t;
            el.addEventListener('click', () => {
                grid.querySelectorAll('.slot-tile').forEach(x => x.classList.remove('selected'));
                el.classList.add('selected');
                state.time = t;
                updateSummary();
            });
            grid.appendChild(el);
        });
    }

    /* --------------------------------------------------------
     * 05 Live summary card
     * -------------------------------------------------------- */
    function updateSummary() {
        const set = (k, val, muted = false) => {
            const el = ROOT.querySelector(`[data-sum="${k}"]`);
            if (!el) return;
            el.textContent = val || '—';
            el.classList.toggle('muted', !val);
        };
        set('service',  state.service?.name);
        set('staff',    state.staff?.name);
        set('date',     state.date);
        set('time',     state.time);
        const note = ROOT.querySelector('[data-step-note]');
        state.note = note ? note.value : '';

        // hidden inputs to be submitted
        ROOT.querySelectorAll('[data-bind]').forEach(el => {
            const key = el.dataset.bind;
            if (key === 'service_id')        el.value = state.service?.id || '';
            else if (key === 'staff_id')     el.value = state.staff?.id || '';
            else if (key === 'appointment_date') el.value = state.date;
            else if (key === 'start_time')   el.value = state.time;
            else if (key === 'notes')        el.value = state.note;
        });

        const total = ROOT.querySelector('[data-sum-total]');
        if (total && state.service?.price) {
            total.textContent = state.service.price;
        }
    }
    ROOT.querySelector('[data-step-note]')?.addEventListener('input', updateSummary);

    /* --------------------------------------------------------
     * 06 Submit
     * -------------------------------------------------------- */
    btnSubmit?.addEventListener('click', () => {
        const form = ROOT.querySelector('form[data-appointment-form]');
        if (!form) return;
        if (!state.service?.id || !state.date || !state.time) {
            alert('Lütfen tüm adımları tamamlayın.');
            return;
        }
        btnSubmit.setAttribute('data-loading', 'true');
        form.submit();
    });

    // Init
    goStep(1);

    /* --------------------------------------------------------
     * 07 Booking gate — open the modal only if user is logged-in
     *     AND email-verified. Otherwise route through Auth drawer
     *     with a pending action that resumes the booking flow.
     * -------------------------------------------------------- */
    const modalEl = document.getElementById('appointmentModal');
    let bsModal = null;
    function openBookingModal() {
        if (!modalEl) return;
        if (!bsModal && window.bootstrap?.Modal) {
            bsModal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
        }
        bsModal?.show();
    }

    function startBookingFlow() {
        if (window.Auth) {
            window.Auth.require(openBookingModal, {
                requireVerified: true,
                label: 'Randevu oluşturmak için giriş yapın ve e-postanızı doğrulayın.'
            });
        } else {
            openBookingModal();
        }
    }

    // Bind all "Randevu Al" / book triggers on the page (including legacy
    // [data-bs-target="#appointmentModal"] buttons sprinkled across home.php).
    const bookTriggers = document.querySelectorAll(
        '[data-book-start], #mobile-cta-book, [data-bs-target="#appointmentModal"]'
    );
    bookTriggers.forEach(btn => {
        // remove Bootstrap auto-trigger to prevent double-open without auth gate
        btn.removeAttribute('data-bs-toggle');
        btn.removeAttribute('data-bs-target');
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            // Optionally pre-select service if data-service is present
            const preService = btn.dataset.service;
            const fireOpen = () => {
                openBookingModal();
                if (preService) {
                    setTimeout(() => {
                        const tile = ROOT.querySelector(`[data-choose-service][data-id="${preService}"]`);
                        tile?.click();
                    }, 200);
                }
            };
            if (window.Auth) {
                window.Auth.require(fireOpen, {
                    requireVerified: true,
                    label: 'Randevu oluşturmak için giriş yapın ve e-postanızı doğrulayın.'
                });
            } else {
                fireOpen();
            }
        });
    });

    // Expose for any custom integration
    window.startBookingFlow = startBookingFlow;
})();
