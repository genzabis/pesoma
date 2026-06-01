<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';

$competitions = db_fetch_all('SELECT id, nama_lomba, jenis, kategori, deskripsi FROM competitions WHERE is_active = 1 ORDER BY kategori, nama_lomba');
$totalAktif   = count($competitions);
$totalKategori = count(array_unique(array_filter(array_map(fn($c) => $c['kategori'] ?? '', $competitions))));

public_header('Cabang Lomba', 'cabang-lomba.php');

// Group competitions by jenis (best-effort match to juknis groupings)
function categorize_jenis(string $jenis): string
{
    $j = strtolower($jenis);
    if (str_contains($j, 'inovasi') || str_contains($j, 'karya')) return 'Karya Inovasi';
    if (str_contains($j, 'qur') || str_contains($j, 'tilawah') || str_contains($j, 'tahfidz') || str_contains($j, 'kitab') || str_contains($j, 'kaligrafi')) return 'Al-Qur\'an & Kitab';
    if (str_contains($j, 'film') || str_contains($j, 'poster')) return 'Visual';
    if (str_contains($j, 'seni') || str_contains($j, 'puisi') || str_contains($j, 'pop') || str_contains($j, 'story') || str_contains($j, 'da\'i')) return 'Seni Tutur';
    return 'Lainnya';
}
?>

<!-- Hero -->
<section class="hero is-page">
    <div class="container">
        <div class="hero-inner">
            <div class="hero-content">
                <div class="hero-eyebrow"><span class="dot"></span>14 Cabang · 4 Kelompok</div>
                <h1>Cabang lomba<br>PESOMA III.</h1>
                <p class="hero-desc">
                    Karya inovasi, seni tutur, kategori Al-Qur'an dan kitab, hingga karya visual. Pilih satu atau lebih cabang sesuai minat dan ketentuan tim masing-masing.
                </p>
                <div class="hero-actions">
                    <a href="<?= e(APP_URL) ?>/src/auth/register.php" class="btn primary">Daftar Sekarang</a>
                    <a href="<?= e(APP_URL) ?>/pages/unduh-juknis.php" class="btn secondary">Unduh Juknis</a>
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
                <span class="stat-value"><?= $totalAktif ?></span>
                <span class="stat-label">Cabang Aktif</span>
            </div>
            <div class="stat">
                <span class="stat-value">4</span>
                <span class="stat-label">Kelompok Lomba</span>
            </div>
            <div class="stat">
                <span class="stat-value">6</span>
                <span class="stat-label">Fakultas Peserta</span>
            </div>
        </div>
    </div>
</section>

<!-- Daftar Lengkap (cream block) -->
<section class="section is-cream">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">Daftar Lengkap</span>
            <h2 class="section-title">Pilih cabang sesuai minat.</h2>
            <p class="section-desc">Setiap cabang punya format, ketentuan tim, dan bobot penilaian sendiri. Klik detail untuk melihat syarat lengkap.</p>
        </div>

        <?php if (!$competitions): ?>
            <div class="empty-state">Belum ada cabang lomba yang tersedia.</div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($competitions as $c): ?>
                    <article class="card">
                        <div class="card-icon">
                            <?php $jenis = strtolower((string) $c['jenis']); ?>
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
                        <h3><?= e($c['nama_lomba']) ?></h3>
                        <p style="margin-bottom: 8px; font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase; letter-spacing: .12em; color: var(--c-ink-mute);"><?= e(categorize_jenis((string) $c['jenis'])) ?></p>
                        <p><?= e(mb_strimwidth((string) ($c['deskripsi'] ?? ''), 0, 130, '…')) ?: 'Detail dan ketentuan tersedia di halaman cabang.' ?></p>
                        <a href="<?= e(APP_URL) ?>/pages/detail-lomba.php?id=<?= (int) $c['id'] ?>" class="btn secondary small" style="width: fit-content; margin-top: auto;">Lihat Detail</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2>Sudah pilih cabang?<br>Daftar tim Anda.</h2>
        <p>Buat akun, tambahkan anggota tim, lalu unggah berkas pendaftaran sebelum 27 April 2026.</p>
        <a href="<?= e(APP_URL) ?>/src/auth/register.php" class="btn primary">Daftar Sekarang</a>
    </div>
</section>

<?php public_footer(); ?>
