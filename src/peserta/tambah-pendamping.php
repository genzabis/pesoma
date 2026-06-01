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
    'SELECT r.id FROM registrations r WHERE r.id = ? AND r.user_id = ?',
    [$registrationId, current_user_id()]
);

$mentorName = trim((string) ($_POST['mentor_name'] ?? ''));
$mentorNidn = trim((string) ($_POST['mentor_nidn'] ?? ''));
$mentorJabatan = trim((string) ($_POST['mentor_jabatan'] ?? ''));

$errors = [];
if (!$reg) {
    $errors[] = 'Pendaftaran tidak valid.';
}
if ($mentorName === '') {
    $errors[] = 'Nama pendamping wajib diisi.';
}

if ($errors !== []) {
    flash('error', implode(' ', $errors));
    redirect('tim-saya.php');
}

$existing = db_fetch('SELECT id FROM mentors WHERE registration_id = ? LIMIT 1', [$registrationId]);
if ($existing) {
    db_query(
        'UPDATE mentors SET nama_dosen = ?, nidn = ?, jabatan = ? WHERE id = ?',
        [$mentorName, $mentorNidn ?: null, $mentorJabatan ?: null, (int) $existing['id']]
    );
    flash('success', 'Pendamping diperbarui.');
} else {
    db_query(
        'INSERT INTO mentors (registration_id, nama_dosen, nidn, jabatan) VALUES (?, ?, ?, ?)',
        [$registrationId, $mentorName, $mentorNidn ?: null, $mentorJabatan ?: null]
    );
    flash('success', 'Pendamping ditambahkan.');
}
log_activity(current_user_id(), ROLE_PESERTA, 'mentor_save', 'Simpan pendamping registration #' . $registrationId);
redirect('tim-saya.php');
