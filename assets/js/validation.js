/* ==========================================================================
   PESOMA 2026 - Validasi form sisi-klien (validation.js)
   Validasi ringan & progresif. Server tetap menjadi sumber kebenaran;
   ini hanya meningkatkan UX (cegah submit jelas-jelas tidak valid).
   Aktif pada <form data-validate>.
   ========================================================================== */
(function () {
    'use strict';

    function setError(field, message) {
        field.classList.add('invalid');
        var holder = field.closest('.field') || field.parentNode;
        var note = holder.querySelector('.field-error');
        if (!note) {
            note = document.createElement('div');
            note.className = 'field-error';
            holder.appendChild(note);
        }
        note.textContent = message;
    }

    function clearError(field) {
        field.classList.remove('invalid');
        var holder = field.closest('.field') || field.parentNode;
        var note = holder ? holder.querySelector('.field-error') : null;
        if (note) note.textContent = '';
    }

    function validateField(field) {
        var value = (field.value || '').trim();

        if (field.hasAttribute('required') && value === '') {
            setError(field, 'Wajib diisi.');
            return false;
        }
        if (field.type === 'email' && value !== '') {
            var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRe.test(value)) {
                setError(field, 'Format email tidak valid.');
                return false;
            }
        }
        if (field.type === 'number' && value !== '') {
            var num = parseFloat(value);
            var min = field.getAttribute('min');
            var max = field.getAttribute('max');
            if (min !== null && num < parseFloat(min)) {
                setError(field, 'Minimal ' + min + '.');
                return false;
            }
            if (max !== null && num > parseFloat(max)) {
                setError(field, 'Maksimal ' + max + '.');
                return false;
            }
        }
        clearError(field);
        return true;
    }

    function initForm(form) {
        var fields = form.querySelectorAll('input, select, textarea');

        fields.forEach(function (field) {
            field.addEventListener('blur', function () { validateField(field); });
            field.addEventListener('input', function () {
                if (field.classList.contains('invalid')) validateField(field);
            });
        });

        form.addEventListener('submit', function (e) {
            var ok = true;
            fields.forEach(function (field) {
                if (!validateField(field)) ok = false;
            });
            if (!ok) {
                e.preventDefault();
                var firstInvalid = form.querySelector('.invalid');
                if (firstInvalid) firstInvalid.focus();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[data-validate]').forEach(initForm);
    });
})();
