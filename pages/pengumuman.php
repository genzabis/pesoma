<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/footer.php';

$type = $_GET['type'] ?? '';
$where = 'WHERE a.is_published = 1';
$params = [];
if (in_array($type, ['umum', 'finalis', 'winner'], true)) {
    $where .= ' AND a.type = ?';
    $params[] = $type;
}
$announcements = db_fetch_all("SELECT a.* FROM announcements a {$where} ORDER BY a.published_at DESC", $params);
$finalists = db_fetch_all('SELECT c.nama_lomba, r.nomor_peserta, u.nama, u.nim, u.fakultas, f.rank_penyisihan, f.announced_at FROM finalists f JOIN registrations r ON r.id=f.registration_id JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=f.competition_id ORDER BY c.nama_lomba, f.rank_penyisihan ASC, u.nama');
$winners = db_fetch_all('SELECT c.nama_lomba, r.nomor_peserta, u.nama, u.nim, u.fakultas, w.juara_ke, w.total_score, w.announced_at FROM winners w JOIN registrations r ON r.id=w.registration_id JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=w.competition_id ORDER BY c.nama_lomba, w.juara_ke ASC');

public_header('Pengumuman', 'pengumuman.php');
?>
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-content">
            <span class="eyebrow">Informasi Resmi PESOMA 2026</span>
            <h1>Pengumuman Finalis &amp; Pemenang</h1>
            <p>Finalis, juara, dan pengumuman umum yang telah dipublikasikan panitia dalam satu halaman terpusat.</p>
            <div class="actions"><a class="btn" href="pengumuman.php">Semua Pengumuman</a><a class="btn secondary" href="jadwal.php">Lihat Jadwal</a></div>
            <div class="hero-note"><span>✓ <?= count($announcements) ?> pengumuman</span><span>✓ <?= count($finalists) ?> finalis</span><span>✓ <?= count($winners) ?> pemenang</span></div>
        </div>
        <div class="hero-panel" aria-label="Highlight pengumuman">
            <div class="hero-panel-card"><span class="hero-panel-label">Finalis</span><strong>Daftar finalis resmi</strong><span>Data finalis ditampilkan berdasarkan cabang lomba dan peringkat penyisihan.</span></div>
            <div class="hero-panel-card"><span class="hero-panel-label">Juara</span><strong>Pemenang terpublikasi</strong><span>Pengumuman juara ditampilkan setelah disahkan oleh panitia.</span></div>
        </div>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="page-highlight">
            <div class="stat"><strong><?= count($announcements) ?></strong><span>Pengumuman</span></div>
            <div class="stat"><strong><?= count($finalists) ?></strong><span>Finalis</span></div>
            <div class="stat"><strong><?= count($winners) ?></strong><span>Pemenang</span></div>
        </div>
        <div class="section-head">
            <div class="section-tag">Filter Pengumuman</div>
            <h2 class="section-title">Pilih kategori informasi yang ingin dilihat</h2>
            <p class="section-desc">Gunakan filter berikut untuk menampilkan pengumuman umum, finalis, atau pemenang sesuai kebutuhan.</p>
        </div>
        <div class="filters">
            <a class="btn <?= $type === '' ? 'active' : '' ?>" href="pengumuman.php">Semua</a>
            <a class="btn <?= $type === 'umum' ? 'active' : '' ?>" href="pengumuman.php?type=umum">Umum</a>
            <a class="btn <?= $type === 'finalis' ? 'active' : '' ?>" href="pengumuman.php?type=finalis">Finalis</a>
            <a class="btn <?= $type === 'winner' ? 'active' : '' ?>" href="pengumuman.php?type=winner">Pemenang</a>
        </div>
        <?php if (!$announcements): ?>
            <div class="empty-state">Belum ada pengumuman yang dipublikasikan.</div>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($announcements as $a): ?>
                    <article class="card">
                        <div class="card-top"><span class="card-icon"><i class="fas fa-bullhorn"></i></span><span class="badge <?= $a['type'] === 'winner' ? 'winner' : '' ?>"><?= e($a['type']) ?></span></div>
                        <div class="card-accent"><i class="fas fa-circle-info"></i><span>Informasi resmi PESOMA</span></div>
                        <h2><?= e($a['title']) ?></h2>
                        <p class="muted"><?= e(date('d M Y H:i', strtotime($a['published_at']))) ?> WIB</p>
                        <p><?= nl2br(e(mb_strimwidth((string) $a['content'], 0, 180, '...'))) ?></p>
                        <a class="btn" href="detail-pengumuman.php?id=<?= (int) $a['id'] ?>">Baca Detail</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<section class="section">
    <div class="container stack">
        <div class="section-head">
            <div class="section-tag">Finalis</div>
            <h2 class="section-title">Daftar Finalis Terpublikasi</h2>
            <p class="section-desc">Berikut daftar finalis yang telah diumumkan secara resmi oleh panitia PESOMA 2026.</p>
        </div>
        <div class="card">
            <?php if (!$finalists): ?>
                <div class="empty-state">Belum ada data finalis yang dipublikasikan.</div>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Cabang</th>
                                <th>Rank</th>
                                <th>No Peserta</th>
                                <th>Nama</th>
                                <th>Fakultas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($finalists as $f): ?>
                                <tr>
                                    <td><?= e($f['nama_lomba']) ?></td>
                                    <td><?= e((string) ($f['rank_penyisihan'] ?: '-')) ?></td>
                                    <td><?= e($f['nomor_peserta']) ?></td>
                                    <td><?= e($f['nama']) ?><br><small class="muted"><?= e($f['nim']) ?></small></td>
                                    <td><?= e($f['fakultas']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<section class="section">
    <div class="container stack">
        <div class="section-head">
            <div class="section-tag">Pemenang</div>
            <h2 class="section-title">Daftar Pemenang Terpublikasi</h2>
            <p class="section-desc">Daftar juara resmi PESOMA 2026 yang telah disahkan dan dipublikasikan panitia.</p>
        </div>
        <div class="card">
            <?php if (!$winners): ?>
                <div class="empty-state">Belum ada data pemenang yang dipublikasikan.</div>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Cabang</th>
                                <th>Juara</th>
                                <th>No Peserta</th>
                                <th>Nama</th>
                                <th>Total Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($winners as $w): ?>
                                <tr>
                                    <td><?= e($w['nama_lomba']) ?></td>
                                    <td><span class="badge winner">Juara <?= (int) $w['juara_ke'] ?></span></td>
                                    <td><?= e($w['nomor_peserta']) ?></td>
                                    <td><?= e($w['nama']) ?><br><small class="muted"><?= e($w['nim'] . ' · ' . $w['fakultas']) ?></small></td>
                                    <td><?= e($w['total_score'] !== null ? (string) $w['total_score'] : '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php public_footer(); ?>