<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        flash('error', 'Token keamanan tidak valid.');
        redirect('buat-pengumuman.php');
    }
    $id = (int)($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'delete' && $id > 0) {
        db_query('DELETE FROM announcements WHERE id=?', [$id]);
        flash('success', 'Pengumuman dihapus.');
        redirect('buat-pengumuman.php');
    }
    $title = trim((string)$_POST['title']);
    $content = trim((string)$_POST['content']);
    $type = $_POST['type'] ?? 'umum';
    $pub = isset($_POST['is_published']) ? 1 : 0;
    if ($title === '' || $content === '' || !in_array($type, ['umum', 'finalis', 'winner'], true)) {
        flash('error', 'Data pengumuman tidak valid.');
        redirect('buat-pengumuman.php');
    }
    if ($id > 0) db_query('UPDATE announcements SET title=?, content=?, type=?, is_published=?, published_by=? WHERE id=?', [$title, $content, $type, $pub, panitia_user_id(), $id]);
    else db_query('INSERT INTO announcements (title,content,type,is_published,published_by,published_at) VALUES (?,?,?,?,?,NOW())', [$title, $content, $type, $pub, panitia_user_id()]);
    log_activity(panitia_user_id(), ROLE_PANITIA, 'save_announcement', $title);
    flash('success', 'Pengumuman berhasil disimpan.');
    redirect('buat-pengumuman.php');
}

$edit = isset($_GET['edit']) ? db_fetch('SELECT * FROM announcements WHERE id=?', [(int)$_GET['edit']]) : null;
$rows = db_fetch_all('SELECT a.*, u.nama published_name FROM announcements a LEFT JOIN users u ON u.id=a.published_by ORDER BY a.published_at DESC');
panitia_header('Buat Pengumuman', 'buat-pengumuman.php');
?>
<section class="card">
    <h2><?= $edit ? 'Edit Pengumuman' : 'Pengumuman Baru' ?></h2>
    <form method="POST"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
        <div class="form-grid">
            <div class="field"><label>Judul</label><input name="title" required value="<?= e($edit['title'] ?? '') ?>"></div>
            <div class="field"><label>Tipe</label><select name="type"><?php foreach (['umum' => 'Umum', 'finalis' => 'Finalis', 'winner' => 'Pemenang'] as $k => $v): ?><option value="<?= $k ?>" <?= ($edit['type'] ?? 'umum') === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select></div>
            <div class="field"><label>Status</label><label><input type="checkbox" name="is_published" <?= !isset($edit['is_published']) || (int)$edit['is_published'] === 1 ? 'checked' : '' ?>> Publish</label></div>
        </div>
        <div class="field"><label>Isi Pengumuman</label><textarea name="content" rows="6" required><?= e($edit['content'] ?? '') ?></textarea></div><button class="btn" type="submit">Simpan Pengumuman</button> <?php if ($edit): ?><a class="btn secondary" href="buat-pengumuman.php">Batal</a><?php endif; ?>
    </form>
</section>
<section class="card">
    <h2>Riwayat Pengumuman</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Tipe</th>
                <th>Status</th>
                <th>Publish</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody><?php foreach ($rows as $r): ?><tr>
                    <td><b><?= e($r['title']) ?></b><br><small class="muted"><?= e(mb_strimwidth($r['content'], 0, 120, '...')) ?></small></td>
                    <td><span class="badge info"><?= e($r['type']) ?></span></td>
                    <td><?= (int)$r['is_published'] ? '<span class="badge ok">Published</span>' : '<span class="badge no">Draft</span>' ?></td>
                    <td><?= e(date('d M Y H:i', strtotime($r['published_at']))) ?><br><small class="muted"><?= e($r['published_name'] ?? '-') ?></small></td>
                    <td class="actions"><a class="btn small secondary" href="buat-pengumuman.php?edit=<?= (int)$r['id'] ?>">Edit</a>
                        <form method="POST" onsubmit="return confirm('Hapus pengumuman ini?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn small danger" name="action" value="delete">Hapus</button></form>
                    </td>
                </tr><?php endforeach; ?></tbody>
    </table>
</section>
<?php panitia_footer(); ?>