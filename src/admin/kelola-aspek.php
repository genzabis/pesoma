<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
$competitions = db_fetch_all('SELECT id,nama_lomba FROM competitions WHERE is_active=1 ORDER BY nama_lomba');
$competitionId = (int)($_GET['competition_id'] ?? ($competitions[0]['id'] ?? 0));
$babak = $_GET['babak'] ?? 'penyisihan';
$editId = (int)($_GET['edit'] ?? 0);
$edit = $editId ? db_fetch('SELECT * FROM aspek_penilaian WHERE id=?', [$editId]) : null;
$rows = $competitionId ? db_fetch_all('SELECT * FROM aspek_penilaian WHERE competition_id=? AND babak=? ORDER BY urutan,id', [$competitionId, $babak]) : [];
$total = (float)(db_fetch('SELECT COALESCE(SUM(bobot_persen),0) total FROM aspek_penilaian WHERE competition_id=? AND babak=?', [$competitionId, $babak])['total'] ?? 0);
admin_header('Kelola Aspek Penilaian', 'kelola-aspek.php');
?>
<section class="card">
    <form class="filters" method="GET">
        <div class="field"><label>Cabang Lomba</label><select name="competition_id"><?php foreach ($competitions as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $competitionId === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['nama_lomba']) ?></option><?php endforeach; ?></select></div>
        <div class="field"><label>Babak</label><select name="babak">
                <option value="penyisihan" <?= $babak === 'penyisihan' ? 'selected' : '' ?>>Penyisihan</option>
                <option value="final" <?= $babak === 'final' ? 'selected' : '' ?>>Final</option>
            </select></div><button class="btn">Tampilkan</button>
    </form>
    <p>Total bobot: <b><?= $total ?>%</b> <?= abs($total - 100) < 0.01 ? '<span class="badge ok">Valid</span>' : '<span class="badge no">Harus 100%</span>' ?></p>
</section>
<section class="card">
    <h2><?= $edit ? 'Edit' : 'Tambah' ?> Aspek</h2>
    <form class="two" method="POST" action="proses-aspek.php"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>"><input type="hidden" name="competition_id" value="<?= $competitionId ?>"><input type="hidden" name="babak" value="<?= e($babak) ?>">
        <div class="field"><label>Nama Aspek</label><input name="aspek_name" value="<?= e($edit['aspek_name'] ?? '') ?>" required></div>
        <div class="field"><label>Bobot (%)</label><input type="number" step="0.01" min="0" max="100" name="bobot_persen" value="<?= e((string)($edit['bobot_persen'] ?? 0)) ?>" required></div>
        <div class="field"><label>Urutan</label><input type="number" min="1" name="urutan" value="<?= e((string)($edit['urutan'] ?? 1)) ?>"></div>
        <div class="field"><label>&nbsp;</label><button class="btn">Simpan Aspek</button></div>
    </form>
</section>
<section class="card">
    <h2>Daftar Aspek</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Urutan</th>
                <th>Aspek</th>
                <th>Bobot</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody><?php foreach ($rows as $r): ?><tr>
                    <td><?= e((string)$r['urutan']) ?></td>
                    <td><?= e($r['aspek_name']) ?></td>
                    <td><?= e((string)$r['bobot_persen']) ?>%</td>
                    <td><a class="btn small secondary" href="?competition_id=<?= $competitionId ?>&babak=<?= e($babak) ?>&edit=<?= (int)$r['id'] ?>">Edit</a>
                        <form style="display:inline" method="POST" action="proses-aspek.php" onsubmit="return confirm('Hapus aspek?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="competition_id" value="<?= $competitionId ?>"><input type="hidden" name="babak" value="<?= e($babak) ?>"><button class="btn small danger">Hapus</button></form>
                    </td>
                </tr><?php endforeach; ?></tbody>
    </table>
</section>
<?php admin_footer(); ?>