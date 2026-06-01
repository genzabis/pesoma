<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Token tidak valid.');
    redirect('penilaian-penyisihan.php');
}
$submissionId = (int)($_POST['submission_id'] ?? 0);
$sub = db_fetch('SELECT s.id,c.id competition_id FROM submissions s JOIN registrations r ON r.id=s.registration_id JOIN competitions c ON c.id=r.competition_id WHERE s.id=?', [$submissionId]);
if (!$sub) {
    flash('error', 'Karya tidak ditemukan.');
    redirect('penilaian-penyisihan.php');
}
$aspects = juri_aspects((int)$sub['competition_id'], 'penyisihan');
$scores = $_POST['nilai'] ?? [];
$json = juri_score_json($aspects, (array)$scores);
$total = juri_total_from_post($aspects, (array)$scores);
$komentar = trim((string)($_POST['komentar'] ?? ''));
$juriId = juri_user_id();
db_query('INSERT INTO scores_penyisihan (submission_id,juri_id,nilai_per_aspek,total,komentar) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE nilai_per_aspek=VALUES(nilai_per_aspek), total=VALUES(total), komentar=VALUES(komentar), updated_at=CURRENT_TIMESTAMP', [$submissionId, $juriId, $json, $total, $komentar]);
log_activity($juriId, ROLE_JURI, 'nilai_penyisihan', 'Submission #' . $submissionId . ' total ' . $total);
flash('success', 'Nilai penyisihan berhasil disimpan.');
redirect('penilaian-penyisihan.php?submission_id=' . $submissionId);
