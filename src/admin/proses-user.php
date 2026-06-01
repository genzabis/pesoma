<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Token tidak valid.');
    admin_redirect('kelola-user.php');
}
$id = (int)($_POST['id'] ?? 0);
$action = $_POST['action'] ?? 'save';
if ($action === 'reset') {
    $pass = password_hash('Pesoma2026', PASSWORD_BCRYPT);
    db_query('UPDATE users SET password=?, updated_at=CURRENT_TIMESTAMP WHERE id=?', [$pass, $id]);
    log_activity(admin_id(), ROLE_ADMIN, 'reset_password', 'Reset password user #' . $id);
    flash('success', 'Password direset ke: Pesoma2026');
    admin_redirect('kelola-user.php');
}
$nama = trim((string)$_POST['nama']);
$email = trim((string)$_POST['email']);
$nim = trim((string)($_POST['nim'] ?? ''));
$fak = in_array($_POST['fakultas'] ?? '', ALLOWED_FAKULTAS, true) ? $_POST['fakultas'] : null;
$role = in_array($_POST['role'] ?? '', [ROLE_ADMIN, ROLE_PANITIA, ROLE_JURI, ROLE_PESERTA], true) ? $_POST['role'] : ROLE_PESERTA;
$active = (int)($_POST['is_active'] ?? 1);
$password = (string)($_POST['password'] ?? '');
if ($id) {
    $params = [$nama, $nim ?: null, $email, $fak, $role, $active, $id];
    db_query('UPDATE users SET nama=?, nim=?, email=?, fakultas=?, role=?, is_active=?, updated_at=CURRENT_TIMESTAMP WHERE id=?', $params);
    if ($password !== '') {
        db_query('UPDATE users SET password=? WHERE id=?', [password_hash($password, PASSWORD_BCRYPT), $id]);
    }
    log_activity(admin_id(), ROLE_ADMIN, 'update_user', 'Update user #' . $id);
    flash('success', 'User berhasil diperbarui.');
} else {
    db_query('INSERT INTO users (nama,nim,email,fakultas,role,password,is_active) VALUES (?,?,?,?,?,?,?)', [$nama, $nim ?: null, $email, $fak, $role, password_hash($password, PASSWORD_BCRYPT), $active]);
    log_activity(admin_id(), ROLE_ADMIN, 'create_user', 'Tambah user ' . $email);
    flash('success', 'User berhasil ditambahkan.');
}
admin_redirect('kelola-user.php');
