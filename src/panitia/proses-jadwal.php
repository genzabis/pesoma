<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('kelola-jadwal.php');
}

if (!verify_csrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Token keamanan tidak valid.');
    redirect('kelola-jadwal.php');
}

$id = (int) ($_POST['id'] ?? 0);

if (($_POST['action'] ?? '') === 'delete' && $id > 0) {
    db_query('DELETE FROM schedules WHERE id = ?', [$id]);
    log_activity(panitia_user_id(), ROLE_PANITIA, 'delete_schedule', 'Hapus jadwal #' . $id);
    flash('success', 'Jadwal dihapus.');
    redirect('kelola-jadwal.php');
}

$data = [
    trim((string) ($_POST['event_name'] ?? '')),
    trim((string) ($_POST['event_date'] ?? '')),
    !empty($_POST['event_time']) ? $_POST['event_time'] : null,
    trim((string) ($_POST['location'] ?? '')),
    trim((string) ($_POST['link'] ?? '')),
    trim((string) ($_POST['description'] ?? '')),
    isset($_POST['is_public']) ? 1 : 0,
];

if ($data[0] === '' || $data[1] === '') {
    flash('error', 'Nama event dan tanggal wajib diisi.');
    redirect('kelola-jadwal.php' . ($id > 0 ? '?edit=' . $id : ''));
}

if ($id > 0) {
    db_query(
        'UPDATE schedules SET event_name = ?, event_date = ?, event_time = ?, location = ?, link = ?, description = ?, is_public = ? WHERE id = ?',
        [...$data, $id]
    );
    log_activity(panitia_user_id(), ROLE_PANITIA, 'update_schedule', 'Update jadwal #' . $id);
} else {
    db_query(
        'INSERT INTO schedules (event_name, event_date, event_time, location, link, description, is_public) VALUES (?, ?, ?, ?, ?, ?, ?)',
        $data
    );
    log_activity(panitia_user_id(), ROLE_PANITIA, 'create_schedule', 'Tambah jadwal');
}

flash('success', 'Jadwal berhasil disimpan.');
redirect('kelola-jadwal.php');
