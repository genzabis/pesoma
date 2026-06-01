<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Token tidak valid.');
    admin_redirect('kelola-user.php');
}
$id = (int)($_POST['id'] ?? 0);
if ($id === admin_id()) {
    flash('error', 'Tidak bisa menghapus akun sendiri.');
    admin_redirect('kelola-user.php');
}
db_query('DELETE FROM users WHERE id=?', [$id]);
log_activity(admin_id(), ROLE_ADMIN, 'delete_user', 'Hapus user #' . $id);
flash('success', 'User berhasil dihapus.');
admin_redirect('kelola-user.php');
