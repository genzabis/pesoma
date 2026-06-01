/* ==========================================================================
   PESOMA 2026 - Skrip utama (main.js)
   Perilaku UI publik: toggle menu mobile, scroll progress, reveal-on-scroll,
   animasi penghitung statistik, dan tombol kembali ke atas.
   Aman dijalankan walau elemen terkait tidak ada di halaman.
   ========================================================================== */
(function () {
    'use strict';

    function initMenuToggle() {
        var toggle = document.getElementById('menuToggle');
        var menu = document.getElementById('mainMenu');
        if (!toggle || !menu) return;

        toggle.addEventListener('click', function () {
            var isOpen = menu.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        menu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                menu.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    function initScrollProgress() {
        var bar = document.getElementById('scrollProgress');
        if (!bar) return;

        function update() {
            var scrollTop = window.scrollY || document.documentElement.scrollTop;
            var height = document.documentElement.scrollHeight - window.innerHeight;
            var pct = height > 0 ? (scrollTop / height) * 100 : 0;
            bar.style.width = pct + '%';
        }

        window.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update);
        update();
    }

    function initReveal() {
        var items = document.querySelectorAll('.reveal');
        if (!items.length) return;

        if (!('IntersectionObserver' in window)) {
            items.forEach(function (el) { el.classList.add('is-visible'); });
            return;
        }

        var observer = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        items.forEach(function (el) { observer.observe(el); });
    }

    function animateCount(el) {
        var target = parseInt(el.getAttribute('data-count'), 10) || 0;
        var duration = 1200;
        var start = null;

        function step(timestamp) {
            if (start === null) start = timestamp;
            var progress = Math.min((timestamp - start) / duration, 1);
            el.textContent = Math.floor(progress * target).toString();
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = target.toString();
            }
        }
        requestAnimationFrame(step);
    }

    function initCounters() {
        var counters = document.querySelectorAll('[data-count]');
        if (!counters.length) return;

        if (!('IntersectionObserver' in window)) {
            counters.forEach(animateCount);
            return;
        }

        var observer = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCount(entry.target);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(function (el) { observer.observe(el); });
    }

    function initBackToTop() {
        var btn = document.getElementById('backToTop');
        if (!btn) return;
        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initMenuToggle();
        initScrollProgress();
        initReveal();
        initCounters();
        initBackToTop();
    });
})();
