<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';

$juriId = juri_user_id();
$belumPenyisihan = (int)(db_fetch('SELECT COUNT(*) total FROM submissions s JOIN registrations r ON r.id=s.registration_id WHERE r.status_verifikasi="diterima" AND NOT EXISTS (SELECT 1 FROM scores_penyisihan sp WHERE sp.submission_id=s.id AND sp.juri_id=?)', [$juriId])['total'] ?? 0);
$sudahPenyisihan = (int)(db_fetch('SELECT COUNT(*) total FROM scores_penyisihan WHERE juri_id=?', [$juriId])['total'] ?? 0);
$belumFinal = (int)(db_fetch('SELECT COUNT(*) total FROM finalists f WHERE NOT EXISTS (SELECT 1 FROM scores_final sf WHERE sf.registration_id=f.registration_id AND sf.juri_id=?)', [$juriId])['total'] ?? 0);
$sudahFinal = (int)(db_fetch('SELECT COUNT(*) total FROM scores_final WHERE juri_id=?', [$juriId])['total'] ?? 0);
$latest = db_fetch_all('SELECT "Penyisihan" babak, c.nama_lomba, r.nomor_peserta, u.nama, sp.total, sp.updated_at waktu FROM scores_penyisihan sp JOIN submissions s ON s.id=sp.submission_id JOIN registrations r ON r.id=s.registration_id JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=r.competition_id WHERE sp.juri_id=? UNION ALL SELECT "Final" babak, c.nama_lomba, r.nomor_peserta, u.nama, sf.total, sf.updated_at waktu FROM scores_final sf JOIN registrations r ON r.id=sf.registration_id JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=r.competition_id WHERE sf.juri_id=? ORDER BY waktu DESC LIMIT 8', [$juriId, $juriId]);

juri_header('Dashboard Juri', 'dashboard.php');
?>
<div class="grid">
    <section class="card span-3">
        <h3 style="margin-top:0;margin-bottom:12px">Belum Dinilai Penyisihan</h3>
        <div style="font-size:32px;font-weight:900;color:var(--primary);margin-bottom:12px"><?= $belumPenyisihan ?></div>
        <a class="btn small" href="penilaian-penyisihan.php" style="width:100%">Nilai Sekarang</a>
    </section>
    <section class="card span-3">
        <h3 style="margin-top:0;margin-bottom:12px">Sudah Dinilai Penyisihan</h3>
        <div style="font-size:32px;font-weight:900;color:var(--primary);margin-bottom:12px"><?= $sudahPenyisihan ?></div>
        <a class="btn small secondary" href="riwayat-penilaian.php" style="width:100%">Lihat Riwayat</a>
    </section>
    <section class="card span-3">
        <h3 style="margin-top:0;margin-bottom:12px">Belum Dinilai Final</h3>
        <div style="font-size:32px;font-weight:900;color:var(--primary);margin-bottom:12px"><?= $belumFinal ?></div>
        <a class="btn small" href="penilaian-final.php" style="width:100%">Nilai Final</a>
    </section>
    <section class="card span-3">
        <h3 style="margin-top:0;margin-bottom:12px">Sudah Dinilai Final</h3>
        <div style="font-size:32px;font-weight:900;color:var(--primary);margin-bottom:12px"><?= $sudahFinal ?></div>
        <a class="btn small secondary" href="riwayat-penilaian.php" style="width:100%">Lihat Riwayat</a>
    </section>
    <section class="card span-12">
        <h2 style="margin-top:0">Riwayat Penilaian Terbaru</h2>
        <?php if (!$latest): ?>
            <div style="text-align:center;padding:40px;color:var(--text-secondary)">
                <i class="fas fa-inbox" style="font-size:48px;margin-bottom:12px;opacity:.3"></i>
                <p>Belum ada riwayat penilaian</p>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Babak</th>
                        <th>No Peserta</th>
                        <th>Nama</th>
                        <th>Cabang</th>
                        <th style="text-align:right">Total Skor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latest as $row): ?>
                        <tr>
                            <td><span class="badge info"><?= e($row['babak']) ?></span></td>
                            <td><strong><?= e($row['nomor_peserta']) ?></strong></td>
                            <td><?= e($row['nama']) ?></td>
                            <td><?= e($row['nama_lomba']) ?></td>
                            <td style="text-align:right"><strong><?= e((string)$row['total']) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</div>
<?php juri_footer(); ?>
