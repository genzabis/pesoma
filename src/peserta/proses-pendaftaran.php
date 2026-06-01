<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('form-pendaftaran.php');
}

if (!verify_csrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Token keamanan tidak valid.');
    redirect('form-pendaftaran.php');
}

$selectedId = (int) ($_POST['competition_id'] ?? 0);
$competition = $selectedId
    ? db_fetch('SELECT * FROM competitions WHERE id = ? AND is_active = 1', [$selectedId])
    : null;

$errors = [];

if (!$competition) {
    $errors[] = 'Cabang lomba tidak valid.';
}

if ($competition) {
    $existing = db_fetch(
        'SELECT id FROM registrations WHERE user_id = ? AND competition_id = ?',
        [current_user_id(), $selectedId]
    );
    if ($existing) {
        $errors[] = 'Anda sudah terdaftar pada cabang lomba ini.';
    }
}

$teamNames = $_POST['team_nama'] ?? [];
$teamNims = $_POST['team_nim'] ?? [];
$teamFakultas = $_POST['team_fakultas'] ?? [];
$teamRoles = $_POST['team_peran'] ?? [];
$teamRows = [];

if ($competition && $competition['jenis'] === 'tim') {
    for ($i = 0; $i < count($teamNames); $i++) {
        $name = trim((string) ($teamNames[$i] ?? ''));
        $nim = trim((string) ($teamNims[$i] ?? ''));
        $fakultas = trim((string) ($teamFakultas[$i] ?? ''));
        $peran = trim((string) ($teamRoles[$i] ?? 'Anggota'));
        if ($name === '' && $nim === '') {
            continue;
        }
        if ($name === '' || $nim === '' || !in_array($fakultas, ALLOWED_FAKULTAS, true)) {
            $errors[] = 'Data anggota tim wajib lengkap dan fakultas valid.';
            break;
        }
        $teamRows[] = compact('name', 'nim', 'fakultas', 'peran');
    }
    if (count($teamRows) > (int) $competition['max_anggota']) {
        $errors[] = 'Jumlah anggota melebihi batas maksimal lomba.';
    }
}

$mentorName = trim((string) ($_POST['mentor_name'] ?? ''));
$mentorNidn = trim((string) ($_POST['mentor_nidn'] ?? ''));
if ($competition && (int) $competition['requires_mentor'] === 1 && $mentorName === '') {
    $errors[] = 'Nama pendamping wajib diisi untuk lomba ini.';
}

if ($errors !== []) {
    flash('error', implode(' ', $errors));
    redirect('form-pendaftaran.php?competition_id=' . $selectedId);
}

db()->beginTransaction();
try {
    $nomor = sprintf('PESOMA-2026-%d-%d', $selectedId, current_user_id());
    db_query(
        'INSERT INTO registrations (nomor_peserta, user_id, competition_id) VALUES (?, ?, ?)',
        [$nomor, current_user_id(), $selectedId]
    );
    $registrationId = (int) db_last_insert_id();
    foreach ($teamRows as $row) {
        db_query(
            'INSERT INTO teams (registration_id, nama_anggota, nim_anggota, fakultas, peran) VALUES (?, ?, ?, ?, ?)',
            [$registrationId, $row['name'], $row['nim'], $row['fakultas'], $row['peran']]
        );
    }
    if ($mentorName !== '') {
        db_query(
            'INSERT INTO mentors (registration_id, nama_dosen, nidn) VALUES (?, ?, ?)',
            [$registrationId, $mentorName, $mentorNidn]
        );
    }
    db()->commit();
    log_activity(current_user_id(), ROLE_PESERTA, 'registration_create', 'Mendaftar lomba ' . $competition['nama_lomba']);
    flash('success', 'Pendaftaran berhasil disimpan. Nomor peserta: ' . $nomor);
    redirect('dashboard.php');
} catch (Throwable $e) {
    db()->rollBack();
    error_log('[REGISTRATION_ERROR] ' . $e->getMessage());
    flash('error', 'Gagal menyimpan pendaftaran. Silakan coba lagi.');
    redirect('form-pendaftaran.php?competition_id=' . $selectedId);
}
