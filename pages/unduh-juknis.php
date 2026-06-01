<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/footer.php';

$competitions = db_fetch_all('SELECT id, nama_lomba, juknis_file FROM competitions WHERE is_active = 1 AND juknis_file IS NOT NULL AND juknis_file <> "" ORDER BY nama_lomba');

public_header('Unduh Juknis', 'cabang-lomba.php');
?>
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-content">
            <span class="eyebrow">Panduan Peserta</span>
            <h1>Unduh Petunjuk Teknis</h1>
            <p>Dokumen juknis resmi tiap cabang lomba untuk panduan lengkap peserta PESOMA 2026.</p>
            <div class="actions"><a class="btn" href="cabang-lomba.php">Lihat Cabang Lomba</a><a class="btn secondary" href="<?= e(APP_URL) ?>/src/auth/register.php">Daftar Sekarang</a></div>
            <div class="hero-note"><span>✓ <?= count($competitions) ?> juknis tersedia</span><span>✓ Panduan resmi</span><span>✓ Dokumen lomba</span></div>
        </div>
        <div class="hero-panel" aria-label="Highlight juknis">
            <div class="hero-panel-card"><span class="hero-panel-label">Juknis</span><strong>Panduan teknis lomba</strong><span>Unduh dokumen sebelum mendaftar atau mengunggah karya.</span></div>
            <div class="hero-panel-card"><span class="hero-panel-label">Peserta</span><strong>Siapkan sesuai ketentuan</strong><span>Pastikan format karya dan aturan lomba sudah sesuai dokumen.</span></div>
        </div>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="page-highlight">
            <div class="stat"><strong><?= count($competitions) ?></strong><span>Juknis Tersedia</span></div>
            <div class="stat"><strong>PDF</strong><span>Format Dokumen</span></div>
            <div class="stat"><strong>Resmi</strong><span>Panduan Panitia</span></div>
        </div>
        <div class="section-head">
            <div class="section-tag">Dokumentasi</div>
            <h2 class="section-title">Petunjuk Teknis Cabang Lomba</h2>
            <p class="section-desc">Unduh panduan lengkap untuk setiap cabang lomba agar Anda siap mengikuti kompetisi.</p>
        </div>
        <?php if (!$competitions): ?>
            <div class="empty-state">Belum ada juknis yang tersedia untuk diunduh.</div>
        <?php else: ?>
            <div class="card">
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Cabang Lomba</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody><?php foreach ($competitions as $c): ?><tr>
                                    <td><?= e($c['nama_lomba']) ?></td>
                                    <td><a class="btn small secondary" href="<?= e(APP_URL) ?>/storage/juknis/<?= e((string) $c['juknis_file']) ?>">Unduh</a></td>
                                </tr><?php endforeach; ?></tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php public_footer(); ?>