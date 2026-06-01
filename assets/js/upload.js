/* ==========================================================================
   PESOMA 2026 - Bantuan upload karya (upload.js)
   Pratinjau nama file & validasi ukuran/ekstensi sebelum submit.
   Batas selaras konstanta server (UPLOAD_DOC_MAX_SIZE 50MB, video 100MB).
   Aktif pada <input type="file" data-upload>.
   ========================================================================== */
(function () {
    'use strict';

    var MAX_DOC = 50 * 1024 * 1024;   // 50 MB
    var MAX_VIDEO = 100 * 1024 * 1024; // 100 MB
    var ALLOWED = ['doc', 'docx', 'ppt', 'pptx', 'pdf', 'jpg', 'jpeg', 'png', 'mp4', 'zip'];

    function humanSize(bytes) {
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
        if (bytes >= 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return bytes + ' B';
    }

    function ensureFeedback(input) {
        var note = input.parentNode.querySelector('.upload-feedback');
        if (!note) {
            note = document.createElement('div');
            note.className = 'upload-feedback field-error';
            input.parentNode.appendChild(note);
        }
        return note;
    }

    function validate(input) {
        var note = ensureFeedback(input);
        if (!input.files || !input.files.length) {
            note.textContent = '';
            return true;
        }

        var file = input.files[0];
        var ext = (file.name.split('.').pop() || '').toLowerCase();
        var max = ext === 'mp4' ? MAX_VIDEO : MAX_DOC;

        if (ALLOWED.indexOf(ext) === -1) {
            note.textContent = 'Ekstensi .' + ext + ' tidak diizinkan.';
            input.value = '';
            return false;
        }
        if (file.size > max) {
            note.textContent = 'Ukuran ' + humanSize(file.size) + ' melebihi batas ' + humanSize(max) + '.';
            input.value = '';
            return false;
        }
        note.classList.remove('field-error');
        note.style.color = '#166534';
        note.textContent = file.name + ' (' + humanSize(file.size) + ') siap diunggah.';
        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var inputs = document.querySelectorAll('input[type="file"][data-upload]');
        inputs.forEach(function (input) {
            input.addEventListener('change', function () { validate(input); });
            var form = input.closest('form');
            if (form) {
                form.addEventListener('submit', function (e) {
                    if (!validate(input)) e.preventDefault();
                });
            }
        });
    });
})();
