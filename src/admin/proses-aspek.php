<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Token tidak valid.');
    admin_redirect('kelola-aspek.php');
}
$id = (int)($_POST['id'] ?? 0);
$competitionId = (int)$_POST['competition_id'];
$babak = $_POST['babak'] === 'final' ? 'final' : 'penyisihan';
if (($_POST['action'] ?? '') === 'delete') {
    db_query('DELETE FROM aspek_penilaian WHERE id=?', [$id]);
    flash('success', 'Aspek dihapus.');
    admin_redirect('kelola-aspek.php?competition_id=' . $competitionId . '&babak=' . $babak);
}
$name = trim($_POST['aspek_name']);
$bobot = (float)$_POST['bobot_persen'];
$urutan = (int)($_POST['urutan'] ?? 1);
$current = (float)(db_fetch('SELECT COALESCE(SUM(bobot_persen),0) total FROM aspek_penilaian WHERE competition_id=? AND babak=? AND id<>?', [$competitionId, $babak, $id])['total'] ?? 0);
if ($current + $bobot > 100.01) {
    flash('error', 'Total bobot per babak tidak boleh melebihi 100%.');
    admin_redirect('kelola-aspek.php?competition_id=' . $competitionId . '&babak=' . $babak . ($id ? '&edit=' . $id : ''));
}
if ($id) {
    db_query('UPDATE aspek_penilaian SET aspek_name=?,bobot_persen=?,urutan=?,updated_at=CURRENT_TIMESTAMP WHERE id=?', [$name, $bobot, $urutan, $id]);
    flash('success', 'Aspek diperbarui.');
} else {
    db_query('INSERT INTO aspek_penilaian (competition_id,babak,aspek_name,bobot_persen,urutan) VALUES (?,?,?,?,?)', [$competitionId, $babak, $name, $bobot, $urutan]);
    flash('success', 'Aspek ditambahkan.');
}
log_activity(admin_id(), ROLE_ADMIN, 'save_aspek', 'Simpan aspek ' . $babak . ' cabang #' . $competitionId);
admin_redirect('kelola-aspek.php?competition_id=' . $competitionId . '&babak=' . $babak);
