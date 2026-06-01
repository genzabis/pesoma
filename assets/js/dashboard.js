/* ==========================================================================
   PESOMA 2026 - Util panel dashboard (dashboard.js)
   Konfirmasi aksi destruktif & auto-tutup alert. Aman tanpa elemen terkait.
   ========================================================================== */
(function () {
    'use strict';

    function initConfirm() {
        // Tombol/aksi dengan data-confirm akan meminta konfirmasi sebelum lanjut.
        document.querySelectorAll('[data-confirm]').forEach(function (el) {
            el.addEventListener('click', function (e) {
                var message = el.getAttribute('data-confirm') || 'Lanjutkan aksi ini?';
                if (!window.confirm(message)) {
                    e.preventDefault();
                }
            });
        });
    }

    function initAutoDismiss() {
        // Alert dengan data-autodismiss akan menghilang setelah beberapa detik.
        document.querySelectorAll('.alert[data-autodismiss]').forEach(function (alert) {
            var delay = parseInt(alert.getAttribute('data-autodismiss'), 10) || 5000;
            setTimeout(function () {
                alert.style.transition = 'opacity .4s ease';
                alert.style.opacity = '0';
                setTimeout(function () { alert.remove(); }, 400);
            }, delay);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initConfirm();
        initAutoDismiss();
    });
})();
