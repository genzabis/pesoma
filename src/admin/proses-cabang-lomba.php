<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Token tidak valid.');
    admin_redirect('kelola-cabang-lomba.php');
}
$id = (int)($_POST['id'] ?? 0);
if (($_POST['action'] ?? '') === 'delete') {
    db_query('DELETE FROM competitions WHERE id=?', [$id]);
    log_activity(admin_id(), ROLE_ADMIN, 'delete_competition', 'Hapus cabang #' . $id);
    flash('success', 'Cabang lomba dihapus.');
    admin_redirect('kelola-cabang-lomba.php');
}
$data = [trim($_POST['kode_lomba']), trim($_POST['nama_lomba']), $_POST['jenis'], trim($_POST['kategori'] ?? ''), (int)$_POST['min_anggota'], trim($_POST['deskripsi'] ?? ''), trim($_POST['aturan'] ?? ''), (int)$_POST['max_anggota'], (int)$_POST['need_mentor'], (int)$_POST['need_mentor'], (int)$_POST['has_penyisihan'], (int)$_POST['has_final'], (int)$_POST['is_active']];
if ($id) {
    $data[] = $id;
    db_query('UPDATE competitions SET kode_lomba=?,nama_lomba=?,jenis=?,kategori=?,min_anggota=?,deskripsi=?,aturan=?,max_anggota=?,need_mentor=?,requires_mentor=?,has_penyisihan=?,has_final=?,is_active=?,updated_at=CURRENT_TIMESTAMP WHERE id=?', $data);
    log_activity(admin_id(), ROLE_ADMIN, 'update_competition', 'Update cabang #' . $id);
    flash('success', 'Cabang lomba diperbarui.');
} else {
    db_query('INSERT INTO competitions (kode_lomba,nama_lomba,jenis,kategori,min_anggota,deskripsi,aturan,max_anggota,need_mentor,requires_mentor,has_penyisihan,has_final,is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)', $data);
    log_activity(admin_id(), ROLE_ADMIN, 'create_competition', 'Tambah cabang ' . trim($_POST['nama_lomba']));
    flash('success', 'Cabang lomba ditambahkan.');
}
admin_redirect('kelola-cabang-lomba.php');
