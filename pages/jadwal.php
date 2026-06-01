<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/footer.php';

$schedules = db_fetch_all('SELECT event_name, event_date, event_time, location, link, description FROM schedules WHERE is_public = 1 ORDER BY event_date ASC, event_time ASC');

public_header('Jadwal', 'jadwal.php');
?>
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-content">
            <span class="eyebrow">Agenda Resmi</span>
            <h1>Jadwal Kegiatan PESOMA 2026</h1>
            <p>Rangkaian agenda resmi PESOMA 2026 yang dipublikasikan panitia untuk semua peserta.</p>
            <div class="actions"><a class="btn" href="<?= e(APP_URL) ?>/src/auth/register.php">Daftar Sekarang</a><a class="btn secondary" href="pengumuman.php">Lihat Pengumuman</a></div>
            <div class="hero-note"><span>✓ <?= count($schedules) ?> agenda publik</span><span>✓ Update terpusat</span><span>✓ Tautan kegiatan</span></div>
        </div>
        <div class="hero-panel" aria-label="Highlight jadwal">
            <div class="hero-panel-card"><span class="hero-panel-label">Agenda</span><strong>Pantau tanggal penting</strong><span>Ikuti jadwal pembukaan, technical meeting, perlombaan, sampai pengumuman.</span></div>
            <div class="hero-panel-card"><span class="hero-panel-label">Informasi</span><strong>Lokasi dan tautan tersedia</strong><span>Setiap agenda menampilkan waktu, lokasi, dan link jika disediakan panitia.</span></div>
        </div>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="page-highlight">
            <div class="stat"><strong><?= count($schedules) ?></strong><span>Agenda Publik</span></div>
            <div class="stat"><strong>WIB</strong><span>Zona Waktu Resmi</span></div>
            <div class="stat"><strong>Live</strong><span>Pembaruan Jadwal</span></div>
        </div>
        <div class="section-head">
            <div class="section-tag">Agenda</div>
            <h2 class="section-title">Pantau Jadwal Kegiatan</h2>
            <p class="section-desc">Berikut adalah jadwal lengkap kegiatan PESOMA 2026 mulai dari pembukaan hingga pengumuman pemenang.</p>
        </div>
        <?php if (!$schedules): ?>
            <div class="empty-state">Jadwal publik belum tersedia.</div>
        <?php else: ?>
            <div class="card">
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Kegiatan</th>
                                <th>Lokasi</th>
                            </tr>
                        </thead>
                        <tbody><?php foreach ($schedules as $s): ?><tr>
                                    <td><?= e(date('d M Y', strtotime((string) $s['event_date']))) ?></td>
                                    <td><?= e($s['event_time'] ? substr((string) $s['event_time'], 0, 5) . ' WIB' : '-') ?></td>
                                    <td><?= e($s['event_name']) ?><?php if (!empty($s['description'])): ?><br><small class="muted"><?= e($s['description']) ?></small><?php endif; ?><?php if (!empty($s['link'])): ?><br><a href="<?= e($s['link']) ?>" target="_blank" rel="noopener">Tautan</a><?php endif; ?></td>
                                    <td><?= e($s['location'] ?: '-') ?></td>
                                </tr><?php endforeach; ?></tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php public_footer(); ?>