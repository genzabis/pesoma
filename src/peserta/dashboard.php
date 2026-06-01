<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

$registrations = peserta_registrations();
$submissionCount = (int) (db_fetch(
    'SELECT COUNT(*) AS total FROM submissions s JOIN registrations r ON r.id = s.registration_id WHERE r.user_id = ?',
    [current_user_id()]
)['total'] ?? 0);
$finalistCount = (int) (db_fetch(
    'SELECT COUNT(*) AS total FROM finalists f JOIN registrations r ON r.id = f.registration_id WHERE r.user_id = ?',
    [current_user_id()]
)['total'] ?? 0);
$winnerCount = (int) (db_fetch(
    'SELECT COUNT(*) AS total FROM winners w JOIN registrations r ON r.id = w.registration_id WHERE r.user_id = ?',
    [current_user_id()]
)['total'] ?? 0);
$nearSchedules = db_fetch_all(
    "SELECT * FROM schedules WHERE is_public = 1 AND event_date IN ('2026-05-05','2026-06-10','2026-06-11') ORDER BY event_date ASC, event_time ASC"
);

peserta_header('Dashboard Peserta', 'dashboard.php');
?>
<div class="grid">
    <section class="card span-12">
        <h2>Selamat datang, <?= e($_SESSION['user']['nama'] ?? 'Peserta') ?></h2>
        <p class="muted">Pantau pendaftaran, upload karya, jadwal, dan pengumuman PESOMA 2026 dari dashboard ini.</p>
        <div class="actions"><a class="btn" href="daftar-lomba.php">Daftar Lomba</a><a class="btn secondary" href="upload-karya.php">Upload Karya</a></div>
    </section>

    <section class="card span-3">
        <div style="text-align: center; padding: 12px 0;">
            <div class="stat"><?= count($registrations) ?></div>
            <p class="muted" style="margin: 8px 0 0 0; font-size: 13px;">Pendaftaran Aktif</p>
        </div>
    </section>
    <section class="card span-3">
        <div style="text-align: center; padding: 12px 0;">
            <div class="stat"><?= $submissionCount ?></div>
            <p class="muted" style="margin: 8px 0 0 0; font-size: 13px;">Karya Terunggah</p>
        </div>
    </section>
    <section class="card span-3">
        <div style="text-align: center; padding: 12px 0;">
            <div class="stat" style="color: #22a56b;"><?= $finalistCount ?></div>
            <p class="muted" style="margin: 8px 0 0 0; font-size: 13px;">Lolos Finalis</p>
        </div>
    </section>
    <section class="card span-3">
        <div style="text-align: center; padding: 12px 0;">
            <div class="stat" style="color: #c99a2e;"><?= $winnerCount ?></div>
            <p class="muted" style="margin: 8px 0 0 0; font-size: 13px;">Juara</p>
        </div>
    </section>

    <section class="card span-6">
        <h3>Pendaftaran Lomba Aktif</h3>
        <?php if (!$registrations): ?>
            <p class="muted">Belum ada pendaftaran lomba.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Lomba</th>
                        <th>No Peserta</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registrations as $reg): ?><tr>
                            <td><?= e($reg['nama_lomba']) ?><br><small><?= e($reg['jenis']) ?></small></td>
                            <td><?= e($reg['nomor_peserta']) ?></td>
                            <td><?= badge_status($reg['status_verifikasi']) ?></td>
                        </tr><?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <section class="card span-6">
        <h3>Upload Karya</h3>
        <p>Total karya terunggah: <strong><?= $submissionCount ?></strong></p>
        <p>Batas waktu upload utama: <span class="deadline">18 Mei 2026, 23:59 WIB</span></p>
        <a class="btn" href="upload-karya.php">Kelola Upload</a>
    </section>

    <section class="card span-6">
        <h3>Pengumuman Saya</h3>
        <p>Finalis: <strong><?= $finalistCount ?></strong> | Juara: <strong><?= $winnerCount ?></strong></p>
        <p class="muted">Notifikasi lolos finalis atau juara akan tampil jika sudah dipublikasikan panitia.</p>
        <a class="btn secondary" href="pengumuman-saya.php">Lihat Pengumuman</a>
    </section>

    <section class="card span-6">
        <h3>Jadwal Terdekat</h3>
        <?php if (!$nearSchedules): ?>
            <p class="muted">Jadwal Technical Meeting dan Final belum tersedia.</p>
        <?php else: ?>
            <table class="table">
                <tbody><?php foreach ($nearSchedules as $schedule): ?><tr>
                            <td><strong><?= e($schedule['event_name']) ?></strong><br><span class="muted"><?= e(date('d M Y', strtotime($schedule['event_date']))) ?> <?= e(substr((string) $schedule['event_time'], 0, 5)) ?></span></td>
                            <td><?= e($schedule['location']) ?></td>
                        </tr><?php endforeach; ?></tbody>
            </table>
        <?php endif; ?>
    </section>
</div>
<?php peserta_footer(); ?>