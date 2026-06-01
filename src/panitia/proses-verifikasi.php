<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

$redirectBack = 'verifikasi-peserta.php?competition_id=' . (int) ($_POST['filter_competition_id'] ?? 0)
    . '&status=' . urlencode((string) ($_POST['filter_status'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('verifikasi-peserta.php');
}

if (!verify_csrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Token keamanan tidak valid.');
    redirect('verifikasi-peserta.php');
}

$id = (int) ($_POST['registration_id'] ?? 0);
$action = $_POST['action'] ?? '';
$status = $action === 'terima' ? 'diterima' : ($action === 'tolak' ? 'ditolak' : '');

if ($id <= 0) {
    flash('error', 'Pendaftaran tidak valid.');
    redirect($redirectBack);
}

if (in_array($action, ['tm_hadir', 'tm_batal', 'final_hadir', 'final_batal'], true)) {
    $field = str_starts_with($action, 'tm_') ? 'tm' : 'final';
    $present = str_ends_with($action, '_hadir') ? 1 : 0;
    // Kolom dipilih dari whitelist tetap ('tm'/'final'), bukan input mentah.
    $column = $field === 'tm' ? 'tm' : 'final';
    db_query(
        "UPDATE registrations SET {$column}_attendance = ?, {$column}_checked_by = ?, {$column}_checked_at = NOW() WHERE id = ?",
        [$present, panitia_user_id(), $id]
    );
    log_activity(panitia_user_id(), ROLE_PANITIA, 'checkin_' . $column, ($present ? 'Hadir ' : 'Batal hadir ') . 'registration #' . $id);
    flash('success', 'Status check-in berhasil diperbarui.');
} elseif ($status !== '') {
    db_query(
        'UPDATE registrations SET status_verifikasi = ?, catatan_verifikasi = ?, verified_by = ?, verified_at = NOW() WHERE id = ?',
        [$status, trim((string) ($_POST['catatan'] ?? '')), panitia_user_id(), $id]
    );
    log_activity(panitia_user_id(), ROLE_PANITIA, 'verifikasi_peserta', strtoupper($status) . ' registration #' . $id);
    flash('success', 'Status pendaftaran berhasil diperbarui.');
} else {
    flash('error', 'Aksi tidak dikenali.');
}

redirect($redirectBack);
