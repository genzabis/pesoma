<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        flash('error', 'Token keamanan tidak valid.');
        redirect('tentukan-finalis.php');
    }
    $competitionId = (int)($_POST['competition_id'] ?? 0);
    $selected = array_map('intval', $_POST['finalists'] ?? []);
    if ($competitionId <= 0 || !$selected) {
        flash('error', 'Pilih cabang lomba dan minimal satu finalis.');
        redirect('tentukan-finalis.php?competition_id=' . $competitionId);
    }
    db_query('DELETE FROM finalists WHERE competition_id = ?', [$competitionId]);
    foreach ($selected as $rank => $registrationId) {
        db_query('INSERT INTO finalists (registration_id, competition_id, rank_penyisihan, published_by, announced_at) VALUES (?, ?, ?, ?, NOW())', [$registrationId, $competitionId, $rank + 1, panitia_user_id()]);
    }
    db_query('INSERT INTO announcements (title, content, type, published_by, published_at) VALUES (?, ?, "finalis", ?, NOW())', ['Pengumuman Finalis PESOMA 2026', 'Finalis cabang lomba terpilih telah dipublikasikan. Silakan cek daftar finalis pada halaman pengumuman.', panitia_user_id()]);
    log_activity(panitia_user_id(), ROLE_PANITIA, 'publish_finalists', 'Publikasi finalis competition #' . $competitionId);
    flash('success', 'Finalis berhasil dipublikasikan.');
    redirect('tentukan-finalis.php?competition_id=' . $competitionId);
}

$competitions = panitia_competitions();
$competitionId = (int)($_GET['competition_id'] ?? ($competitions[0]['id'] ?? 0));
$rows = $competitionId ? db_fetch_all('SELECT r.id registration_id, r.nomor_peserta, u.nim, u.nama, c.nama_lomba, COALESCE(AVG(sp.total),0) nilai_penyisihan, COUNT(sp.id) jumlah_juri, f.id finalist_id FROM registrations r JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=r.competition_id LEFT JOIN submissions s ON s.registration_id=r.id LEFT JOIN scores_penyisihan sp ON sp.submission_id=s.id LEFT JOIN finalists f ON f.registration_id=r.id WHERE r.competition_id=? AND r.status_verifikasi="diterima" GROUP BY r.id, r.nomor_peserta, u.nim, u.nama, c.nama_lomba, f.id ORDER BY nilai_penyisihan DESC, r.created_at ASC', [$competitionId]) : [];
panitia_header('Tentukan Finalis', 'tentukan-finalis.php');
?>
<section class="card">
    <form class="filters" method="GET">
        <div class="field"><label>Cabang Lomba</label><select name="competition_id"><?php foreach ($competitions as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $competitionId === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['nama_lomba']) ?></option><?php endforeach; ?></select></div><button class="btn" type="submit">Tampilkan</button>
    </form>
</section>
<section class="card">
    <form method="POST"><?= csrf_field() ?><input type="hidden" name="competition_id" value="<?= $competitionId ?>">
        <div class="actions" style="margin-bottom:12px"><button class="btn" type="submit">Publikasikan Finalis</button><span class="muted">Centang peserta yang lolos final.</span></div>
        <table class="table">
            <thead>
                <tr>
                    <th>Pilih</th>
                    <th>Peringkat</th>
                    <th>No Peserta</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Nilai Penyisihan</th>
                    <th>Juri</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody><?php foreach ($rows as $i => $r): ?><tr>
                        <td><input type="checkbox" name="finalists[]" value="<?= (int)$r['registration_id'] ?>" <?= $r['finalist_id'] ? 'checked' : '' ?>></td>
                        <td class="rank">#<?= $i + 1 ?></td>
                        <td><?= e($r['nomor_peserta']) ?></td>
                        <td><?= e($r['nim']) ?></td>
                        <td><?= e($r['nama']) ?></td>
                        <td><?= number_format((float)$r['nilai_penyisihan'], 2) ?></td>
                        <td><?= (int)$r['jumlah_juri'] ?></td>
                        <td><?= $r['finalist_id'] ? '<span class="badge ok">Finalis</span>' : '<span class="badge pending">Belum</span>' ?></td>
                    </tr><?php endforeach; ?></tbody>
        </table>
    </form>
</section>
<?php panitia_footer(); ?>