<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/footer.php';

$id = (int) ($_GET['id'] ?? 0);
$announcement = $id > 0 ? db_fetch('SELECT * FROM announcements WHERE id = ? AND is_published = 1', [$id]) : null;
if (!$announcement) {
    http_response_code(404);
}

public_header($announcement ? (string) $announcement['title'] : 'Pengumuman tidak ditemukan', 'pengumuman.php');
?>
<section class="hero">
    <div class="detail-shell hero-grid">
        <div class="hero-content">
            <span class="eyebrow">Detail Pengumuman</span>
            <h1><?= $announcement ? e($announcement['title']) : 'Pengumuman tidak ditemukan' ?></h1>
            <p><?= $announcement ? 'Informasi resmi yang telah dipublikasikan panitia PESOMA 2026.' : 'Data yang Anda cari tidak tersedia atau belum dipublikasikan.' ?></p>
            <div class="actions"><a class="btn secondary" href="pengumuman.php">Kembali ke Pengumuman</a></div>
            <div class="hero-note"><span>✓ Informasi resmi</span><span>✓ Terpublikasi panitia</span><span>✓ Portal terpusat</span></div>
        </div>
        <div class="hero-panel" aria-label="Highlight detail pengumuman">
            <div class="hero-panel-card"><span class="hero-panel-label">Kategori</span><strong><?= $announcement ? e($announcement['type']) : '-' ?></strong><span>Jenis pengumuman yang sedang dibaca.</span></div>
            <div class="hero-panel-card"><span class="hero-panel-label">Publikasi</span><strong><?= $announcement ? e(date('d M Y H:i', strtotime((string) $announcement['published_at']))) . ' WIB' : '-' ?></strong><span>Waktu pengumuman dipublikasikan.</span></div>
        </div>
    </div>
</section>
<section class="section">
    <div class="detail-shell">
        <article class="card">
            <?php if (!$announcement): ?>
                <h2>Pengumuman tidak ditemukan</h2>
                <p class="muted">Data tidak tersedia atau belum dipublikasikan.</p>
            <?php else: ?>
                <span class="badge <?= $announcement['type'] === 'winner' ? 'winner' : '' ?>"><?= e($announcement['type']) ?></span>
                <h2 style="margin-top:16px;"><?= e($announcement['title']) ?></h2>
                <p class="muted"><?= e(date('d M Y H:i', strtotime((string) $announcement['published_at']))) ?> WIB</p>
                <p><?= nl2br(e((string) $announcement['content'])) ?></p>
            <?php endif; ?>
            <div class="detail-actions">
                <a class="btn secondary" href="pengumuman.php">Kembali ke Pengumuman</a>
            </div>
        </article>
    </div>
</section>
<?php public_footer(); ?>