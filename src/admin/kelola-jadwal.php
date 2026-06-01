<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        flash('error', 'Token tidak valid.');
        admin_redirect('kelola-jadwal.php');
    }
    $id = (int)($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'delete') {
        db_query('DELETE FROM schedules WHERE id=?', [$id]);
        flash('success', 'Jadwal dihapus.');
        admin_redirect('kelola-jadwal.php');
    }
    $params = [trim($_POST['event_name']), $_POST['event_date'], $_POST['event_time'] ?: null, trim($_POST['location'] ?? ''), trim($_POST['link'] ?? ''), trim($_POST['description'] ?? ''), (int)($_POST['is_public'] ?? 1)];
    if ($id) {
        $params[] = $id;
        db_query('UPDATE schedules SET event_name=?, event_date=?, event_time=?, location=?, link=?, description=?, is_public=?, updated_at=CURRENT_TIMESTAMP WHERE id=?', $params);
        flash('success', 'Jadwal diperbarui.');
    } else {
        db_query('INSERT INTO schedules (event_name,event_date,event_time,location,link,description,is_public) VALUES (?,?,?,?,?,?,?)', $params);
        flash('success', 'Jadwal ditambahkan.');
    }
    log_activity(admin_id(), ROLE_ADMIN, 'save_schedule', 'Kelola jadwal kegiatan');
    admin_redirect('kelola-jadwal.php');
}

$editId = (int)($_GET['edit'] ?? 0);
$edit = $editId ? db_fetch('SELECT * FROM schedules WHERE id=?', [$editId]) : null;
$rows = db_fetch_all('SELECT * FROM schedules ORDER BY event_date,event_time');
admin_header('Kelola Jadwal', 'kelola-jadwal.php');
?>
<section class="card">
    <h2><?= $edit ? 'Edit' : 'Tambah' ?> Jadwal</h2>
    <form class="two" method="POST"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
        <div class="field"><label>Nama Kegiatan</label><input name="event_name" value="<?= e($edit['event_name'] ?? '') ?>" required></div>
        <div class="field"><label>Tanggal</label><input type="date" name="event_date" value="<?= e($edit['event_date'] ?? '') ?>" required></div>
        <div class="field"><label>Waktu</label><input type="time" name="event_time" value="<?= e($edit['event_time'] ?? '') ?>"></div>
        <div class="field"><label>Lokasi</label><input name="location" value="<?= e($edit['location'] ?? '') ?>"></div>
        <div class="field"><label>Link</label><input name="link" value="<?= e($edit['link'] ?? '') ?>"></div>
        <div class="field"><label>Publik</label><select name="is_public">
                <option value="1" <?= (int)($edit['is_public'] ?? 1) === 1 ? 'selected' : '' ?>>Ya</option>
                <option value="0">Tidak</option>
            </select></div>
        <div class="field"><label>Deskripsi</label><textarea name="description" rows="3"><?= e($edit['description'] ?? '') ?></textarea></div>
        <div class="field"><label>&nbsp;</label><button class="btn">Simpan Jadwal</button></div>
    </form>
</section>
<section class="card">
    <h2>Daftar Jadwal</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Kegiatan</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Lokasi/Link</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody><?php foreach ($rows as $r): ?><tr>
                    <td><?= e($r['event_name']) ?><br><small><?= e($r['description'] ?? '') ?></small></td>
                    <td><?= e($r['event_date']) ?></td>
                    <td><?= e($r['event_time'] ?? '-') ?></td>
                    <td><?= e($r['location'] ?? '-') ?><br><?= e($r['link'] ?? '') ?></td>
                    <td><?= (int)$r['is_public'] ? '<span class="badge ok">Publik</span>' : '<span class="badge no">Draft</span>' ?></td>
                    <td><a class="btn small secondary" href="?edit=<?= (int)$r['id'] ?>">Edit</a>
                        <form style="display:inline" method="POST" onsubmit="return confirm('Hapus jadwal?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn small danger">Hapus</button></form>
                    </td>
                </tr><?php endforeach; ?></tbody>
    </table>
</section>
<?php admin_footer(); ?>