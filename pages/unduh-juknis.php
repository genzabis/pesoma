<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';

$competitions = db_fetch_all('SELECT id, nama_lomba, juknis_file FROM competitions WHERE is_active = 1 AND juknis_file IS NOT NULL AND juknis_file <> "" ORDER BY nama_lomba');

public_header('Unduh Juknis', 'cabang-lomba.php');
?>

<!-- Hero -->
<section class="hero is-page">
    <div class="container">
        <div class="hero-inner">
            <div class="hero-content">
                <div class="hero-eyebrow"><span class="dot"></span>Panduan Resmi Peserta</div>
                <h1>Petunjuk teknis<br>cabang lomba.</h1>
                <p class="hero-desc">
                    Dokumen juknis berisi ketentuan penulisan, format file, jadwal pengumpulan, dan bobot penilaian per cabang. Wajib dibaca sebelum mendaftar.
                </p>
                <div class="hero-actions">
                    <a href="<?= e(APP_URL) ?>/pages/cabang-lomba.php" class="btn primary">Lihat Cabang Lomba</a>
                    <a href="<?= e(APP_URL) ?>/src/auth/register.php" class="btn secondary">Daftar Sekarang</a>
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
                <span class="stat-value"><?= count($competitions) ?></span>
                <span class="stat-label">Juknis Tersedia</span>
            </div>
            <div class="stat">
                <span class="stat-value">PDF</span>
                <span class="stat-label">Format Dokumen</span>
            </div>
            <div class="stat">
                <span class="stat-value">Resmi</span>
                <span class="stat-label">Sumber Panitia</span>
            </div>
        </div>
    </div>
</section>

<!-- Daftar Juknis (cream) -->
<section class="section is-cream">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">Dokumentasi Lomba</span>
            <h2 class="section-title">Unduh juknis<br>per cabang.</h2>
            <p class="section-desc">Tiap cabang punya juknis sendiri. Buka PDF, baca, lalu kembali ke halaman pendaftaran.</p>
        </div>

        <?php if (!$competitions): ?>
            <div class="empty-state">Belum ada juknis yang tersedia untuk diunduh.</div>
        <?php else: ?>
            <div class="list">
                <?php foreach ($competitions as $c): ?>
                    <div class="list-item">
                        <div class="list-item-content">
                            <span class="list-item-title"><?= e($c['nama_lomba']) ?></span>
                            <div class="list-item-meta">
                                <i class="fa-regular fa-file-pdf"></i> Format PDF · <?= e($c['juknis_file']) ?>
                            </div>
                        </div>
                        <a href="<?= e(APP_URL) ?>/public/uploads/juknis/<?= e($c['juknis_file']) ?>" target="_blank" rel="noopener" class="btn secondary small">
                            <i class="fa-solid fa-download"></i> Unduh
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2>Sudah baca<br>juknis-nya?</h2>
        <p>Pastikan Anda memahami semua ketentuan tim, format penulisan, dan jadwal pengumpulan sebelum mendaftar.</p>
        <a href="<?= e(APP_URL) ?>/src/auth/register.php" class="btn primary">Daftar Sekarang</a>
    </div>
</section>

<?php public_footer(); ?>
