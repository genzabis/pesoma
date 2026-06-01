<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Token tidak valid.');
    admin_redirect('kelola-user.php');
}

$id     = (int) ($_POST['id'] ?? 0);
$action = $_POST['action'] ?? 'save';

// Reset password shortcut
if ($action === 'reset') {
    if ($id <= 0) {
        flash('error', 'User tidak ditemukan.');
        admin_redirect('kelola-user.php');
    }
    $pass = password_hash('Pesoma2026', PASSWORD_BCRYPT);
    db_query('UPDATE users SET password=?, updated_at=CURRENT_TIMESTAMP WHERE id=?', [$pass, $id]);
    log_activity(admin_id(), ROLE_ADMIN, 'reset_password', 'Reset password user #' . $id);
    flash('success', 'Password direset ke: Pesoma2026');
    admin_redirect('kelola-user.php');
}

$nama     = trim((string) ($_POST['nama'] ?? ''));
$email    = trim((string) ($_POST['email'] ?? ''));
$nim      = trim((string) ($_POST['nim'] ?? ''));
$fak      = in_array($_POST['fakultas'] ?? '', ALLOWED_FAKULTAS, true) ? $_POST['fakultas'] : null;
$role     = in_array($_POST['role'] ?? '', [ROLE_ADMIN, ROLE_PANITIA, ROLE_JURI, ROLE_PESERTA], true) ? $_POST['role'] : ROLE_PESERTA;
$active   = (int) ($_POST['is_active'] ?? 1);
$password = (string) ($_POST['password'] ?? '');

// Validasi dasar
$errors = [];
if ($nama === '') {
    $errors[] = 'Nama wajib diisi.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email tidak valid.';
}
if ($id === 0 && $password === '') {
    $errors[] = 'Password wajib diisi untuk user baru.';
}
if ($password !== '' && strlen($password) < 6) {
    $errors[] = 'Password minimal 6 karakter.';
}

// Cek unik email & NIM (exclude diri sendiri saat update)
if ($errors === []) {
    $emailDup = db_fetch('SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1', [$email, $id]);
    if ($emailDup) {
        $errors[] = 'Email sudah dipakai oleh user lain.';
    }
    if ($nim !== '') {
        $nimDup = db_fetch('SELECT id FROM users WHERE nim = ? AND id <> ? LIMIT 1', [$nim, $id]);
        if ($nimDup) {
            $errors[] = 'NIM sudah dipakai oleh user lain.';
        }
    }
}

if ($errors) {
    flash('error', implode(' ', $errors));
    admin_redirect('kelola-user.php' . ($id ? ('?id=' . $id) : ''));
}

try {
    if ($id) {
        db_query(
            'UPDATE users SET nama=?, nim=?, email=?, fakultas=?, role=?, is_active=?, updated_at=CURRENT_TIMESTAMP WHERE id=?',
            [$nama, $nim ?: null, $email, $fak, $role, $active, $id]
        );
        if ($password !== '') {
            db_query('UPDATE users SET password=? WHERE id=?', [password_hash($password, PASSWORD_BCRYPT), $id]);
        }
        log_activity(admin_id(), ROLE_ADMIN, 'update_user', 'Update user #' . $id);
        flash('success', 'User berhasil diperbarui.');
    } else {
        db_query(
            'INSERT INTO users (nama,nim,email,fakultas,role,password,is_active) VALUES (?,?,?,?,?,?,?)',
            [$nama, $nim ?: null, $email, $fak, $role, password_hash($password, PASSWORD_BCRYPT), $active]
        );
        log_activity(admin_id(), ROLE_ADMIN, 'create_user', 'Tambah user ' . $email);
        flash('success', 'User berhasil ditambahkan.');
    }
} catch (PDOException $e) {
    // Defensive: race condition kalau dua admin insert email/NIM yang sama bersamaan,
    // atau constraint lain yang dilanggar.
    if ($e->getCode() === '23000') {
        flash('error', 'Data bentrok dengan user lain (email atau NIM sudah dipakai).');
    } else {
        flash('error', 'Gagal menyimpan: ' . $e->getMessage());
    }
}

admin_redirect('kelola-user.php');
