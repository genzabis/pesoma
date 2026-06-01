<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';

$id = (int) ($_GET['id'] ?? 0);
$competition = $id > 0 ? db_fetch('SELECT * FROM competitions WHERE id = ? AND is_active = 1', [$id]) : null;

if (!$competition) {
    http_response_code(404);
    public_header('Lomba tidak ditemukan', 'cabang-lomba.php');
    echo '<section class="hero is-page"><div class="container"><div class="hero-inner"><div class="hero-content">'
        . '<div class="hero-eyebrow"><span class="dot"></span>404</div>'
        . '<h1>Cabang lomba<br>tidak ditemukan.</h1>'
        . '<p class="hero-desc">Data tidak tersedia atau lomba sudah tidak aktif.</p>'
        . '<div class="hero-actions"><a class="btn primary" href="' . e(APP_URL) . '/pages/cabang-lomba.php">Kembali ke Daftar Lomba</a></div>'
        . '</div></div></div></section>';
    public_footer();
    exit;
}

$aspek = json_decode((string) ($competition['aspek_penilaian'] ?? ''), true);
$aspek = is_array($aspek) ? $aspek : [];

public_header($competition['nama_lomba'], 'cabang-lomba.php');
?>

<!-- Hero -->
<section class="hero is-page">
    <div class="container">
        <div class="hero-inner">
            <div class="hero-content">
                <div class="hero-eyebrow"><span class="dot"></span>Detail Cabang · Kode <?= e($competition['kode_lomba']) ?></div>
                <h1><?= e($competition['nama_lomba']) ?>.</h1>
                <p class="hero-desc">
                    <?= e($competition['kategori'] ?: 'Umum') ?> · <?= e($competition['jenis']) ?>.
                    Tim <?= (int) ($competition['min_anggota'] ?? 1) ?>–<?= (int) ($competition['max_anggota'] ?? 5) ?> orang ·
                    Pendamping <?= (int) ($competition['requires_mentor'] ?? 0) === 1 ? 'wajib' : 'tidak wajib' ?>.
                </p>
                <div class="hero-actions">
                    <a href="<?= e(APP_URL) ?>/src/auth/register.php" class="btn primary">Daftar Sekarang</a>
                    <a href="<?= e(APP_URL) ?>/pages/cabang-lomba.php" class="btn secondary">← Semua Cabang</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Info Stats -->
<section class="section" style="padding: 0;">
    <div class="container">
        <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
            <div class="stat">
                <span class="stat-value"><?= (int) ($competition['min_anggota'] ?? 1) ?>–<?= (int) ($competition['max_anggota'] ?? 5) ?></span>
                <span class="stat-label">Anggota Tim</span>
            </div>
            <div class="stat">
                <span class="stat-value"><?= (int) ($competition['requires_mentor'] ?? 0) === 1 ? 'Wajib' : '—' ?></span>
                <span class="stat-label">Pendamping</span>
            </div>
            <div class="stat">
                <span class="stat-value"><?= e($competition['kode_lomba']) ?></span>
                <span class="stat-label">Kode Lomba</span>
            </div>
            <div class="stat">
                <span class="stat-value"><?= count($aspek) ?: '—' ?></span>
                <span class="stat-label">Aspek Penilaian</span>
            </div>
        </div>
    </div>
</section>

<!-- Deskripsi & Aturan -->
<section class="section">
    <div class="container" style="max-width: 820px;">
        <div class="section-head">
            <span class="section-eyebrow">Deskripsi Lomba</span>
            <h2 class="section-title">Apa yang dilombakan.</h2>
        </div>
        <p style="font-size: 17px; line-height: 1.7; color: var(--c-ink-soft);"><?= nl2br(e((string) ($competition['deskripsi'] ?? '—'))) ?></p>

        <div class="section-head" style="margin-top: 72px;">
            <span class="section-eyebrow">Ketentuan</span>
            <h2 class="section-title">Aturan & ketentuan tim.</h2>
        </div>
        <p style="font-size: 16px; line-height: 1.7; color: var(--c-ink-soft);"><?= nl2br(e((string) ($competition['aturan'] ?? '—'))) ?></p>
    </div>
</section>

<?php if ($aspek): ?>
<!-- Aspek Penilaian (cream) -->
<section class="section is-cream">
    <div class="container" style="max-width: 820px;">
        <div class="section-head">
            <span class="section-eyebrow">Bobot Penilaian</span>
            <h2 class="section-title">Aspek penilaian<br>juri.</h2>
            <p class="section-desc">Setiap aspek punya bobot persentase tertentu. Total seluruh bobot 100%.</p>
        </div>
        <div class="list">
            <?php foreach ($aspek as $item): ?>
                <div class="list-item">
                    <div class="list-item-content">
                        <span class="list-item-title"><?= e($item['aspek'] ?? '—') ?></span>
                    </div>
                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 16px; font-weight: 600; color: var(--c-ink); letter-spacing: -.01em;"><?= (int) ($item['bobot'] ?? 0) ?>%</span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Batas Waktu (sage) -->
<section class="section is-sage">
    <div class="container" style="max-width: 820px;">
        <div class="section-head">
            <span class="section-eyebrow">Tanggal Penting</span>
            <h2 class="section-title">Deadline pendaftaran<br>dan upload karya.</h2>
        </div>
        <div class="timeline">
            <div class="timeline-item">
                <span>Pendaftaran</span>
                <strong><?= $competition['registration_deadline'] ? e(date('d M Y', strtotime((string) $competition['registration_deadline']))) : 'Belum ditentukan' ?></strong>
                <p><?= $competition['registration_deadline'] ? e(date('H:i', strtotime((string) $competition['registration_deadline']))) . ' WIB' : '—' ?></p>
            </div>
            <div class="timeline-item">
                <span>Upload Karya</span>
                <strong><?= $competition['upload_deadline'] ? e(date('d M Y', strtotime((string) $competition['upload_deadline']))) : 'Belum ditentukan' ?></strong>
                <p><?= $competition['upload_deadline'] ? e(date('H:i', strtotime((string) $competition['upload_deadline']))) . ' WIB' : '—' ?></p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2>Tertarik ikut<br><?= e($competition['nama_lomba']) ?>?</h2>
        <p>Buat akun, susun tim, lalu ikuti tahap pendaftaran sebelum deadline yang tertera di atas.</p>
        <a href="<?= e(APP_URL) ?>/src/auth/register.php" class="btn primary">Daftar Sekarang</a>
    </div>
</section>

<?php public_footer(); ?>
