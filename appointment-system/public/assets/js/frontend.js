/* ============================================================
 * RandevuTakip - Frontend JS
 * Sticky navbar, mobile CTA, smooth scroll, gallery lightbox,
 * scrollspy menu, lazy fade in.
 *
 * Bölümler:
 *   1. Bootstrap & utilities
 *   2. Sticky navbar
 *   3. Smooth scroll & scrollspy
 *   4. Mobile sticky CTA
 *   5. Gallery lightbox
 *   6. Scroll reveal
 * ============================================================ */

(function () {
    'use strict';

    const ready = (fn) => {
        if (document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    };

    /* 2. Sticky navbar
     * ------------------------------------------------------- */
    function initStickyNavbar() {
        const navbar = document.getElementById('mainNav') ||
                       document.querySelector('.site-navbar');
        if (!navbar) return;

        const update = () => {
            if (window.scrollY > 24) navbar.classList.add('scrolled');
            else navbar.classList.remove('scrolled');
        };
        update();
        window.addEventListener('scroll', update, { passive: true });
    }

    /* 3. Smooth scroll & scrollspy
     * ------------------------------------------------------- */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(link => {
            link.addEventListener('click', e => {
                const id = link.getAttribute('href');
                if (!id || id === '#' || id.length < 2) return;
                const target = document.querySelector(id);
                if (!target) return;
                e.preventDefault();
                const top = target.getBoundingClientRect().top + window.scrollY - 70;
                window.scrollTo({ top, behavior: 'smooth' });

                const navCollapse = document.querySelector('.navbar-collapse.show');
                if (navCollapse && window.bootstrap) {
                    bootstrap.Collapse.getInstance(navCollapse)?.hide();
                }
            });
        });
    }

    function initScrollSpy() {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.site-navbar .nav-link[href^="#"]');
        if (!sections.length || !navLinks.length) return;

        const setActive = (id) => {
            navLinks.forEach(l => {
                l.classList.toggle('active', l.getAttribute('href') === '#' + id);
            });
        };

        const onScroll = () => {
            let current = '';
            const y = window.scrollY + 120;
            sections.forEach(s => {
                if (s.offsetTop <= y) current = s.id;
            });
            if (current) setActive(current);
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    /* 4. Mobile sticky CTA
     * ------------------------------------------------------- */
    function initMobileCta() {
        const btn = document.getElementById('mobile-cta-book');
        if (!btn) return;
        btn.addEventListener('click', () => {
            const modal = document.getElementById('appointmentModal');
            if (!modal || !window.bootstrap) return;
            bootstrap.Modal.getOrCreateInstance(modal).show();
        });
    }

    /* 5. Gallery lightbox
     * ------------------------------------------------------- */
    function initGalleryLightbox() {
        const items = document.querySelectorAll('.gallery-item img');
        if (!items.length) return;

        let overlay = document.querySelector('.lightbox-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'lightbox-overlay';
            overlay.innerHTML = `
                <button type="button" class="lightbox-close" aria-label="Kapat">&times;</button>
                <img alt="">
            `;
            document.body.appendChild(overlay);
        }
        const imgEl = overlay.querySelector('img');
        const close = () => overlay.classList.remove('show');

        items.forEach(img => {
            img.addEventListener('click', () => {
                imgEl.src = img.src;
                imgEl.alt = img.alt || '';
                overlay.classList.add('show');
            });
        });

        overlay.addEventListener('click', e => {
            if (e.target === overlay || e.target.classList.contains('lightbox-close')) close();
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') close();
        });
    }

    /* 6. Scroll reveal
     * ------------------------------------------------------- */
    function initReveal() {
        const items = document.querySelectorAll('[data-reveal]');
        if (!items.length || !('IntersectionObserver' in window)) {
            items.forEach(el => el.classList.add('is-visible'));
            return;
        }
        const io = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: .15 });
        items.forEach(el => io.observe(el));
    }

    ready(() => {
        initStickyNavbar();
        initSmoothScroll();
        initScrollSpy();
        initMobileCta();
        initGalleryLightbox();
        initReveal();
    });
})();
