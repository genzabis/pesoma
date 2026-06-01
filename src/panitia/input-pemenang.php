<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        flash('error', 'Token keamanan tidak valid.');
        redirect('input-pemenang.php');
    }
    $competitionId = (int)($_POST['competition_id'] ?? 0);
    $ranks = [1 => (int)($_POST['juara_1'] ?? 0), 2 => (int)($_POST['juara_2'] ?? 0), 3 => (int)($_POST['juara_3'] ?? 0)];
    $chosen = array_filter($ranks);
    if ($competitionId <= 0 || count($chosen) !== count(array_unique($chosen))) {
        flash('error', 'Pilihan juara tidak valid atau duplikat.');
        redirect('input-pemenang.php?competition_id=' . $competitionId);
    }
    db_query('DELETE FROM winners WHERE competition_id=?', [$competitionId]);
    foreach ($ranks as $rank => $registrationId) {
        if ($registrationId <= 0) continue;
        $score = db_fetch('SELECT COALESCE(AVG(total),0) total FROM scores_final WHERE registration_id=?', [$registrationId]);
        db_query('INSERT INTO winners (registration_id, competition_id, juara_ke, total_score, published_by, announced_at) VALUES (?, ?, ?, ?, ?, NOW())', [$registrationId, $competitionId, $rank, (float)($score['total'] ?? 0), panitia_user_id()]);
    }
    db_query('INSERT INTO announcements (title, content, type, published_by, published_at) VALUES (?, ?, "winner", ?, NOW())', ['Pengumuman Pemenang PESOMA 2026', 'Pemenang cabang lomba telah dipublikasikan pada halaman pengumuman.', panitia_user_id()]);
    log_activity(panitia_user_id(), ROLE_PANITIA, 'publish_winners', 'Publikasi pemenang competition #' . $competitionId);
    flash('success', 'Pemenang berhasil dipublikasikan.');
    redirect('input-pemenang.php?competition_id=' . $competitionId);
}

$competitions = panitia_competitions();
$competitionId = (int)($_GET['competition_id'] ?? ($competitions[0]['id'] ?? 0));
$finalists = $competitionId ? db_fetch_all('SELECT r.id registration_id, r.nomor_peserta, u.nama, u.nim, COALESCE(AVG(sf.total),0) nilai_final, w.juara_ke FROM finalists f JOIN registrations r ON r.id=f.registration_id JOIN users u ON u.id=r.user_id LEFT JOIN scores_final sf ON sf.registration_id=r.id LEFT JOIN winners w ON w.registration_id=r.id WHERE f.competition_id=? GROUP BY r.id, r.nomor_peserta, u.nama, u.nim, w.juara_ke ORDER BY nilai_final DESC, f.rank_penyisihan ASC', [$competitionId]) : [];
$current = [1 => 0, 2 => 0, 3 => 0];
foreach ($finalists as $f) {
    if ($f['juara_ke']) $current[(int)$f['juara_ke']] = (int)$f['registration_id'];
}
panitia_header('Input Pemenang', 'input-pemenang.php');
?>
<section class="card">
    <form class="filters" method="GET">
        <div class="field"><label>Cabang Lomba</label><select name="competition_id"><?php foreach ($competitions as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $competitionId === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['nama_lomba']) ?></option><?php endforeach; ?></select></div><button class="btn" type="submit">Tampilkan</button>
    </form>
</section>
<section class="card">
    <form method="POST"><?= csrf_field() ?><input type="hidden" name="competition_id" value="<?= $competitionId ?>">
        <div class="form-grid"><?php for ($i = 1; $i <= 3; $i++): ?><div class="field"><label>Juara <?= $i ?></label><select name="juara_<?= $i ?>">
                        <option value="0">-- pilih finalis --</option><?php foreach ($finalists as $f): ?><option value="<?= (int)$f['registration_id'] ?>" <?= $current[$i] === (int)$f['registration_id'] ? 'selected' : '' ?>><?= e($f['nomor_peserta'] . ' - ' . $f['nama'] . ' (Nilai ' . number_format((float)$f['nilai_final'], 2) . ')') ?></option><?php endforeach; ?>
                    </select></div><?php endfor; ?></div><button class="btn" type="submit">Publikasikan Pemenang</button>
    </form>
</section>
<section class="card">
    <h2>Daftar Finalis & Nilai Final</h2>
    <table class="table">
        <thead>
            <tr>
                <th>No Peserta</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Nilai Final</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody><?php foreach ($finalists as $f): ?><tr>
                    <td><?= e($f['nomor_peserta']) ?></td>
                    <td><?= e($f['nama']) ?></td>
                    <td><?= e($f['nim']) ?></td>
                    <td><?= number_format((float)$f['nilai_final'], 2) ?></td>
                    <td><?= $f['juara_ke'] ? '<span class="badge ok">Juara ' . (int)$f['juara_ke'] . '</span>' : '<span class="badge pending">Finalis</span>' ?></td>
                </tr><?php endforeach; ?></tbody>
    </table>
</section>
<?php panitia_footer(); ?>