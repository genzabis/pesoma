<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';

$schedules = db_fetch_all('SELECT event_name, event_date, event_time, location, link FROM schedules WHERE is_public = 1 ORDER BY event_date ASC, event_time ASC');

public_header('Jadwal', 'jadwal.php');
?>

<!-- Hero -->
<section class="hero is-page">
    <div class="container">
        <div class="hero-inner">
            <div class="hero-content">
                <div class="hero-eyebrow"><span class="dot"></span>April – Juni 2026</div>
                <h1>Jadwal kegiatan<br>PESOMA III.</h1>
                <p class="hero-desc">
                    Rangkaian agenda resmi: sosialisasi, pendaftaran, technical meeting, pengumpulan karya, hingga yudisium dan pengumuman pemenang.
                </p>
                <div class="hero-actions">
                    <a href="<?= e(APP_URL) ?>/src/auth/register.php" class="btn primary">Daftar Sekarang</a>
                    <a href="<?= e(APP_URL) ?>/pages/pengumuman.php" class="btn secondary">Lihat Pengumuman</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="section" style="padding: 0;">
    <div class="container">
        <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="stat">
                <span class="stat-value"><?= count($schedules) ?></span>
                <span class="stat-label">Agenda Publik</span>
            </div>
            <div class="stat">
                <span class="stat-value">WIB</span>
                <span class="stat-label">Zona Waktu</span>
            </div>
            <div class="stat">
                <span class="stat-value">Live</span>
                <span class="stat-label">Pembaruan Jadwal</span>
            </div>
        </div>
    </div>
</section>

<!-- Timeline (sage block) -->
<section class="section is-sage">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">Tahapan Lengkap</span>
            <h2 class="section-title">Tanggal yang perlu diingat.</h2>
            <p class="section-desc">Jadwal di bawah dapat diperbarui sewaktu-waktu oleh panitia. Pastikan akun Anda aktif untuk menerima notifikasi resmi.</p>
        </div>

        <?php if (!$schedules): ?>
            <div class="empty-state">Jadwal publik belum tersedia.</div>
        <?php else: ?>
            <div class="timeline">
                <?php foreach ($schedules as $s): ?>
                    <div class="timeline-item">
                        <span><?= e(date('d M Y', strtotime($s['event_date']))) ?> · <?= e(substr((string) $s['event_time'], 0, 5)) ?></span>
                        <strong><?= e($s['event_name']) ?></strong>
                        <p><?= e($s['location']) ?>
                            <?php if (!empty($s['link'])): ?>
                                · <a href="<?= e($s['link']) ?>" target="_blank" rel="noopener" style="color: var(--c-ink); text-decoration: underline;">Buka link →</a>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2>Jangan lewatkan<br>tanggal penting.</h2>
        <p>Daftarkan akun lebih awal agar Anda mendapat notifikasi setiap pembaruan jadwal langsung dari portal panitia.</p>
        <a href="<?= e(APP_URL) ?>/src/auth/register.php" class="btn primary">Daftar Sekarang</a>
    </div>
</section>

<?php public_footer(); ?>
