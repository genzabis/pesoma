<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('tentukan-finalis.php');
}

if (!verify_csrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Token keamanan tidak valid.');
    redirect('tentukan-finalis.php');
}

$competitionId = (int) ($_POST['competition_id'] ?? 0);
$selected = array_values(array_filter(array_map('intval', (array) ($_POST['finalists'] ?? [])), fn($v) => $v > 0));

if ($competitionId <= 0 || !$selected) {
    flash('error', 'Pilih cabang lomba dan minimal satu finalis.');
    redirect('tentukan-finalis.php?competition_id=' . $competitionId);
}

db()->beginTransaction();
try {
    db_query('DELETE FROM finalists WHERE competition_id = ?', [$competitionId]);
    foreach ($selected as $rank => $registrationId) {
        db_query(
            'INSERT INTO finalists (registration_id, competition_id, rank_penyisihan, published_by, announced_at) VALUES (?, ?, ?, ?, NOW())',
            [$registrationId, $competitionId, $rank + 1, panitia_user_id()]
        );
    }
    db_query(
        'INSERT INTO announcements (title, content, type, published_by, published_at) VALUES (?, ?, "finalis", ?, NOW())',
        ['Pengumuman Finalis PESOMA 2026', 'Finalis cabang lomba terpilih telah dipublikasikan. Silakan cek daftar finalis pada halaman pengumuman.', panitia_user_id()]
    );
    db()->commit();
    log_activity(panitia_user_id(), ROLE_PANITIA, 'publish_finalists', 'Publikasi finalis competition #' . $competitionId);
    flash('success', 'Finalis berhasil dipublikasikan.');
} catch (Throwable $e) {
    db()->rollBack();
    error_log('[PUBLISH_FINALISTS_ERROR] ' . $e->getMessage());
    flash('error', 'Gagal mempublikasikan finalis. Silakan coba lagi.');
}

redirect('tentukan-finalis.php?competition_id=' . $competitionId);
