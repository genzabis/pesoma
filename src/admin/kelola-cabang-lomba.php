<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
$editId = (int)($_GET['edit'] ?? 0);
$edit = $editId ? db_fetch('SELECT * FROM competitions WHERE id=?', [$editId]) : null;
$rows = db_fetch_all('SELECT * FROM competitions ORDER BY is_active DESC,nama_lomba');
admin_header('Kelola Cabang Lomba', 'kelola-cabang-lomba.php');
?>
<section class="card">
    <h2><?= $edit ? 'Edit' : 'Tambah' ?> Cabang Lomba</h2>
    <form class="two" method="POST" action="proses-cabang-lomba.php"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
        <div class="field"><label>Kode Lomba</label><input name="kode_lomba" value="<?= e($edit['kode_lomba'] ?? '') ?>" required></div>
        <div class="field"><label>Nama Lomba</label><input name="nama_lomba" value="<?= e($edit['nama_lomba'] ?? '') ?>" required></div>
        <div class="field"><label>Jenis</label><select name="jenis">
                <option value="individu" <?= ($edit['jenis'] ?? '') === 'individu' ? 'selected' : '' ?>>Individu</option>
                <option value="tim" <?= ($edit['jenis'] ?? '') === 'tim' ? 'selected' : '' ?>>Tim</option>
            </select></div>
        <div class="field"><label>Kategori</label><input name="kategori" value="<?= e($edit['kategori'] ?? '') ?>" placeholder="mis. Seni Visual, Olahraga"></div>
        <div class="field"><label>Min Anggota</label><input type="number" name="min_anggota" min="1" value="<?= e((string)($edit['min_anggota'] ?? 1)) ?>"></div>
        <div class="field"><label>Max Anggota</label><input type="number" name="max_anggota" min="1" value="<?= e((string)($edit['max_anggota'] ?? 1)) ?>"></div>
        <div class="field"><label>Butuh Mentor</label><select name="need_mentor">
                <option value="0">Tidak</option>
                <option value="1" <?= (int)($edit['need_mentor'] ?? $edit['requires_mentor'] ?? 0) === 1 ? 'selected' : '' ?>>Ya</option>
            </select></div>
        <div class="field"><label>Penyisihan</label><select name="has_penyisihan">
                <option value="1" <?= (int)($edit['has_penyisihan'] ?? 1) === 1 ? 'selected' : '' ?>>Ya</option>
                <option value="0">Tidak</option>
            </select></div>
        <div class="field"><label>Final</label><select name="has_final">
                <option value="1" <?= (int)($edit['has_final'] ?? 1) === 1 ? 'selected' : '' ?>>Ya</option>
                <option value="0">Tidak</option>
            </select></div>
        <div class="field"><label>Aktif</label><select name="is_active">
                <option value="1" <?= (int)($edit['is_active'] ?? 1) === 1 ? 'selected' : '' ?>>Aktif</option>
                <option value="0">Nonaktif</option>
            </select></div>
        <div class="field"><label>Deskripsi</label><textarea name="deskripsi" rows="3"><?= e($edit['deskripsi'] ?? '') ?></textarea></div>
        <div class="field"><label>Aturan</label><textarea name="aturan" rows="3"><?= e($edit['aturan'] ?? '') ?></textarea></div>
        <div class="field"><label>&nbsp;</label><button class="btn">Simpan Cabang</button> <?php if ($edit): ?><a class="btn secondary" href="kelola-cabang-lomba.php">Batal</a><?php endif; ?></div>
    </form>
</section>
<section class="card">
    <h2>Daftar Cabang Lomba</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Jenis</th>
                <th>Anggota</th>
                <th>Mentor</th>
                <th>Babak</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody><?php foreach ($rows as $r): ?><tr>
                    <td><?= e($r['kode_lomba']) ?></td>
                    <td><?= e($r['nama_lomba']) ?></td>
                    <td><?= e($r['jenis']) ?></td>
                    <td><?= e((string)($r['min_anggota'] ?? 1)) ?>-<?= e((string)$r['max_anggota']) ?></td>
                    <td><?= (int)($r['need_mentor'] ?? $r['requires_mentor'] ?? 0) ? 'Ya' : 'Tidak' ?></td>
                    <td><?= (int)($r['has_penyisihan'] ?? 1) ? 'Penyisihan ' : '' ?><?= (int)($r['has_final'] ?? 1) ? 'Final' : '' ?></td>
                    <td><?= (int)$r['is_active'] ? '<span class="badge ok">Aktif</span>' : '<span class="badge no">Nonaktif</span>' ?></td>
                    <td><a class="btn small secondary" href="?edit=<?= (int)$r['id'] ?>">Edit</a>
                        <form style="display:inline" method="POST" action="proses-cabang-lomba.php" onsubmit="return confirm('Hapus cabang lomba?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn small danger">Hapus</button></form>
                    </td>
                </tr><?php endforeach; ?></tbody>
    </table>
</section>
<?php admin_footer(); ?>