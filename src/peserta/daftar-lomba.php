<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

$jenis = $_GET['jenis'] ?? '';
$params = [];
$where = 'WHERE is_active = 1';
if (in_array($jenis, ['individu', 'tim'], true)) {
    $where .= ' AND jenis = ?';
    $params[] = $jenis;
}
$competitions = db_fetch_all("SELECT * FROM competitions {$where} ORDER BY kategori, nama_lomba", $params);
$registered = db_fetch_all('SELECT competition_id FROM registrations WHERE user_id = ?', [current_user_id()]);
$registeredIds = array_map(static fn($row) => (int) $row['competition_id'], $registered);

peserta_header('Daftar Lomba', 'daftar-lomba.php');
?>
<section class="card span-12">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:24px;margin-bottom:24px;flex-wrap:wrap">
        <div>
            <h2><?= count($competitions) ?> Cabang Lomba</h2>
            <p class="muted">Pilih cabang lomba yang ingin Anda ikuti.</p>
        </div>
        <div class="actions">
            <a class="btn secondary" href="daftar-lomba.php">Semua</a>
            <a class="btn secondary" href="?jenis=individu">Individu</a>
            <a class="btn secondary" href="?jenis=tim">Tim</a>
        </div>
    </div>
    <div class="grid">
        <?php foreach ($competitions as $competition): ?>
            <article class="card span-4">
                <div style="display:flex;gap:8px;margin-bottom:12px">
                    <span class="badge ok"><?= e($competition['jenis']) ?></span>
                    <span class="badge pending"><?= e($competition['kategori']) ?></span>
                </div>
                <h3 style="margin-top:0;margin-bottom:8px"><?= e($competition['nama_lomba']) ?></h3>
                <p class="muted" style="margin-bottom:12px"><?= e(mb_strimwidth((string) ($competition['deskripsi'] ?? ''), 0, 100, '...')) ?></p>
                <p style="font-size:13px;margin-bottom:16px">
                    <strong>Maks anggota:</strong> <?= (int) $competition['max_anggota'] ?>
                    <?= (int) $competition['requires_mentor'] === 1 ? '<br><strong>Wajib pendamping</strong>' : '' ?>
                </p>
                <?php if (in_array((int) $competition['id'], $registeredIds, true)): ?>
                    <span class="btn secondary" style="display:block;text-align:center">✓ Sudah Terdaftar</span>
                <?php else: ?>
                    <a class="btn" href="form-pendaftaran.php?competition_id=<?= (int) $competition['id'] ?>" style="display:block;text-align:center">Daftar Sekarang</a>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php peserta_footer(); ?>
