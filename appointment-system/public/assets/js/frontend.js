/* ============================================================
 * RandevuTakip — Frontend Premium UX
 *   01  Sticky Navbar
 *   02  Smooth Scroll & Scroll Spy
 *   03  Scroll Reveal
 *   04  Animated Counters
 *   05  Floating WhatsApp / Mobile CTA
 *   06  Gallery Lightbox
 *   07  Live Booking Hero Strip
 * ============================================================ */

(function () {
    'use strict';

    const $  = (sel, ctx = document) => ctx.querySelector(sel);
    const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

    document.addEventListener('DOMContentLoaded', () => {
        initStickyNavbar();
        initSmoothScroll();
        initScrollSpy();
        initScrollReveal();
        initAnimatedCounters();
        initMobileCta();
        initLightbox();
        initLiveHeroStrip();
    });


    /* --------------------------------------------------------
     * 01 Sticky navbar — add `.scrolled` after a few pixels
     * -------------------------------------------------------- */
    function initStickyNavbar() {
        const nav = $('.site-nav');
        if (!nav) return;
        const onScroll = () => {
            nav.classList.toggle('scrolled', window.scrollY > 18);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }


    /* --------------------------------------------------------
     * 02 Smooth scroll for in-page anchors
     * -------------------------------------------------------- */
    function initSmoothScroll() {
        $$('a[href^="#"]').forEach(a => {
            const id = a.getAttribute('href');
            if (id.length < 2 || id === '#') return;
            a.addEventListener('click', (e) => {
                const target = document.querySelector(id);
                if (!target) return;
                e.preventDefault();
                const offset = 72;
                window.scrollTo({
                    top: target.getBoundingClientRect().top + window.scrollY - offset,
                    behavior: 'smooth'
                });
                const collapse = document.getElementById('mainNav');
                if (collapse && collapse.classList.contains('show')) {
                    bootstrap.Collapse.getInstance(collapse)?.hide();
                }
            });
        });
    }


    /* --------------------------------------------------------
     * 03 Scroll spy — highlight active nav-link as user scrolls
     * -------------------------------------------------------- */
    function initScrollSpy() {
        const links = $$('.site-nav .nav-link[href^="#"]');
        if (!links.length) return;
        const sections = links
            .map(l => ({ link: l, section: document.querySelector(l.getAttribute('href')) }))
            .filter(x => x.section);
        if (!sections.length) return;

        const update = () => {
            const y = window.scrollY + 120;
            let current = sections[0];
            sections.forEach(s => {
                if (s.section.offsetTop <= y) current = s;
            });
            sections.forEach(s => s.link.classList.toggle('active', s === current));
        };
        window.addEventListener('scroll', update, { passive: true });
        update();
    }


    /* --------------------------------------------------------
     * 04 Scroll-reveal animations for [data-reveal] elements
     * -------------------------------------------------------- */
    function initScrollReveal() {
        const items = $$('[data-reveal]');
        if (!items.length || !('IntersectionObserver' in window)) {
            items.forEach(el => el.classList.add('visible'));
            return;
        }
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.12 });
        items.forEach(el => io.observe(el));
    }


    /* --------------------------------------------------------
     * 05 Animated counters [data-counter="123"]
     * -------------------------------------------------------- */
    function initAnimatedCounters() {
        const counters = $$('[data-counter]');
        if (!counters.length) return;
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (!e.isIntersecting) return;
                const el     = e.target;
                const target = parseFloat(el.dataset.counter) || 0;
                const dur    = 1400;
                const start  = performance.now();
                const step = (now) => {
                    const p = Math.min((now - start) / dur, 1);
                    const ease = 1 - Math.pow(1 - p, 3);
                    el.textContent = formatNumber(target * ease);
                    if (p < 1) requestAnimationFrame(step);
                    else el.textContent = formatNumber(target);
                };
                requestAnimationFrame(step);
                io.unobserve(el);
            });
        }, { threshold: 0.3 });
        counters.forEach(el => io.observe(el));
    }
    function formatNumber(n) {
        if (n >= 1000) return Math.round(n).toLocaleString('tr-TR');
        if (n % 1 === 0) return Math.round(n).toString();
        return n.toFixed(1);
    }


    /* --------------------------------------------------------
     * 06 Mobile CTA bar — open appointment modal
     * -------------------------------------------------------- */
    function initMobileCta() {
        const btn = document.getElementById('mobile-cta-book');
        if (!btn) return;
        btn.addEventListener('click', () => {
            const modalEl = document.getElementById('appointmentModal');
            if (modalEl && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        });
    }


    /* --------------------------------------------------------
     * 07 Gallery lightbox
     * -------------------------------------------------------- */
    function initLightbox() {
        const items = $$('.gallery-item img');
        if (!items.length) return;

        const box = document.createElement('div');
        box.className = 'lightbox';
        box.innerHTML = `
            <button class="lightbox-close" aria-label="Kapat"><i class="bi bi-x-lg"></i></button>
            <img alt="">
        `;
        document.body.appendChild(box);

        const img = box.querySelector('img');
        items.forEach(el => {
            el.style.cursor = 'zoom-in';
            el.parentElement.addEventListener('click', () => {
                img.src = el.src;
                box.classList.add('active');
            });
        });
        const hide = () => box.classList.remove('active');
        box.querySelector('.lightbox-close').addEventListener('click', hide);
        box.addEventListener('click', (e) => { if (e.target === box) hide(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') hide(); });
    }


    /* --------------------------------------------------------
     * 08 Live hero strip — "Bugün X kişi randevu aldı"
     *    Rotates randomly between common counts every 4s.
     * -------------------------------------------------------- */
    function initLiveHeroStrip() {
        const el = $('[data-live-count]');
        if (!el) return;
        const base = parseInt(el.dataset.liveCount || '0', 10) || 23;
        let n = base;
        const tick = () => {
            n += Math.random() < 0.6 ? 1 : 0;
            el.textContent = n;
        };
        tick();
        setInterval(tick, 4500);
    }
})();
