<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('buat-pengumuman.php');
}

if (!verify_csrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Token keamanan tidak valid.');
    redirect('buat-pengumuman.php');
}

$id = (int) ($_POST['id'] ?? 0);

if (($_POST['action'] ?? '') === 'delete' && $id > 0) {
    db_query('DELETE FROM announcements WHERE id = ?', [$id]);
    log_activity(panitia_user_id(), ROLE_PANITIA, 'delete_announcement', 'Hapus pengumuman #' . $id);
    flash('success', 'Pengumuman dihapus.');
    redirect('buat-pengumuman.php');
}

$title = trim((string) ($_POST['title'] ?? ''));
$content = trim((string) ($_POST['content'] ?? ''));
$type = $_POST['type'] ?? 'umum';
$pub = isset($_POST['is_published']) ? 1 : 0;

if ($title === '' || $content === '' || !in_array($type, ['umum', 'finalis', 'winner'], true)) {
    flash('error', 'Data pengumuman tidak valid.');
    redirect('buat-pengumuman.php' . ($id > 0 ? '?edit=' . $id : ''));
}

if ($id > 0) {
    db_query(
        'UPDATE announcements SET title = ?, content = ?, type = ?, is_published = ?, published_by = ? WHERE id = ?',
        [$title, $content, $type, $pub, panitia_user_id(), $id]
    );
} else {
    db_query(
        'INSERT INTO announcements (title, content, type, is_published, published_by, published_at) VALUES (?, ?, ?, ?, ?, NOW())',
        [$title, $content, $type, $pub, panitia_user_id()]
    );
}

log_activity(panitia_user_id(), ROLE_PANITIA, 'save_announcement', $title);
flash('success', 'Pengumuman berhasil disimpan.');
redirect('buat-pengumuman.php');
