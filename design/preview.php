<?php

declare(strict_types=1);

/**
 * design/preview.php
 *
 * Helper khusus design gallery — bypass login agar iframe di
 * design/index.html bisa langsung menampilkan dashboard.
 *
 * SAFETY: hanya jalan dari loopback (127.0.0.1 / ::1 / localhost).
 * Selain itu kembalikan 403. Jangan deploy ke publik.
 *
 * Pemakaian:
 *   /pesoma/design/preview.php?role=admin&page=dashboard.php
 *   /pesoma/design/preview.php?role=peserta&page=daftar-lomba.php
 */

require_once __DIR__ . '/../includes/auth.php';

$remote = $_SERVER['REMOTE_ADDR'] ?? '';
$allowedRemote = ['127.0.0.1', '::1', 'localhost'];
if (!in_array($remote, $allowedRemote, true)) {
    http_response_code(403);
    echo 'design/preview.php hanya dapat diakses dari localhost.';
    exit;
}

$role = (string) ($_GET['role'] ?? '');
$page = (string) ($_GET['page'] ?? 'dashboard.php');

$roleMap = [
    'admin'   => ROLE_ADMIN,
    'panitia' => ROLE_PANITIA,
    'juri'    => ROLE_JURI,
    'peserta' => ROLE_PESERTA,
];

if (!isset($roleMap[$role])) {
    http_response_code(400);
    echo 'Parameter role tidak valid. Pilihan: admin, panitia, juri, peserta.';
    exit;
}

// Sanitasi page: hanya nama file di folder role-nya, tidak boleh path traversal.
if (!preg_match('/^[a-zA-Z0-9_\-]+\.php$/', $page)) {
    http_response_code(400);
    echo 'Parameter page tidak valid.';
    exit;
}

// Cari user pertama dengan role yang cocok dari database
$user = db_fetch('SELECT * FROM users WHERE role = ? AND is_active = 1 ORDER BY id LIMIT 1', [$roleMap[$role]]);
if (!$user) {
    http_response_code(404);
    echo 'Tidak ada user aktif dengan role <b>' . htmlspecialchars($role) . '</b> di database. Buat dulu via Admin → Kelola User.';
    exit;
}

// Set session manual seperti yang dilakukan login.php
$_SESSION['user'] = [
    'id'       => (int) $user['id'],
    'nama'     => (string) $user['nama'],
    'email'    => (string) $user['email'],
    'role'     => (string) $user['role'],
    'fakultas' => $user['fakultas'] ?? null,
];
$_SESSION['last_activity'] = time();

// Redirect ke halaman target di folder role-nya.
$target = '../src/' . $role . '/' . $page;
header('Location: ' . $target);
exit;
