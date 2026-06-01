<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';

$totalLomba       = (int) (db_fetch('SELECT COUNT(*) AS total FROM competitions WHERE is_active = 1')['total'] ?? 14);
$pesertaTerdaftar = (int) (db_fetch('SELECT COUNT(*) AS total FROM users WHERE role = ?', [ROLE_PESERTA])['total'] ?? 0);
$finalisTerpilih  = (int) (db_fetch('SELECT COUNT(*) AS total FROM finalists')['total'] ?? 0);
$totalFakultas    = count(ALLOWED_FAKULTAS);

$competitions  = db_fetch_all('SELECT id, nama_lomba, jenis, deskripsi FROM competitions WHERE is_active = 1 ORDER BY id ASC LIMIT 6');
$schedules     = db_fetch_all('SELECT event_name, event_date, event_time, location FROM schedules WHERE is_public = 1 ORDER BY event_date ASC, event_time ASC LIMIT 5');
$announcements = db_fetch_all('SELECT id, title, type, published_at FROM announcements WHERE is_published = 1 ORDER BY published_at DESC LIMIT 3');

public_header('Beranda', 'beranda.php');
?>

<!-- Hero -->
<section class="hero">
    <div class="container">
        <div class="hero-inner">
            <div class="hero-content">
                <div class="hero-eyebrow">
                    <span class="dot"></span>
                    Pendaftaran 20–27 April 2026
                </div>
                <h1>
                    Unjuk aksi,<br>
                    raih <span class="accent">prestasi</span>.
                </h1>
                <p class="hero-desc">
                    Pekan Seni dan Inovasi Mahasiswa (PESOMA) III UIN Prof. K.H. Saifuddin Zuhri Purwokerto. 14 cabang lomba, 6 fakultas, satu ajang pembuktian.
                </p>
                <div class="hero-actions">
                    <a href="<?= e(APP_URL) ?>/src/auth/register.php" class="btn primary">Daftar Sekarang</a>
                    <a href="<?= e(APP_URL) ?>/pages/cabang-lomba.php" class="btn secondary">Lihat Cabang Lomba</a>
                </div>
            </div>
            <div class="hero-panel">
                <div class="hero-panel-card">
                    <span class="hero-panel-label">Babak Final</span>
                    <strong>10–11 Juni 2026</strong>
                    <span>Luring di Auditorium dan ruang lomba UIN SAIZU Purwokerto.</span>
                </div>
                <div class="hero-panel-card">
                    <span class="hero-panel-label">Tema</span>
                    <strong>SDGs berbasis Ekoteologi</strong>
                    <span>Sinergi seni, sains, dan teknologi dalam karya mahasiswa.</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="section" style="padding: 0;">
    <div class="container">
        <div class="stats-grid">
            <div class="stat">
                <span class="stat-value"><?= $totalLomba ?></span>
                <span class="stat-label">Cabang Lomba</span>
            </div>
            <div class="stat">
                <span class="stat-value"><?= $totalFakultas ?></span>
                <span class="stat-label">Fakultas Peserta</span>
            </div>
            <div class="stat">
                <span class="stat-value"><?= $pesertaTerdaftar ?></span>
                <span class="stat-label">Peserta Terdaftar</span>
            </div>
            <div class="stat">
                <span class="stat-value"><?= $finalisTerpilih ?></span>
                <span class="stat-label">Finalis</span>
            </div>
        </div>
    </div>
</section>

<!-- Cabang Lomba (cream block) -->
<section class="section is-cream" id="lomba">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">14 Cabang · 4 Kelompok</span>
            <h2 class="section-title">Cabang lomba PESOMA III.</h2>
            <p class="section-desc">Karya inovasi, seni tutur, kategori Al-Qur'an dan kitab, hingga karya visual. Detail format, ketentuan, dan bobot penilaian tersedia di tiap halaman cabang.</p>
        </div>

        <?php if ($competitions): ?>
            <div class="grid-3">
                <?php foreach ($competitions as $competition): ?>
                    <article class="card">
                        <div class="card-icon">
                            <?php $jenis = strtolower((string) $competition['jenis']); ?>
                            <?php if (str_contains($jenis, 'inovasi') || str_contains($jenis, 'karya')): ?>
                                <i class="fa-solid fa-flask"></i>
                            <?php elseif (str_contains($jenis, 'qur') || str_contains($jenis, 'tilawah') || str_contains($jenis, 'tahfidz') || str_contains($jenis, 'kitab') || str_contains($jenis, 'kaligrafi')): ?>
                                <i class="fa-solid fa-book"></i>
                            <?php elseif (str_contains($jenis, 'film') || str_contains($jenis, 'poster')): ?>
                                <i class="fa-solid fa-film"></i>
                            <?php elseif (str_contains($jenis, 'seni') || str_contains($jenis, 'puisi') || str_contains($jenis, 'pop')): ?>
                                <i class="fa-solid fa-microphone"></i>
                            <?php else: ?>
                                <i class="fa-solid fa-trophy"></i>
                            <?php endif; ?>
                        </div>
                        <h3><?= e($competition['nama_lomba']) ?></h3>
                        <p><?= e(mb_strimwidth((string) ($competition['deskripsi'] ?? ''), 0, 130, '…')) ?: 'Detail dan ketentuan tersedia di halaman cabang.' ?></p>
                        <a href="<?= e(APP_URL) ?>/pages/detail-lomba.php?id=<?= (int) $competition['id'] ?>" class="btn secondary small" style="width: fit-content; margin-top: auto;">Lihat Detail</a>
                    </article>
                <?php endforeach; ?>
            </div>
            <div style="margin-top: 40px;">
                <a class="btn secondary" href="<?= e(APP_URL) ?>/pages/cabang-lomba.php">Lihat Semua 14 Cabang →</a>
            </div>
        <?php else: ?>
            <div class="empty-state">Data cabang lomba belum tersedia.</div>
        <?php endif; ?>
    </div>
</section>

<!-- Jadwal (sage block) -->
<section class="section is-sage" id="jadwal">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">April – Juni 2026</span>
            <h2 class="section-title">Tanggal yang perlu diingat.</h2>
            <p class="section-desc">Sosialisasi, pendaftaran, technical meeting, hingga yudisium pemenang.</p>
        </div>

        <?php if ($schedules): ?>
            <div class="timeline">
                <?php foreach ($schedules as $schedule): ?>
                    <div class="timeline-item">
                        <span><?= e(date('d M Y', strtotime($schedule['event_date']))) ?></span>
                        <strong><?= e($schedule['event_name']) ?></strong>
                        <p><?= e($schedule['location']) ?> · <?= e(substr((string) $schedule['event_time'], 0, 5)) ?> WIB</p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="margin-top: 32px;">
                <a class="btn secondary" href="<?= e(APP_URL) ?>/pages/jadwal.php">Jadwal Lengkap →</a>
            </div>
        <?php else: ?>
            <div class="empty-state">Jadwal publik belum tersedia.</div>
        <?php endif; ?>
    </div>
</section>

<!-- Pengumuman (white) -->
<section class="section" id="pengumuman">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">Informasi Resmi Panitia</span>
            <h2 class="section-title">Pengumuman terbaru.</h2>
        </div>

        <?php if ($announcements): ?>
            <div class="list">
                <?php foreach ($announcements as $announcement): ?>
                    <div class="list-item">
                        <div class="list-item-content">
                            <span class="badge <?= strtolower($announcement['type']) === 'finalis' ? 'finalist' : (strtolower($announcement['type']) === 'pemenang' ? 'winner' : 'pending') ?>" style="width: fit-content;">
                                <span class="badge-dot-indicator"></span><?= e($announcement['type']) ?>
                            </span>
                            <span class="list-item-title"><?= e($announcement['title']) ?></span>
                            <div class="list-item-meta">
                                <i class="fa-regular fa-calendar"></i> <?= e(date('d M Y', strtotime($announcement['published_at']))) ?>
                            </div>
                        </div>
                        <a href="<?= e(APP_URL) ?>/pages/detail-pengumuman.php?id=<?= (int) $announcement['id'] ?>" class="btn secondary small">Baca</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">Belum ada pengumuman.</div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA (navy block) -->
<section class="cta-section">
    <div class="container">
        <h2>Pendaftaran ditutup<br>27 April 2026.</h2>
        <p>Buat akun, lengkapi data tim, dan unggah berkas sebelum batas waktu. Setelah ditutup, daftar peserta akan dikunci dan diumumkan resmi pada 29 April 2026.</p>
        <a href="<?= e(APP_URL) ?>/src/auth/register.php" class="btn primary">Daftar Sekarang</a>
    </div>
</section>

<?php
public_footer();
?>
