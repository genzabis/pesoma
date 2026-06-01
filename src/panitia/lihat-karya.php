<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
$id = (int)($_GET['id'] ?? 0);
$s = db_fetch('SELECT s.*, r.nomor_peserta, u.nama, c.nama_lomba FROM submissions s JOIN registrations r ON r.id=s.registration_id JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=r.competition_id WHERE s.id=?', [$id]);
if (!$s) {
    flash('error', 'Karya tidak ditemukan.');
    redirect('daftar-karya.php');
}
$files = panitia_json_array($s['file_paths']);
$names = panitia_json_array($s['original_names']);
panitia_header('Lihat Karya', 'daftar-karya.php');
?>
<section class="card">
    <h2><?= e($s['nama_lomba']) ?></h2>
    <p><b>No Peserta:</b> <?= e($s['nomor_peserta']) ?> · <b>Nama:</b> <?= e($s['nama']) ?></p>
    <table class="table">
        <tr>
            <th>Jenis File</th>
            <th>Nama Asli</th>
            <th>Aksi</th>
        </tr><?php foreach ($files as $key => $path): ?><tr>
                <td><?= e((string)$key) ?></td>
                <td><?= e($names[$key] ?? basename((string)$path)) ?></td>
                <td><a class="btn small" target="_blank" href="<?= e(APP_URL . '/' . ltrim((string)$path, '/')) ?>">Download/Lihat</a></td>
            </tr><?php endforeach; ?>
    </table>
</section>
<?php panitia_footer(); ?>