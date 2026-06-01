<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/auth-layout.php';

if (is_logged_in()) {
    redirect(dashboard_url_by_role($_SESSION['user']['role']));
}

$identifier = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Token keamanan tidak valid. Silakan coba lagi.';
    }
    if ($identifier === '') {
        $errors[] = 'Email atau NIM wajib diisi.';
    }
    if ($password === '') {
        $errors[] = 'Password wajib diisi.';
    }

    if ($errors === []) {
        $user = find_user_by_identifier($identifier);

        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = 'Email/NIM atau password tidak sesuai.';
            log_activity($user['id'] ?? null, $user['role'] ?? null, 'login_failed', 'Percobaan login gagal untuk identifier: ' . $identifier);
        } else {
            login_user($user);
            db_query('UPDATE users SET last_login_at = NOW() WHERE id = ?', [$user['id']]);
            log_activity((int) $user['id'], $user['role'], 'login_success', 'User berhasil login.');
            redirect(dashboard_url_by_role($user['role']));
        }
    }
}

auth_layout_start('Login', [
    'heading' => 'Unjuk aksi,<br>raih prestasi.',
]);
?>
<a class="auth-back" href="<?= e(APP_URL) ?>/pages/beranda.php">← Beranda</a>

<h2>Masuk akun.</h2>
<p class="lead">Masukkan email atau NIM dan kata sandi portal Anda.</p>

<?php if ($errors): ?>
    <div class="auth-alert error">
        <div>
            <?php foreach ($errors as $error): ?>
                <div><?= e($error) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <div class="form-group">
        <label for="identifier">Email atau NIM</label>
        <input type="text" id="identifier" name="identifier" value="<?= e($identifier) ?>" placeholder="nim@mhs.uinsaizu.ac.id" required autofocus>
    </div>

    <div class="form-group">
        <label for="password" style="display: flex; justify-content: space-between; align-items: center;">
            <span>Kata Sandi</span>
            <a href="<?= e(APP_URL) ?>/src/auth/forgot-password.php" style="font-size: 12px; font-weight: 500; color: var(--c-ink-mute); text-decoration: none;">Lupa sandi?</a>
        </label>
        <div class="input-icon-wrapper">
            <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" required style="padding-left: 14px; padding-right: 40px;">
            <button type="button" class="input-suffix-icon" onclick="pesomaTogglePassword('password', this)" aria-label="Tampilkan/sembunyikan password"><i class="fa-regular fa-eye"></i></button>
        </div>
    </div>

    <button type="submit" class="btn primary">Masuk</button>

    <p style="text-align: center; margin: 24px 0 0; font-size: 13.5px; color: var(--c-ink-soft);">
        Belum punya akun? <a href="<?= e(APP_URL) ?>/src/auth/register.php" style="color: var(--c-ink); font-weight: 600;">Daftar Sekarang</a>
    </p>
</form>
<?php
auth_layout_end();
