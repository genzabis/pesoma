<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('input-pemenang.php');
}

if (!verify_csrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Token keamanan tidak valid.');
    redirect('input-pemenang.php');
}

$competitionId = (int) ($_POST['competition_id'] ?? 0);
$ranks = [
    1 => (int) ($_POST['juara_1'] ?? 0),
    2 => (int) ($_POST['juara_2'] ?? 0),
    3 => (int) ($_POST['juara_3'] ?? 0),
];
$chosen = array_filter($ranks);

if ($competitionId <= 0 || count($chosen) !== count(array_unique($chosen))) {
    flash('error', 'Pilihan juara tidak valid atau duplikat.');
    redirect('input-pemenang.php?competition_id=' . $competitionId);
}

db()->beginTransaction();
try {
    db_query('DELETE FROM winners WHERE competition_id = ?', [$competitionId]);
    foreach ($ranks as $rank => $registrationId) {
        if ($registrationId <= 0) {
            continue;
        }
        $score = db_fetch('SELECT COALESCE(AVG(total), 0) total FROM scores_final WHERE registration_id = ?', [$registrationId]);
        db_query(
            'INSERT INTO winners (registration_id, competition_id, juara_ke, total_score, published_by, announced_at) VALUES (?, ?, ?, ?, ?, NOW())',
            [$registrationId, $competitionId, $rank, (float) ($score['total'] ?? 0), panitia_user_id()]
        );
    }
    db_query(
        'INSERT INTO announcements (title, content, type, published_by, published_at) VALUES (?, ?, "winner", ?, NOW())',
        ['Pengumuman Pemenang PESOMA 2026', 'Pemenang cabang lomba telah dipublikasikan pada halaman pengumuman.', panitia_user_id()]
    );
    db()->commit();
    log_activity(panitia_user_id(), ROLE_PANITIA, 'publish_winners', 'Publikasi pemenang competition #' . $competitionId);
    flash('success', 'Pemenang berhasil dipublikasikan.');
} catch (Throwable $e) {
    db()->rollBack();
    error_log('[PUBLISH_WINNERS_ERROR] ' . $e->getMessage());
    flash('error', 'Gagal mempublikasikan pemenang. Silakan coba lagi.');
}

redirect('input-pemenang.php?competition_id=' . $competitionId);
