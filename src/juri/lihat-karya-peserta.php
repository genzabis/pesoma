<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
$submissionId = (int)($_GET['submission_id'] ?? 0);
$row = db_fetch('SELECT s.*,r.nomor_peserta,u.nama,u.nim,u.fakultas,c.nama_lomba FROM submissions s JOIN registrations r ON r.id=s.registration_id JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=r.competition_id WHERE s.id=?', [$submissionId]);
juri_header('Lihat Karya Peserta', 'lihat-karya-peserta.php');
$files = $row ? json_decode((string)$row['file_paths'], true) : [];
$names = $row ? json_decode((string)$row['original_names'], true) : [];
?>
<div class="grid">
    <section class="card span-12">
        <?php if (!$row): ?>
            <div style="text-align:center;padding:60px 20px;color:var(--text-secondary)">
                <i class="fas fa-file-slash" style="font-size:64px;margin-bottom:16px;opacity:.3"></i>
                <h2 style="margin:0">Karya tidak ditemukan</h2>
                <p>Submission ID tidak valid atau telah dihapus</p>
                <a class="btn secondary" href="penilaian-penyisihan.php" style="margin-top:16px">Kembali ke Penilaian</a>
            </div>
        <?php else: ?>
            <div style="margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--border)">
                <h2 style="margin-top:0;margin-bottom:8px"><?= e($row['nomor_peserta']) ?> - <?= e($row['nama']) ?></h2>
                <p class="muted" style="margin:0"><?= e($row['nim']) ?> · <?= e($row['fakultas']) ?> · <?= e($row['nama_lomba']) ?></p>
            </div>

            <h3 style="margin-bottom:12px">File Karya</h3>
            <?php if (!$files || count($files) === 0): ?>
                <div style="text-align:center;padding:40px;background:rgba(0,0,0,.02);border-radius:8px;color:var(--text-secondary)">
                    <i class="fas fa-inbox" style="font-size:48px;margin-bottom:12px;opacity:.3"></i>
                    <p>Tidak ada file karya yang diupload</p>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama File</th>
                            <th style="text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ((array)$files as $i => $path): ?>
                            <tr>
                                <td>
                                    <strong><?= e($names[$i] ?? basename((string)$path)) ?></strong><br>
                                    <small class="muted"><?= e((string)$path) ?></small>
                                </td>
                                <td style="text-align:center">
                                    <a class="btn small" target="_blank" href="<?= e(APP_URL . '/' . ltrim((string)$path, '/')) ?>">
                                        <i class="fas fa-download"></i> Buka/Download
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border)">
                <a class="btn secondary" href="penilaian-penyisihan.php">← Kembali ke Penilaian</a>
            </div>
        <?php endif; ?>
    </section>
</div>
<?php juri_footer(); ?>
