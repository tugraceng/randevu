/* ============================================================
 * RandevuTakip — Auth Drawer (one-page auth experience)
 *
 *   Public API: window.Auth.open(pane?), window.Auth.close(),
 *               window.Auth.require(fn, opts)
 *               -> ensures the user is logged-in (and verified, if opts.requireVerified)
 *                  before invoking fn(); otherwise opens drawer with pending action.
 *
 *   Triggers in DOM:
 *     - [data-auth-open]            -> open drawer (with optional value: pane name)
 *     - [data-auth-close]           -> close drawer
 *     - [data-auth-tab]             -> switch tab (login | register)
 *     - [data-auth-tab-trigger]     -> switch tab from inside a pane
 *     - [data-auth-switch]          -> switch pane to a sub-pane (forgot, verify, account)
 *     - [data-auth-form]            -> AJAX submitted form (login / register / forgot / resend)
 *     - [data-auth-resume]          -> resume pending action
 *     - [data-auth-logout]          -> AJAX logout (link)
 *     - [data-pwd-toggle]           -> toggle password visibility
 *
 *   URL trigger: page loaded with ?auth=login (or register|forgot) opens drawer.
 *
 *   Toasts are reused from window.Admin.toast() if present;
 *   otherwise a lightweight inline toast is shown.
 * ============================================================ */
(function () {
    'use strict';

    const drawer = document.getElementById('authDrawer');
    if (!drawer) return;

    const $   = (sel, root = drawer) => root.querySelector(sel);
    const $$  = (sel, root = drawer) => Array.from(root.querySelectorAll(sel));

    const state = {
        ...(window.AUTH_STATE || { loggedIn: false, verified: false, user: {}, urls: {} }),
        pending: null,            // { fn, requireVerified, label }
        currentPane: 'login'
    };
    window.AUTH_STATE = state;

    /* ----------------------------------------------------------
     * Toast helper (falls back to inline notice)
     * ---------------------------------------------------------- */
    function toast(msg, type = 'info', timeout = 3800) {
        if (window.Admin && typeof window.Admin.toast === 'function') {
            window.Admin.toast(msg, type, timeout);
            return;
        }
        let host = document.getElementById('authToastHost');
        if (!host) {
            host = document.createElement('div');
            host.id = 'authToastHost';
            Object.assign(host.style, {
                position: 'fixed', top: '20px', right: '20px',
                zIndex: 2000, display: 'flex', flexDirection: 'column',
                gap: '.5rem', maxWidth: '320px'
            });
            document.body.appendChild(host);
        }
        const colors = {
            success: ['#10b981', '#ecfdf5'],
            error:   ['#ef4444', '#fef2f2'],
            warning: ['#f59e0b', '#fffbeb'],
            info:    ['#4f46e5', '#eef2ff']
        }[type] || ['#4f46e5', '#eef2ff'];
        const el = document.createElement('div');
        Object.assign(el.style, {
            background: colors[1],
            color: colors[0],
            border: `1px solid ${colors[0]}33`,
            borderRadius: '12px',
            padding: '.75rem 1rem',
            fontSize: '.88rem',
            fontWeight: '500',
            boxShadow: '0 12px 30px rgba(15,23,42,.12)',
            transform: 'translateX(40px)',
            opacity: '0',
            transition: 'all .25s ease'
        });
        el.textContent = msg;
        host.appendChild(el);
        requestAnimationFrame(() => {
            el.style.transform = 'translateX(0)';
            el.style.opacity = '1';
        });
        setTimeout(() => {
            el.style.transform = 'translateX(40px)';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 250);
        }, timeout);
    }

    /* ----------------------------------------------------------
     * Pane switching
     * ---------------------------------------------------------- */
    function showPane(name) {
        const validPanes = ['login', 'register', 'forgot', 'verify', 'account'];
        if (!validPanes.includes(name)) name = 'login';
        state.currentPane = name;
        $$('.auth-pane').forEach(p => p.classList.toggle('active', p.dataset.authPane === name));

        // Tabs are visible only for login/register; sub-panes still highlight the closest tab
        const tabName = (name === 'register') ? 'register' : 'login';
        $$('.auth-tab').forEach(t => t.classList.toggle('active', t.dataset.authTab === tabName));

        // focus first input
        setTimeout(() => {
            const focusable = $(`.auth-pane.active input:not([type="hidden"]):not([disabled])`);
            focusable?.focus({ preventScroll: true });
        }, 220);
    }

    /* ----------------------------------------------------------
     * Open / close
     * ---------------------------------------------------------- */
    function open(pane) {
        // Decide initial pane based on current auth state
        if (!pane) {
            if (!state.loggedIn) pane = 'login';
            else if (!state.verified) pane = 'verify';
            else pane = 'account';
        }
        refreshUserSlots();
        showPane(pane);
        drawer.setAttribute('aria-hidden', 'false');
        document.documentElement.style.overflow = 'hidden';
        document.dispatchEvent(new CustomEvent('auth:opened', { detail: { pane } }));
    }

    function close() {
        drawer.setAttribute('aria-hidden', 'true');
        document.documentElement.style.overflow = '';
        // Clear pending banner after small delay
        setTimeout(() => hidePending(), 380);
        document.dispatchEvent(new CustomEvent('auth:closed'));
    }

    /* ----------------------------------------------------------
     * Pending action banner
     * ---------------------------------------------------------- */
    function showPending(text) {
        const el = $('[data-auth-pending]');
        const txt = $('[data-auth-pending-text]');
        if (!el || !txt) return;
        txt.textContent = text;
        el.hidden = false;
    }
    function hidePending() {
        const el = $('[data-auth-pending]');
        if (el) el.hidden = true;
    }

    /* ----------------------------------------------------------
     * User slot refresh
     * ---------------------------------------------------------- */
    function refreshUserSlots() {
        $$('[data-auth-user-email]').forEach(el => el.textContent = state.user.email || '');
        $$('[data-auth-user-name]').forEach(el => el.textContent = state.user.first_name || state.user.email || '');
    }

    /* ----------------------------------------------------------
     * Pending action queue — used by Auth.require()
     * ---------------------------------------------------------- */
    function require(fn, opts = {}) {
        const requireVerified = opts.requireVerified !== false;
        const label = opts.label || 'Devam etmek için giriş yapın.';
        if (state.loggedIn && (state.verified || !requireVerified)) {
            fn();
            return;
        }
        state.pending = { fn, requireVerified, label };
        if (!state.loggedIn) {
            open('login');
        } else if (requireVerified && !state.verified) {
            open('verify');
        }
        showPending(label);
    }
    function runPending() {
        if (!state.pending) return false;
        const { fn, requireVerified } = state.pending;
        if (!state.loggedIn || (requireVerified && !state.verified)) return false;
        const cb = fn;
        state.pending = null;
        close();
        setTimeout(cb, 220);
        return true;
    }

    /* ----------------------------------------------------------
     * AJAX form submit
     * ---------------------------------------------------------- */
    async function submitForm(form) {
        const kind = form.dataset.authForm;
        const btn  = form.querySelector('button[type="submit"]');
        if (btn) btn.setAttribute('data-loading', 'true');

        const fd = new FormData(form);
        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd,
                credentials: 'same-origin'
            });
            let data = {};
            try { data = await res.json(); } catch { data = { success: res.ok, message: 'İşlem tamamlandı.' }; }

            if (!data.success) {
                toast(data.message || 'Bir hata oluştu.', 'error');
                return;
            }

            // ----- success branches -----
            if (kind === 'login' || kind === 'register') {
                state.loggedIn = true;
                state.verified = !!data.verified;
                state.user = data.user || state.user;
                refreshUserSlots();
                toast(data.message || (kind === 'register' ? 'Kayıt başarılı.' : 'Giriş başarılı.'), 'success');

                if (kind === 'register' || !state.verified) {
                    // newly registered users (or unverified) → verify pane
                    showPane('verify');
                    return;
                }
                // verified → try to resume pending action, else show account
                if (!runPending()) {
                    showPane('account');
                }
                return;
            }

            if (kind === 'forgot') {
                toast(data.message || 'Bağlantı gönderildi.', 'success');
                form.reset();
                showPane('login');
                return;
            }

            if (kind === 'resend') {
                toast(data.message || 'Doğrulama linki yeniden gönderildi.', 'success');
                return;
            }

            toast(data.message || 'Tamamlandı.', 'success');

        } catch (err) {
            toast('Bağlantı hatası. Tekrar deneyin.', 'error');
        } finally {
            if (btn) btn.removeAttribute('data-loading');
        }
    }

    /* ----------------------------------------------------------
     * Event bindings
     * ---------------------------------------------------------- */
    document.addEventListener('click', (e) => {
        const t = e.target.closest('[data-auth-open]');
        if (t) {
            e.preventDefault();
            open(t.getAttribute('data-auth-open') || undefined);
            return;
        }
        if (e.target.closest('[data-auth-close]')) {
            e.preventDefault();
            close();
            return;
        }
        const tab = e.target.closest('[data-auth-tab]');
        if (tab) {
            e.preventDefault();
            showPane(tab.dataset.authTab);
            return;
        }
        const tabTrig = e.target.closest('[data-auth-tab-trigger]');
        if (tabTrig) {
            e.preventDefault();
            showPane(tabTrig.dataset.authTabTrigger);
            return;
        }
        const sw = e.target.closest('[data-auth-switch]');
        if (sw) {
            e.preventDefault();
            showPane(sw.dataset.authSwitch);
            return;
        }
        if (e.target.closest('[data-auth-resume]')) {
            e.preventDefault();
            if (!runPending()) close();
            return;
        }
        const tog = e.target.closest('[data-pwd-toggle]');
        if (tog) {
            e.preventDefault();
            const input = tog.parentElement?.querySelector('input');
            if (input) {
                input.type = input.type === 'password' ? 'text' : 'password';
                const icon = tog.querySelector('i');
                if (icon) icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
            }
            return;
        }
        const logoutLink = e.target.closest('[data-auth-logout]');
        if (logoutLink) {
            e.preventDefault();
            fetch(state.urls.logout || logoutLink.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin'
            }).then(() => {
                state.loggedIn = false;
                state.verified = false;
                state.user = {};
                toast('Çıkış yapıldı.', 'info');
                showPane('login');
            });
            return;
        }
    });

    // ESC closes
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drawer.getAttribute('aria-hidden') === 'false') {
            close();
        }
    });

    // form submit (delegated)
    drawer.addEventListener('submit', (e) => {
        const form = e.target.closest('[data-auth-form]');
        if (!form) return;
        e.preventDefault();
        submitForm(form);
    });

    /* ----------------------------------------------------------
     * URL trigger (?auth=login)
     * ---------------------------------------------------------- */
    const params = new URLSearchParams(window.location.search);
    if (params.has('auth')) {
        const target = params.get('auth') || 'login';
        setTimeout(() => open(target), 200);
        // clean the URL
        params.delete('auth');
        const cleaned = window.location.pathname + (params.toString() ? '?' + params.toString() : '') + window.location.hash;
        window.history.replaceState({}, '', cleaned);
    }

    /* ----------------------------------------------------------
     * Public API
     * ---------------------------------------------------------- */
    window.Auth = {
        open,
        close,
        require,
        state
    };
})();
