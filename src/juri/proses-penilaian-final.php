<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Token tidak valid.');
    redirect('penilaian-final.php');
}
$registrationId = (int)($_POST['registration_id'] ?? 0);
$reg = db_fetch('SELECT r.id,c.id competition_id FROM registrations r JOIN competitions c ON c.id=r.competition_id JOIN finalists f ON f.registration_id=r.id WHERE r.id=?', [$registrationId]);
if (!$reg) {
    flash('error', 'Finalis tidak ditemukan.');
    redirect('penilaian-final.php');
}
$aspects = juri_aspects((int)$reg['competition_id'], 'final');
$scores = $_POST['nilai'] ?? [];
$json = juri_score_json($aspects, (array)$scores);
$total = juri_total_from_post($aspects, (array)$scores);
$komentar = trim((string)($_POST['komentar'] ?? ''));
$juriId = juri_user_id();
db_query('INSERT INTO scores_final (registration_id,juri_id,nilai_per_aspek,total,komentar) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE nilai_per_aspek=VALUES(nilai_per_aspek), total=VALUES(total), komentar=VALUES(komentar), updated_at=CURRENT_TIMESTAMP', [$registrationId, $juriId, $json, $total, $komentar]);
log_activity($juriId, ROLE_JURI, 'nilai_final', 'Registration #' . $registrationId . ' total ' . $total);
flash('success', 'Nilai final berhasil disimpan.');
redirect('penilaian-final.php?registration_id=' . $registrationId);
