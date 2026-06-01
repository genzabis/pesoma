<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';

$type = $_GET['type'] ?? '';
$where = 'WHERE a.is_published = 1';
$params = [];
if (in_array($type, ['umum', 'finalis', 'winner'], true)) {
    $where .= ' AND a.type = ?';
    $params[] = $type;
}
$announcements = db_fetch_all("SELECT a.* FROM announcements a {$where} ORDER BY a.published_at DESC", $params);
$finalists = db_fetch_all('SELECT c.nama_lomba, r.nomor_peserta, u.nama, u.nim, u.fakultas, f.rank_penyisihan FROM finalists f JOIN registrations r ON r.id=f.registration_id JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=f.competition_id ORDER BY c.nama_lomba, f.rank_penyisihan ASC, u.nama');
$winners = db_fetch_all('SELECT c.nama_lomba, r.nomor_peserta, u.nama, u.nim, u.fakultas, w.juara_ke, w.total_score FROM winners w JOIN registrations r ON r.id=w.registration_id JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=w.competition_id ORDER BY c.nama_lomba, w.juara_ke ASC');

public_header('Pengumuman', 'pengumuman.php');
?>

<!-- Hero -->
<section class="hero is-page">
    <div class="container">
        <div class="hero-inner">
            <div class="hero-content">
                <div class="hero-eyebrow"><span class="dot"></span>Informasi Resmi</div>
                <h1>Pengumuman<br>PESOMA III.</h1>
                <p class="hero-desc">
                    Daftar peserta, finalis, pemenang, dan pengumuman umum yang dipublikasikan panitia. Halaman ini diperbarui setiap kali ada informasi baru.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="section" style="padding: 0;">
    <div class="container">
        <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="stat">
                <span class="stat-value"><?= count($announcements) ?></span>
                <span class="stat-label">Pengumuman</span>
            </div>
            <div class="stat">
                <span class="stat-value"><?= count($finalists) ?></span>
                <span class="stat-label">Finalis</span>
            </div>
            <div class="stat">
                <span class="stat-value"><?= count($winners) ?></span>
                <span class="stat-label">Pemenang</span>
            </div>
        </div>
    </div>
</section>

<!-- Daftar Pengumuman -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">Berita Terbaru</span>
            <h2 class="section-title">Daftar pengumuman.</h2>
        </div>

        <?php if (!$announcements): ?>
            <div class="empty-state">Belum ada pengumuman yang dipublikasikan.</div>
        <?php else: ?>
            <div class="list">
                <?php foreach ($announcements as $a): ?>
                    <div class="list-item">
                        <div class="list-item-content">
                            <span class="badge <?= strtolower($a['type']) === 'finalis' ? 'finalist' : (strtolower($a['type']) === 'pemenang' ? 'winner' : 'pending') ?>" style="width: fit-content;">
                                <span class="badge-dot-indicator"></span><?= e($a['type']) ?>
                            </span>
                            <span class="list-item-title"><?= e($a['title']) ?></span>
                            <div class="list-item-meta">
                                <i class="fa-regular fa-calendar"></i> <?= e(date('d M Y', strtotime($a['published_at']))) ?>
                            </div>
                        </div>
                        <a href="<?= e(APP_URL) ?>/pages/detail-pengumuman.php?id=<?= (int) $a['id'] ?>" class="btn secondary small">Baca</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if ($finalists): ?>
<!-- Daftar Finalis (cream) -->
<section class="section is-cream">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">Lolos Penyisihan</span>
            <h2 class="section-title">Daftar finalis.</h2>
            <p class="section-desc">Selamat kepada peserta yang lolos ke babak final PESOMA III.</p>
        </div>
        <div style="overflow-x: auto; background: rgba(255,255,255,.65); border: 1px solid rgba(15,17,21,.12); border-radius: 14px;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Lomba</th>
                        <th>No. Peserta</th>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Fakultas</th>
                        <th>Rank</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($finalists as $f): ?>
                        <tr>
                            <td><?= e($f['nama_lomba']) ?></td>
                            <td><?= e($f['nomor_peserta']) ?></td>
                            <td><?= e($f['nama']) ?></td>
                            <td><?= e($f['nim']) ?></td>
                            <td><?= e($f['fakultas']) ?></td>
                            <td><span class="badge finalist"><?= (int) $f['rank_penyisihan'] ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($winners): ?>
<!-- Daftar Pemenang (sage) -->
<section class="section is-sage">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">Juara</span>
            <h2 class="section-title">Daftar pemenang.</h2>
            <p class="section-desc">Selamat kepada para juara PESOMA III.</p>
        </div>
        <div style="overflow-x: auto; background: rgba(255,255,255,.65); border: 1px solid rgba(15,17,21,.12); border-radius: 14px;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Lomba</th>
                        <th>No. Peserta</th>
                        <th>Nama</th>
                        <th>Fakultas</th>
                        <th>Juara</th>
                        <th>Skor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($winners as $w): ?>
                        <tr>
                            <td><?= e($w['nama_lomba']) ?></td>
                            <td><?= e($w['nomor_peserta']) ?></td>
                            <td><?= e($w['nama']) ?></td>
                            <td><?= e($w['fakultas']) ?></td>
                            <td><span class="badge winner">Juara <?= (int) $w['juara_ke'] ?></span></td>
                            <td><?= number_format((float) $w['total_score'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php endif; ?>

<?php public_footer(); ?>
