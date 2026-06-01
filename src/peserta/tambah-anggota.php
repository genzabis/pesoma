<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('tim-saya.php');
}

if (!verify_csrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Token keamanan tidak valid.');
    redirect('tim-saya.php');
}

$registrationId = (int) ($_POST['registration_id'] ?? 0);
$reg = db_fetch(
    'SELECT r.*, c.max_anggota FROM registrations r
     JOIN competitions c ON c.id = r.competition_id
     WHERE r.id = ? AND r.user_id = ? AND c.jenis = "tim"',
    [$registrationId, current_user_id()]
);

$errors = [];
if (!$reg) {
    $errors[] = 'Pendaftaran tim tidak valid.';
}
if ($reg && strtotime((string) $reg['created_at']) > strtotime('2026-04-27 23:59:59')) {
    $errors[] = 'Deadline perubahan tim sudah lewat.';
}

$nama = trim((string) ($_POST['nama_anggota'] ?? ''));
$nim = trim((string) ($_POST['nim_anggota'] ?? ''));
$fakultas = trim((string) ($_POST['fakultas'] ?? ''));
$peran = trim((string) ($_POST['peran'] ?? 'Anggota')) ?: 'Anggota';

if ($reg && $errors === []) {
    $count = (int) (db_fetch('SELECT COUNT(*) AS total FROM teams WHERE registration_id = ?', [$registrationId])['total'] ?? 0);
    if ($count >= (int) $reg['max_anggota']) {
        $errors[] = 'Anggota tim sudah mencapai batas maksimal.';
    }
    if ($nama === '' || $nim === '' || !in_array($fakultas, ALLOWED_FAKULTAS, true)) {
        $errors[] = 'Data anggota wajib lengkap.';
    }
}

if ($errors !== []) {
    flash('error', implode(' ', $errors));
    redirect('tim-saya.php');
}

try {
    db_query(
        'INSERT INTO teams (registration_id, nama_anggota, nim_anggota, fakultas, peran) VALUES (?, ?, ?, ?, ?)',
        [$registrationId, $nama, $nim, $fakultas, $peran]
    );
    log_activity(current_user_id(), ROLE_PESERTA, 'team_add', 'Tambah anggota tim registration #' . $registrationId);
    flash('success', 'Anggota tim ditambahkan.');
} catch (Throwable $e) {
    error_log('[TEAM_ADD_ERROR] ' . $e->getMessage());
    flash('error', 'Gagal menambahkan anggota. NIM mungkin sudah terdaftar pada tim ini.');
}
redirect('tim-saya.php');
