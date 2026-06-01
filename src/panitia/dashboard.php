<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';

$totalPeserta = (int) (db_fetch('SELECT COUNT(*) total FROM registrations')['total'] ?? 0);
$totalKarya = (int) (db_fetch('SELECT COUNT(*) total FROM submissions WHERE status IN ("submitted","reviewed")')['total'] ?? 0);
$pending = (int) (db_fetch('SELECT COUNT(*) total FROM registrations WHERE status_verifikasi = "pending"')['total'] ?? 0);
$tmHadir = (int) (db_fetch('SELECT COUNT(*) total FROM registrations WHERE tm_attendance = 1')['total'] ?? 0);
$finalHadir = (int) (db_fetch('SELECT COUNT(*) total FROM registrations WHERE final_attendance = 1')['total'] ?? 0);
$statusPerCabang = db_fetch_all('SELECT c.nama_lomba, COUNT(r.id) total, SUM(r.status_verifikasi="pending") pending, SUM(r.status_verifikasi="diterima") diterima, SUM(r.status_verifikasi="ditolak") ditolak FROM competitions c LEFT JOIN registrations r ON r.competition_id=c.id GROUP BY c.id,c.nama_lomba ORDER BY c.nama_lomba');
$finalisPerCabang = db_fetch_all('SELECT c.nama_lomba, COUNT(f.id) total FROM competitions c LEFT JOIN finalists f ON f.competition_id = c.id GROUP BY c.id, c.nama_lomba ORDER BY c.nama_lomba');
$latest = db_fetch_all('SELECT r.nomor_peserta, r.created_at, r.status_verifikasi, u.nim, u.nama, c.nama_lomba FROM registrations r JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=r.competition_id ORDER BY r.created_at DESC LIMIT 8');

panitia_header('Dashboard Panitia', 'dashboard.php');
?>
<div class="grid">
    <section class="card span-4">
        <h3>Total Peserta</h3>
        <div class="stat"><?= $totalPeserta ?></div>
        <p class="muted">Semua pendaftaran lomba.</p>
    </section>
    <section class="card span-4">
        <h3>Karya Masuk</h3>
        <div class="stat"><?= $totalKarya ?></div>
        <p class="muted">Submission yang sudah diupload.</p>
    </section>
    <section class="card span-4">
        <h3>Menunggu Verifikasi</h3>
        <div class="stat"><?= $pending ?></div><a class="btn small" href="verifikasi-peserta.php?status=pending">Verifikasi sekarang</a>
    </section>
    <section class="card span-6">
        <h2>Ringkasan Status per Cabang</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Cabang</th>
                    <th>Total</th>
                    <th>Pending</th>
                    <th>Diterima</th>
                    <th>Ditolak</th>
                </tr>
            </thead>
            <tbody><?php foreach ($statusPerCabang as $row): ?><tr>
                        <td><?= e($row['nama_lomba']) ?></td>
                        <td><?= (int)$row['total'] ?></td>
                        <td><?= (int)$row['pending'] ?></td>
                        <td><?= (int)$row['diterima'] ?></td>
                        <td><?= (int)$row['ditolak'] ?></td>
                    </tr><?php endforeach; ?></tbody>
        </table>
    </section>
    <section class="card span-6">
        <h2>Finalis per Cabang</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Cabang</th>
                    <th>Total Finalis</th>
                </tr>
            </thead>
            <tbody><?php foreach ($finalisPerCabang as $row): ?><tr>
                        <td><?= e($row['nama_lomba']) ?></td>
                        <td><span class="badge ok"><?= (int) $row['total'] ?></span></td>
                    </tr><?php endforeach; ?></tbody>
        </table>
    </section>
    <section class="card span-6">
        <h2>Check-in Kegiatan</h2>
        <p><b>Technical Meeting:</b> <?= $tmHadir ?> peserta sudah hadir.</p>
        <p><b>Final:</b> <?= $finalHadir ?> peserta sudah hadir.</p>
        <a class="btn small secondary" href="verifikasi-peserta.php">Kelola check-in peserta</a>
    </section>
    <section class="card span-6">
        <h2>Pendaftar Terbaru</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>No Peserta</th>
                    <th>Nama</th>
                    <th>Cabang</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody><?php foreach ($latest as $row): ?><tr>
                        <td><?= e($row['nomor_peserta']) ?></td>
                        <td><?= e($row['nama']) ?><br><small class="muted"><?= e($row['nim']) ?></small></td>
                        <td><?= e($row['nama_lomba']) ?></td>
                        <td><?= panitia_badge($row['status_verifikasi']) ?></td>
                    </tr><?php endforeach; ?></tbody>
        </table>
    </section>
</div>
<?php panitia_footer(); ?>