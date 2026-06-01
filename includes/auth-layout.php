<?php

declare(strict_types=1);

/**
 * Auth split-layout helper — minimal editorial.
 * Renders graphic side (left, cream) + form side (right, white).
 * Data berlebihan dari pemanggil (highlights, dsb) sengaja diabaikan.
 *
 * Usage:
 *   auth_layout_start($title, ['heading' => '...', 'desc' => '...']);
 *   // form fields
 *   auth_layout_end();
 */
function auth_layout_start(string $title, array $graphic = []): void
{
    $heading = $graphic['heading'] ?? 'Unjuk Aksi,<br>Raih Prestasi.';
    $desc    = $graphic['desc']    ?? null; // optional one-liner; default disembunyikan biar bersih
?>
    <!DOCTYPE html>
    <html lang="id" class="scroll-smooth">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="theme-color" content="#0c1733">
        <title><?= e($title) ?> — <?= e(APP_NAME) ?></title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="<?= e(APP_URL) ?>/assets/css/pesoma-public.css?v=5">
    </head>

    <body class="auth-body">
        <div class="auth-split">
            <aside class="auth-graphic">
                <a class="auth-brand" href="<?= e(APP_URL) ?>/pages/beranda.php">PESOMA III</a>
                <div class="auth-graphic-content">
                    <h2 class="auth-graphic-title"><?= $heading /* allow <br> */ ?></h2>
                    <?php if ($desc): ?>
                        <p class="auth-graphic-desc"><?= e($desc) ?></p>
                    <?php endif; ?>
                </div>
                <div class="auth-graphic-footer">
                    <span>UIN Prof. K.H. Saifuddin Zuhri Purwokerto · 2026</span>
                </div>
            </aside>
            <section class="auth-form-side">
                <div class="auth-form-card">
<?php
}

function auth_layout_end(): void
{
?>
                </div>
            </section>
        </div>

        <script>
            function pesomaTogglePassword(inputId, btn) {
                const input = document.getElementById(inputId);
                if (!input) return;
                const icon = btn.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    if (icon) { icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); }
                } else {
                    input.type = 'password';
                    if (icon) { icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
                }
            }
        </script>
    </body>

    </html>
<?php
}
