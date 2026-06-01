<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
$editId = (int)($_GET['edit'] ?? 0);
$edit = $editId ? db_fetch('SELECT * FROM users WHERE id=?', [$editId]) : null;
$users = db_fetch_all('SELECT * FROM users ORDER BY role,nama');
admin_header('Kelola User', 'kelola-user.php');
?>
<section class="card">
    <h2><?= $edit ? 'Edit User' : 'Tambah User Panitia/Juri/Admin' ?></h2>
    <form class="two" method="POST" action="proses-user.php"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
        <div class="field"><label>Nama</label><input name="nama" value="<?= e($edit['nama'] ?? '') ?>" required></div>
        <div class="field"><label>Email</label><input type="email" name="email" value="<?= e($edit['email'] ?? '') ?>" required></div>
        <div class="field"><label>NIM/NIP</label><input name="nim" value="<?= e($edit['nim'] ?? '') ?>"></div>
        <div class="field"><label>Fakultas</label><select name="fakultas">
                <option value="">-</option><?php foreach (ALLOWED_FAKULTAS as $f): ?><option value="<?= e($f) ?>" <?= ($edit['fakultas'] ?? '') === $f ? 'selected' : '' ?>><?= e($f) ?></option><?php endforeach; ?>
            </select></div>
        <div class="field"><label>Role</label><select name="role" required><?php foreach ([ROLE_ADMIN, ROLE_PANITIA, ROLE_JURI, ROLE_PESERTA] as $r): ?><option value="<?= e($r) ?>" <?= ($edit['role'] ?? ROLE_PANITIA) === $r ? 'selected' : '' ?>><?= e($r) ?></option><?php endforeach; ?></select></div>
        <div class="field"><label>Password <?= $edit ? '(kosongkan jika tidak diubah)' : '' ?></label><input type="password" name="password" <?= $edit ? '' : 'required' ?>></div>
        <div class="field"><label>Status</label><select name="is_active">
                <option value="1" <?= (int)($edit['is_active'] ?? 1) === 1 ? 'selected' : '' ?>>Aktif</option>
                <option value="0" <?= isset($edit['is_active']) && (int)$edit['is_active'] === 0 ? 'selected' : '' ?>>Nonaktif</option>
            </select></div>
        <div class="field"><label>&nbsp;</label><button class="btn" type="submit">Simpan User</button> <?php if ($edit): ?><a class="btn secondary" href="kelola-user.php">Batal</a><?php endif; ?></div>
    </form>
</section>
<section class="card">
    <h2>Semua User</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Fakultas</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody><?php foreach ($users as $u): ?><tr>
                    <td><?= e($u['nama']) ?><br><small class="muted"><?= e($u['nim'] ?? '-') ?></small></td>
                    <td><?= e($u['email']) ?></td>
                    <td><span class="badge info"><?= e($u['role']) ?></span></td>
                    <td><?= e($u['fakultas'] ?? '-') ?></td>
                    <td><?= (int)$u['is_active'] ? '<span class="badge ok">Aktif</span>' : '<span class="badge no">Nonaktif</span>' ?></td>
                    <td class="actions"><a class="btn small secondary" href="kelola-user.php?edit=<?= (int)$u['id'] ?>">Edit</a>
                        <form method="POST" action="proses-user.php" onsubmit="return confirm('Reset password user ini?')"><?= csrf_field() ?><input type="hidden" name="action" value="reset"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><button class="btn small warn">Reset Password</button></form>
                        <form method="POST" action="hapus-user.php" onsubmit="return confirm('Hapus user ini?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><button class="btn small danger">Hapus</button></form>
                    </td>
                </tr><?php endforeach; ?></tbody>
    </table>
</section>
<?php admin_footer(); ?>