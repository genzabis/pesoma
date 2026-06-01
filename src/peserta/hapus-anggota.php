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
$teamId = (int) ($_POST['team_id'] ?? 0);

$reg = db_fetch(
    'SELECT r.id FROM registrations r
     JOIN competitions c ON c.id = r.competition_id
     WHERE r.id = ? AND r.user_id = ? AND c.jenis = "tim"',
    [$registrationId, current_user_id()]
);

$errors = [];
if (!$reg) {
    $errors[] = 'Pendaftaran tim tidak valid.';
}
if ($reg) {
    $regRow = db_fetch('SELECT created_at FROM registrations WHERE id = ?', [$registrationId]);
    if ($regRow && strtotime((string) $regRow['created_at']) > strtotime('2026-04-27 23:59:59')) {
        $errors[] = 'Deadline perubahan tim sudah lewat.';
    }
}
if ($teamId <= 0) {
    $errors[] = 'Anggota tidak valid.';
}

if ($errors !== []) {
    flash('error', implode(' ', $errors));
    redirect('tim-saya.php');
}

db_query('DELETE FROM teams WHERE id = ? AND registration_id = ?', [$teamId, $registrationId]);
log_activity(current_user_id(), ROLE_PESERTA, 'team_delete', 'Hapus anggota tim #' . $teamId . ' registration #' . $registrationId);
flash('success', 'Anggota tim dihapus.');
redirect('tim-saya.php');
