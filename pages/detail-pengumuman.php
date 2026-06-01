<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';

$id = (int) ($_GET['id'] ?? 0);
$announcement = $id > 0 ? db_fetch('SELECT * FROM announcements WHERE id = ? AND is_published = 1', [$id]) : null;

if (!$announcement) {
    http_response_code(404);
    public_header('Pengumuman tidak ditemukan', 'pengumuman.php');
?>
    <section class="hero is-page">
        <div class="container">
            <div class="hero-inner">
                <div class="hero-content">
                    <div class="hero-eyebrow"><span class="dot"></span>404</div>
                    <h1>Pengumuman<br>tidak ditemukan.</h1>
                    <p class="hero-desc">Data tidak tersedia atau belum dipublikasikan oleh panitia.</p>
                    <div class="hero-actions">
                        <a href="<?= e(APP_URL) ?>/pages/pengumuman.php" class="btn primary">Kembali ke Pengumuman</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
    public_footer();
    exit;
}

public_header((string) $announcement['title'], 'pengumuman.php');
?>

<!-- Hero -->
<section class="hero is-page">
    <div class="container">
        <div class="hero-inner">
            <div class="hero-content">
                <div class="hero-eyebrow">
                    <span class="dot"></span>
                    <?= e(strtoupper($announcement['type'])) ?> · <?= e(date('d M Y', strtotime((string) $announcement['published_at']))) ?>
                </div>
                <h1><?= e($announcement['title']) ?></h1>
                <div class="hero-actions">
                    <a href="<?= e(APP_URL) ?>/pages/pengumuman.php" class="btn secondary">← Semua Pengumuman</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Konten Pengumuman -->
<section class="section">
    <div class="container" style="max-width: 760px;">
        <div style="font-size: 18px; line-height: 1.75; color: var(--c-ink); white-space: pre-wrap;"><?= e((string) $announcement['content']) ?></div>

        <div style="margin-top: 64px; padding-top: 32px; border-top: 1px solid var(--c-line); display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
            <span class="muted" style="font-family: 'JetBrains Mono', monospace; font-size: 12px; text-transform: uppercase; letter-spacing: .12em;">
                Dipublikasikan <?= e(date('d M Y H:i', strtotime((string) $announcement['published_at']))) ?> WIB
            </span>
            <a href="<?= e(APP_URL) ?>/pages/pengumuman.php" class="btn secondary small">← Kembali ke Pengumuman</a>
        </div>
    </div>
</section>

<?php public_footer(); ?>
