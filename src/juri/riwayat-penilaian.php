<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
$juriId = juri_user_id();
$penyisihan = db_fetch_all('SELECT sp.id score_id,sp.submission_id,sp.total,sp.komentar,sp.updated_at,c.nama_lomba,r.nomor_peserta,u.nama,u.nim FROM scores_penyisihan sp JOIN submissions s ON s.id=sp.submission_id JOIN registrations r ON r.id=s.registration_id JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=r.competition_id WHERE sp.juri_id=? ORDER BY sp.updated_at DESC,sp.created_at DESC', [$juriId]);
$final = db_fetch_all('SELECT sf.id score_id,sf.registration_id,sf.total,sf.komentar,sf.updated_at,c.nama_lomba,r.nomor_peserta,u.nama,u.nim FROM scores_final sf JOIN registrations r ON r.id=sf.registration_id JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=r.competition_id WHERE sf.juri_id=? ORDER BY sf.updated_at DESC,sf.created_at DESC', [$juriId]);
juri_header('Riwayat Penilaian', 'riwayat-penilaian.php');
?>
<div class="grid">
    <section class="card span-12">
        <h2 style="margin-top:0">Riwayat Penyisihan</h2>
        <?php if (!$penyisihan): ?>
            <div style="text-align:center;padding:40px;color:var(--text-secondary)">
                <i class="fas fa-inbox" style="font-size:48px;margin-bottom:12px;opacity:.3"></i>
                <p>Belum ada riwayat penilaian penyisihan</p>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>No Peserta</th>
                        <th>Nama</th>
                        <th>Cabang</th>
                        <th style="text-align:right">Total Skor</th>
                        <th>Catatan</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($penyisihan as $r): ?>
                        <tr>
                            <td><strong><?= e($r['nomor_peserta']) ?></strong></td>
                            <td>
                                <?= e($r['nama']) ?><br>
                                <small class="muted"><?= e($r['nim']) ?></small>
                            </td>
                            <td><?= e($r['nama_lomba']) ?></td>
                            <td style="text-align:right"><strong><?= e((string)$r['total']) ?></strong></td>
                            <td><small><?= e($r['komentar'] ?: '-') ?></small></td>
                            <td>
                                <a class="btn small secondary" href="penilaian-penyisihan.php?submission_id=<?= (int)$r['submission_id'] ?>" style="width:100%">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <section class="card span-12">
        <h2 style="margin-top:0">Riwayat Final</h2>
        <?php if (!$final): ?>
            <div style="text-align:center;padding:40px;color:var(--text-secondary)">
                <i class="fas fa-inbox" style="font-size:48px;margin-bottom:12px;opacity:.3"></i>
                <p>Belum ada riwayat penilaian final</p>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>No Peserta</th>
                        <th>Nama</th>
                        <th>Cabang</th>
                        <th style="text-align:right">Total Skor</th>
                        <th>Catatan</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($final as $r): ?>
                        <tr>
                            <td><strong><?= e($r['nomor_peserta']) ?></strong></td>
                            <td>
                                <?= e($r['nama']) ?><br>
                                <small class="muted"><?= e($r['nim']) ?></small>
                            </td>
                            <td><?= e($r['nama_lomba']) ?></td>
                            <td style="text-align:right"><strong><?= e((string)$r['total']) ?></strong></td>
                            <td><small><?= e($r['komentar'] ?: '-') ?></small></td>
                            <td>
                                <a class="btn small secondary" href="penilaian-final.php?registration_id=<?= (int)$r['registration_id'] ?>" style="width:100%">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</div>
<?php juri_footer(); ?>
