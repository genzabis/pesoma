<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/footer.php';

$competitions = db_fetch_all('SELECT id, nama_lomba, jenis, kategori, deskripsi FROM competitions WHERE is_active = 1 ORDER BY kategori, nama_lomba');

public_header('Cabang Lomba', 'cabang-lomba.php');
?>
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-content">
            <span class="eyebrow">Eksplorasi Lomba</span>
            <h1>Cabang Lomba PESOMA 2026</h1>
            <p>Daftar lengkap cabang lomba seni, olahraga, riset, dan inovasi yang dapat diikuti mahasiswa.</p>
            <div class="actions"><a class="btn" href="<?= e(APP_URL) ?>/src/auth/register.php">Daftar Sekarang</a><a class="btn secondary" href="unduh-juknis.php">Unduh Juknis</a></div>
            <div class="hero-note"><span>✓ <?= count($competitions) ?> cabang aktif</span><span>✓ Detail ketentuan lomba</span><span>✓ Pendaftaran online</span></div>
        </div>
        <div class="hero-panel" aria-label="Highlight cabang lomba">
            <div class="hero-panel-card"><span class="hero-panel-label">Pilihan</span><strong>Temukan lomba sesuai minat</strong><span>Setiap cabang memuat kategori, jenis lomba, dan deskripsi singkat.</span></div>
            <div class="hero-panel-card"><span class="hero-panel-label">Panduan</span><strong>Baca detail sebelum daftar</strong><span>Pastikan memahami ketentuan, anggota tim, dan batas unggah karya.</span></div>
        </div>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="page-highlight">
            <div class="stat"><strong><?= count($competitions) ?></strong><span>Cabang Aktif</span></div>
            <div class="stat"><strong>4+</strong><span>Kategori Kompetisi</span></div>
            <div class="stat"><strong>Online</strong><span>Registrasi Peserta</span></div>
        </div>
        <div class="section-head">
            <div class="section-tag">Daftar Lengkap</div>
            <h2 class="section-title">Pilih Cabang Lomba Sesuai Minat</h2>
            <p class="section-desc">Jelajahi berbagai kategori lomba dan temukan yang paling sesuai dengan bakat dan minat Anda.</p>
        </div>
        <?php if (!$competitions): ?>
            <div class="empty-state">Belum ada cabang lomba yang tersedia.</div>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($competitions as $c): ?>
                    <article class="card">
                        <div class="card-top"><span class="card-icon"><i class="fas fa-trophy"></i></span><span class="badge"><?= e($c['jenis']) ?></span></div>
                        <div class="card-accent"><i class="fas fa-sparkles"></i><span>Cabang PESOMA 2026</span></div>
                        <h3><?= e($c['nama_lomba']) ?></h3>
                        <p class="muted"><?= e($c['kategori'] ?: 'Umum') ?></p>
                        <p><?= e(mb_strimwidth((string) ($c['deskripsi'] ?? ''), 0, 140, '...')) ?: 'Informasi cabang lomba PESOMA 2026.' ?></p>
                        <a class="btn" href="detail-lomba.php?id=<?= (int) $c['id'] ?>">Lihat Detail</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php public_footer(); ?>