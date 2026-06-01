<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

$jenis  = $_GET['jenis'] ?? '';
$params = [];
$where  = 'WHERE is_active = 1';
if (in_array($jenis, ['individu', 'tim'], true)) {
    $where .= ' AND jenis = ?';
    $params[] = $jenis;
}
$competitions  = db_fetch_all("SELECT * FROM competitions {$where} ORDER BY kategori, nama_lomba", $params);
$registered    = db_fetch_all('SELECT competition_id FROM registrations WHERE user_id = ?', [current_user_id()]);
$registeredIds = array_map(static fn($row) => (int) $row['competition_id'], $registered);

peserta_header('Daftar Lomba', 'daftar-lomba.php');
?>

<div class="section-head">
    <span class="section-eyebrow"><?= count($competitions) ?> Cabang Tersedia</span>
    <h2 class="section-title">Pilih cabang lomba.</h2>
</div>

<div class="actions" style="margin: 0 0 28px;">
    <a class="btn <?= $jenis === '' ? '' : 'secondary' ?>" href="daftar-lomba.php">Semua</a>
    <a class="btn <?= $jenis === 'individu' ? '' : 'secondary' ?>" href="?jenis=individu">Individu</a>
    <a class="btn <?= $jenis === 'tim' ? '' : 'secondary' ?>" href="?jenis=tim">Tim</a>
</div>

<?php if (!$competitions): ?>
    <div class="card">
        <p class="muted" style="margin: 0;">Belum ada cabang lomba aktif.</p>
    </div>
<?php else: ?>
    <div class="grid">
        <?php foreach ($competitions as $competition): ?>
            <?php $isRegistered = in_array((int) $competition['id'], $registeredIds, true); ?>
            <article class="card span-4">
                <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom: 14px;">
                    <span class="badge neutral"><?= e($competition['jenis']) ?></span>
                    <span class="badge info"><?= e($competition['kategori'] ?: 'Umum') ?></span>
                    <?php if ($isRegistered): ?>
                        <span class="badge ok">Terdaftar</span>
                    <?php endif; ?>
                </div>

                <h3 style="margin: 0 0 8px;"><?= e($competition['nama_lomba']) ?></h3>
                <p class="muted" style="margin: 0 0 16px; line-height: 1.55;"><?= e(mb_strimwidth((string) ($competition['deskripsi'] ?? ''), 0, 110, '…')) ?: 'Detail tersedia di halaman publik cabang lomba.' ?></p>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 8px 16px; padding: 12px 0; margin: 0 0 18px; border-top: 1px solid var(--c-line); border-bottom: 1px solid var(--c-line);">
                    <div>
                        <div style="font-family: var(--ff-mono); font-size: 10px; text-transform: uppercase; letter-spacing: .12em; color: var(--c-ink-mute);">Maks Anggota</div>
                        <div style="font-weight: 600;"><?= (int) $competition['max_anggota'] ?> orang</div>
                    </div>
                    <div>
                        <div style="font-family: var(--ff-mono); font-size: 10px; text-transform: uppercase; letter-spacing: .12em; color: var(--c-ink-mute);">Pendamping</div>
                        <div style="font-weight: 600;"><?= (int) $competition['requires_mentor'] === 1 ? 'Wajib' : 'Tidak' ?></div>
                    </div>
                </div>

                <?php if ($isRegistered): ?>
                    <a class="btn secondary" href="status-pendaftaran.php" style="width: 100%;">Lihat Status</a>
                <?php else: ?>
                    <a class="btn" href="form-pendaftaran.php?competition_id=<?= (int) $competition['id'] ?>" style="width: 100%;">Daftar Sekarang</a>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php peserta_footer(); ?>
