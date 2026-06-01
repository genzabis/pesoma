<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
$rows = db_fetch_all('SELECT s.*, r.nomor_peserta, u.nama, c.nama_lomba FROM submissions s JOIN registrations r ON r.id=s.registration_id JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=r.competition_id ORDER BY s.uploaded_at DESC');
panitia_header('Daftar Karya', 'daftar-karya.php');
?>
<section class="card">
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>No Peserta</th>
                <th>Nama</th>
                <th>Cabang Lomba</th>
                <th>Tanggal Upload</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody><?php foreach ($rows as $i => $r): ?><tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($r['nomor_peserta']) ?></td>
                    <td><?= e($r['nama']) ?></td>
                    <td><?= e($r['nama_lomba']) ?></td>
                    <td><?= e(date('d M Y H:i', strtotime($r['uploaded_at']))) ?></td>
                    <td><a class="btn small" href="lihat-karya.php?id=<?= (int)$r['id'] ?>">Lihat Karya</a></td>
                </tr><?php endforeach; ?></tbody>
    </table>
</section>
<?php panitia_footer(); ?>