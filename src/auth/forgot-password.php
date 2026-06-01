<?php

declare(strict_types=1);


/**
 * PESOMA III 2026 - UIN Prof. K.H. Saifuddin Zuhri Purwokerto
 * Copyright (c) 2026 Tim Pengembang PESOMA III. All Rights Reserved.
 *
 * This file is part of a proprietary software project. Unauthorized
 * copying, redistribution, or use of this file, via any medium, is
 * strictly prohibited. See LICENSE for the full terms.
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/auth-layout.php';

if (is_logged_in()) {
    redirect(dashboard_url_by_role($_SESSION['user']['role']));
}

$email = '';
$errors = [];
$done = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));

    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Token keamanan tidak valid. Silakan coba lagi.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    if ($errors === []) {
        $user = db_fetch('SELECT id FROM users WHERE email = ? AND is_active = 1 LIMIT 1', [$email]);
        if ($user) {
            $token = bin2hex(random_bytes(32));
            db_query('INSERT INTO password_resets (email, token, created_at) VALUES (?, ?, NOW())', [$email, $token]);
            log_activity((int) $user['id'], null, 'password_reset_request', 'Permintaan reset password untuk ' . $email);
        }
        // Pesan generik agar tidak membocorkan apakah email terdaftar.
        $done = true;
    }
}

auth_layout_start('Lupa Sandi', [
    'heading' => 'Reset<br>kata sandi.',
]);
?>
<a class="auth-back" href="<?= e(APP_URL) ?>/src/auth/login.php">â† Kembali ke Login</a>

<h2>Lupa sandi.</h2>
<p class="lead">Masukkan email mahasiswa terdaftar. Kami kirimkan tautan untuk mengatur ulang kata sandi.</p>

<?php if ($done): ?>
    <div class="auth-alert success">
        <div>Jika email terdaftar, instruksi reset password telah dikirim. Silakan periksa kotak masuk Anda.</div>
    </div>
<?php endif; ?>

<?php if ($errors): ?>
    <div class="auth-alert error">
        <div>
            <?php foreach ($errors as $error): ?>
                <div><?= e($error) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php if (!$done): ?>
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <div class="form-group">
            <label for="email">Email Mahasiswa</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" placeholder="nim@mhs.uinsaizu.ac.id" required autofocus>
        </div>

        <button type="submit" class="btn primary">Kirim Tautan Reset</button>

        <p style="text-align: center; margin: 24px 0 0; font-size: 13.5px; color: var(--c-ink-soft);">
            Sudah ingat sandinya? <a href="<?= e(APP_URL) ?>/src/auth/login.php" style="color: var(--c-ink); font-weight: 600;">Masuk</a>
        </p>
    </form>
<?php else: ?>
    <a href="<?= e(APP_URL) ?>/src/auth/login.php" class="btn primary">â† Kembali ke Login</a>
<?php endif; ?>
<?php
auth_layout_end();
