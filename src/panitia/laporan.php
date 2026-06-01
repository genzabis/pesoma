<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';

$competitionId = (int)($_GET['competition_id'] ?? 0);
$competitions = panitia_competitions();
$params = [];
$where = '';
if ($competitionId > 0) {
    $where = ' WHERE r.competition_id = ?';
    $params[] = $competitionId;
}

$summary = db_fetch_all('SELECT c.nama_lomba, COUNT(r.id) total_peserta, SUM(r.status_verifikasi="pending") pending, SUM(r.status_verifikasi="diterima") diterima, SUM(r.status_verifikasi="ditolak") ditolak, COUNT(s.id) total_karya, COUNT(f.id) total_finalis, COUNT(w.id) total_pemenang FROM competitions c LEFT JOIN registrations r ON r.competition_id=c.id LEFT JOIN submissions s ON s.registration_id=r.id LEFT JOIN finalists f ON f.registration_id=r.id LEFT JOIN winners w ON w.registration_id=r.id GROUP BY c.id,c.nama_lomba ORDER BY c.nama_lomba');
$registrations = db_fetch_all('SELECT r.nomor_peserta, r.status_verifikasi, r.tm_attendance, r.created_at, u.nim, u.nama, u.email, u.fakultas, c.nama_lomba, c.jenis, s.uploaded_at, f.rank_penyisihan, w.juara_ke FROM registrations r JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=r.competition_id LEFT JOIN submissions s ON s.registration_id=r.id LEFT JOIN finalists f ON f.registration_id=r.id LEFT JOIN winners w ON w.registration_id=r.id' . $where . ' ORDER BY c.nama_lomba, u.nama', $params);

panitia_header('Laporan Panitia', 'laporan.php');
?>
<section class="card">
    <form class="filters" method="GET">
        <div class="field"><label>Filter Cabang</label><select name="competition_id">
                <option value="0">Semua cabang</option><?php foreach ($competitions as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $competitionId === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['nama_lomba']) ?></option><?php endforeach; ?>
            </select></div>
        <button class="btn" type="submit">Tampilkan</button>
        <a class="btn secondary" href="export-excel.php?type=peserta&competition_id=<?= $competitionId ?>">Export Peserta CSV</a>
        <a class="btn secondary" href="export-excel.php?type=karya&competition_id=<?= $competitionId ?>">Export Karya CSV</a>
        <a class="btn secondary" href="export-excel.php?type=nilai&competition_id=<?= $competitionId ?>">Export Nilai CSV</a>
    </form>
</section>
<section class="card">
    <h2>Ringkasan per Cabang</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Cabang</th>
                <th>Peserta</th>
                <th>Pending</th>
                <th>Diterima</th>
                <th>Ditolak</th>
                <th>Karya</th>
                <th>Finalis</th>
                <th>Pemenang</th>
            </tr>
        </thead>
        <tbody><?php foreach ($summary as $s): ?><tr>
                    <td><?= e($s['nama_lomba']) ?></td>
                    <td><?= (int)$s['total_peserta'] ?></td>
                    <td><?= (int)$s['pending'] ?></td>
                    <td><?= (int)$s['diterima'] ?></td>
                    <td><?= (int)$s['ditolak'] ?></td>
                    <td><?= (int)$s['total_karya'] ?></td>
                    <td><?= (int)$s['total_finalis'] ?></td>
                    <td><?= (int)$s['total_pemenang'] ?></td>
                </tr><?php endforeach; ?></tbody>
    </table>
</section>
<section class="card">
    <h2>Data Peserta</h2>
    <table class="table">
        <thead>
            <tr>
                <th>No Peserta</th>
                <th>Nama</th>
                <th>Cabang</th>
                <th>Status</th>
                <th>Upload</th>
                <th>Finalis/Juara</th>
            </tr>
        </thead>
        <tbody><?php foreach ($registrations as $r): ?><tr>
                    <td><?= e($r['nomor_peserta']) ?></td>
                    <td><?= e($r['nama']) ?><br><small class="muted"><?= e($r['nim'] . ' · ' . $r['email'] . ' · ' . $r['fakultas']) ?></small></td>
                    <td><?= e($r['nama_lomba']) ?><br><small class="muted"><?= e($r['jenis']) ?></small></td>
                    <td><?= panitia_badge($r['status_verifikasi']) ?></td>
                    <td><?= $r['uploaded_at'] ? e(date('d M Y H:i', strtotime($r['uploaded_at']))) : '<span class="badge no">Belum</span>' ?></td>
                    <td><?= $r['rank_penyisihan'] ? '<span class="badge ok">Finalis #' . (int)$r['rank_penyisihan'] . '</span>' : '-' ?> <?= $r['juara_ke'] ? '<span class="badge ok">Juara ' . (int)$r['juara_ke'] . '</span>' : '' ?></td>
                </tr><?php endforeach; ?></tbody>
    </table>
</section>
<?php panitia_footer(); ?>