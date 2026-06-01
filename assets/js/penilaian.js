/* ==========================================================================
   PESOMA 2026 - Bantuan form penilaian juri (penilaian.js)
   Menghitung total berbobot secara langsung saat juri mengisi nilai per aspek.
   - Tiap input nilai: <input data-nilai data-bobot="30" min="0" max="100">
   - Tampilan total: elemen dengan id="totalNilai".
   Total = Σ (nilai × bobot / 100), nilai dibatasi 0–100. Server tetap
   menghitung ulang sebagai sumber kebenaran.
   ========================================================================== */
(function () {
    'use strict';

    function clamp(value, min, max) {
        return Math.max(min, Math.min(max, value));
    }

    function recalc(inputs, output) {
        var total = 0;
        inputs.forEach(function (input) {
            var nilai = clamp(parseFloat(input.value) || 0, 0, 100);
            var bobot = parseFloat(input.getAttribute('data-bobot')) || 0;
            total += nilai * bobot / 100;

            var sub = input.parentNode.querySelector('[data-subtotal]');
            if (sub) sub.textContent = (nilai * bobot / 100).toFixed(2);
        });
        if (output) output.textContent = total.toFixed(2);
        return total;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var inputs = Array.prototype.slice.call(document.querySelectorAll('input[data-nilai]'));
        if (!inputs.length) return;

        var output = document.getElementById('totalNilai');

        inputs.forEach(function (input) {
            input.addEventListener('input', function () {
                var v = parseFloat(input.value);
                if (!isNaN(v)) {
                    if (v < 0) input.value = 0;
                    if (v > 100) input.value = 100;
                }
                recalc(inputs, output);
            });
        });

        recalc(inputs, output);
    });
})();
