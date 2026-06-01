<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/footer.php';

$id = (int) ($_GET['id'] ?? 0);
$competition = $id > 0 ? db_fetch('SELECT * FROM competitions WHERE id = ? AND is_active = 1', [$id]) : null;

if (!$competition) {
    http_response_code(404);
    public_header('Lomba tidak ditemukan', 'cabang-lomba.php');
    echo '<section class="section"><div class="container"><div class="card"><h2>Cabang lomba tidak ditemukan</h2><p class="muted">Data tidak tersedia atau lomba tidak aktif.</p><a class="btn" href="cabang-lomba.php">Kembali ke Daftar Lomba</a></div></div></section>';
    public_footer();
    exit;
}

$aspek = json_decode((string) ($competition['aspek_penilaian'] ?? ''), true);
$aspek = is_array($aspek) ? $aspek : [];

public_header($competition['nama_lomba'], 'cabang-lomba.php');
?>
<section class="hero">
    <div class="detail-shell hero-grid">
        <div class="hero-content">
            <span class="eyebrow">Detail Cabang Lomba</span>
            <h1><?= e($competition['nama_lomba']) ?></h1>
            <p><?= e($competition['kategori'] ?: 'Umum') ?> · Kode: <?= e($competition['kode_lomba']) ?></p>
            <div class="actions"><a class="btn" href="<?= e(APP_URL) ?>/src/auth/login.php">Daftar / Login</a><a class="btn secondary" href="cabang-lomba.php">Kembali</a></div>
            <div class="hero-note"><span>✓ <?= e($competition['jenis']) ?></span><span>✓ <?= (int) ($competition['min_anggota'] ?? 1) ?>–<?= (int) ($competition['max_anggota'] ?? 5) ?> anggota</span><span>✓ <?= (int) ($competition['requires_mentor'] ?? 0) === 1 ? 'Pendamping wajib' : 'Pendamping opsional' ?></span></div>
        </div>
        <div class="hero-panel" aria-label="Highlight detail lomba">
            <div class="hero-panel-card"><span class="hero-panel-label">Pendaftaran</span><strong><?= $competition['registration_deadline'] ? e(date('d M Y H:i', strtotime((string) $competition['registration_deadline']))) . ' WIB' : '-' ?></strong><span>Batas resmi pendaftaran cabang lomba ini.</span></div>
            <div class="hero-panel-card"><span class="hero-panel-label">Upload Karya</span><strong><?= $competition['upload_deadline'] ? e(date('d M Y H:i', strtotime((string) $competition['upload_deadline']))) . ' WIB' : '-' ?></strong><span>Pastikan karya diunggah sebelum batas waktu.</span></div>
        </div>
    </div>
</section>
<section class="section">
    <div class="detail-shell">
        <article class="card" style="margin-bottom:18px">
            <h3>Deskripsi</h3>
            <p><?= nl2br(e((string) ($competition['deskripsi'] ?? '-'))) ?></p>
            <h3>Ketentuan</h3>
            <p><?= nl2br(e((string) ($competition['aturan'] ?? '-'))) ?></p>
            <p class="muted">
                Anggota: <?= (int) ($competition['min_anggota'] ?? 1) ?>–<?= (int) ($competition['max_anggota'] ?? 5) ?> orang ·
                Pendamping: <?= (int) ($competition['requires_mentor'] ?? 0) === 1 ? 'Wajib' : 'Tidak wajib' ?>
            </p>
        </article>

        <?php if ($aspek): ?>
            <article class="card" style="margin-bottom:18px">
                <h3>Aspek Penilaian</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Aspek</th>
                            <th>Bobot</th>
                        </tr>
                    </thead>
                    <tbody><?php foreach ($aspek as $row): ?><tr>
                                <td><?= e((string) ($row['nama'] ?? '-')) ?></td>
                                <td><?= e((string) ($row['bobot'] ?? '-')) ?>%</td>
                            </tr><?php endforeach; ?></tbody>
                </table>
            </article>
        <?php endif; ?>

        <article class="card">
            <h3>Jadwal Penting</h3>
            <p><b>Batas Pendaftaran:</b> <?= $competition['registration_deadline'] ? e(date('d M Y H:i', strtotime((string) $competition['registration_deadline']))) . ' WIB' : '-' ?></p>
            <p><b>Batas Upload Karya:</b> <?= $competition['upload_deadline'] ? e(date('d M Y H:i', strtotime((string) $competition['upload_deadline']))) . ' WIB' : '-' ?></p>
            <?php if (!empty($competition['juknis_file'])): ?>
                <a class="btn secondary" href="<?= e(APP_URL) ?>/storage/juknis/<?= e((string) $competition['juknis_file']) ?>">Unduh Juknis</a>
            <?php endif; ?>
            <a class="btn" href="<?= e(APP_URL) ?>/src/auth/login.php">Daftar / Login</a>
            <a class="btn secondary" href="cabang-lomba.php">Kembali</a>
        </article>
    </div>
</section>
<?php public_footer(); ?>