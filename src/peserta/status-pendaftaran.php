<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

$rows = db_fetch_all(
    'SELECT r.*, c.nama_lomba, c.jenis, c.kategori,
            f.id AS finalist_id, w.juara_ke,
            sp.total AS nilai_penyisihan, sf.total AS nilai_final
     FROM registrations r
     JOIN competitions c ON c.id = r.competition_id
     LEFT JOIN finalists f ON f.registration_id = r.id
     LEFT JOIN winners w ON w.registration_id = r.id
     LEFT JOIN submissions sub ON sub.registration_id = r.id
     LEFT JOIN scores_penyisihan sp ON sp.submission_id = sub.id
     LEFT JOIN scores_final sf ON sf.registration_id = r.id
     WHERE r.user_id = ?
     ORDER BY r.created_at DESC',
    [current_user_id()]
);

peserta_header('Status Pendaftaran', 'status-pendaftaran.php');
?>
<section class="card">
    <h2>Status Verifikasi, Kelolosan, dan Nilai</h2>
    <?php if (!$rows): ?><p class="muted">Belum ada pendaftaran.</p><?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Lomba</th>
                    <th>Status Verifikasi</th>
                    <th>Kelolosan</th>
                    <th>Nilai</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?><tr>
                        <td><?= e($row['nama_lomba']) ?><br><small><?= e($row['nomor_peserta']) ?></small></td>
                        <td><?= badge_status($row['status_verifikasi']) ?></td>
                        <td><?= $row['finalist_id'] ? '<span class="badge ok">Finalis</span>' : '<span class="badge no">Belum/Tidak Finalis</span>' ?> <?= $row['juara_ke'] ? '<span class="badge ok">Juara ' . (int) $row['juara_ke'] . '</span>' : '' ?></td>
                        <td>Penyisihan: <?= e($row['nilai_penyisihan'] ?? '-') ?><br>Final: <?= e($row['nilai_final'] ?? '-') ?></td>
                        <td><?= e($row['catatan_verifikasi'] ?? '-') ?></td>
                    </tr><?php endforeach; ?>
            </tbody>
        </table><?php endif; ?>
</section>
<?php peserta_footer(); ?>