<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        flash('error', 'Token keamanan tidak valid.');
        redirect('kelola-jadwal.php');
    }
    $id = (int)($_POST['id'] ?? 0);
    $data = [trim((string)$_POST['event_name']), $_POST['event_date'], $_POST['event_time'] ?: null, trim((string)$_POST['location']), trim((string)$_POST['link']), trim((string)$_POST['description']), isset($_POST['is_public']) ? 1 : 0];
    if ($data[0] === '' || $data[1] === '') {
        flash('error', 'Nama event dan tanggal wajib diisi.');
        redirect('kelola-jadwal.php');
    }
    if (($_POST['action'] ?? '') === 'delete' && $id > 0) {
        db_query('DELETE FROM schedules WHERE id=?', [$id]);
        log_activity(panitia_user_id(), ROLE_PANITIA, 'delete_schedule', 'Hapus jadwal #' . $id);
        flash('success', 'Jadwal dihapus.');
        redirect('kelola-jadwal.php');
    }
    if ($id > 0) {
        db_query('UPDATE schedules SET event_name=?, event_date=?, event_time=?, location=?, link=?, description=?, is_public=? WHERE id=?', [...$data, $id]);
        log_activity(panitia_user_id(), ROLE_PANITIA, 'update_schedule', 'Update jadwal #' . $id);
    } else {
        db_query('INSERT INTO schedules (event_name,event_date,event_time,location,link,description,is_public) VALUES (?,?,?,?,?,?,?)', $data);
        log_activity(panitia_user_id(), ROLE_PANITIA, 'create_schedule', 'Tambah jadwal');
    }
    flash('success', 'Jadwal berhasil disimpan.');
    redirect('kelola-jadwal.php');
}

$edit = isset($_GET['edit']) ? db_fetch('SELECT * FROM schedules WHERE id=?', [(int)$_GET['edit']]) : null;
$rows = db_fetch_all('SELECT * FROM schedules ORDER BY event_date ASC, event_time ASC');
panitia_header('Kelola Jadwal', 'kelola-jadwal.php');
?>
<section class="card">
    <h2><?= $edit ? 'Edit Jadwal' : 'Tambah Jadwal' ?></h2>
    <form method="POST"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
        <div class="form-grid">
            <div class="field"><label>Nama Event</label><input name="event_name" required value="<?= e($edit['event_name'] ?? '') ?>"></div>
            <div class="field"><label>Tanggal</label><input type="date" name="event_date" required value="<?= e($edit['event_date'] ?? '') ?>"></div>
            <div class="field"><label>Waktu</label><input type="time" name="event_time" value="<?= e(isset($edit['event_time']) ? substr((string)$edit['event_time'], 0, 5) : '') ?>"></div>
            <div class="field"><label>Lokasi</label><input name="location" value="<?= e($edit['location'] ?? '') ?>"></div>
            <div class="field"><label>Link</label><input name="link" value="<?= e($edit['link'] ?? '') ?>"></div>
            <div class="field"><label>Publik?</label><label><input type="checkbox" name="is_public" <?= !isset($edit['is_public']) || (int)$edit['is_public'] === 1 ? 'checked' : '' ?>> Tampilkan di publik</label></div>
        </div>
        <div class="field"><label>Deskripsi</label><textarea name="description" rows="3"><?= e($edit['description'] ?? '') ?></textarea></div><button class="btn" type="submit">Simpan Jadwal</button> <?php if ($edit): ?><a class="btn secondary" href="kelola-jadwal.php">Batal</a><?php endif; ?>
    </form>
</section>
<section class="card">
    <h2>Daftar Jadwal</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Event</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Lokasi/Link</th>
                <th>Publik</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody><?php foreach ($rows as $r): ?><tr>
                    <td><?= e($r['event_name']) ?><br><small class="muted"><?= e($r['description'] ?? '') ?></small></td>
                    <td><?= e(date('d M Y', strtotime($r['event_date']))) ?></td>
                    <td><?= e($r['event_time'] ? substr($r['event_time'], 0, 5) : '-') ?></td>
                    <td><?= e($r['location'] ?: '-') ?><br><?php if ($r['link']): ?><a href="<?= e($r['link']) ?>" target="_blank">Link</a><?php endif; ?></td>
                    <td><?= (int)$r['is_public'] ? '<span class="badge ok">Ya</span>' : '<span class="badge no">Tidak</span>' ?></td>
                    <td class="actions"><a class="btn small secondary" href="kelola-jadwal.php?edit=<?= (int)$r['id'] ?>">Edit</a>
                        <form method="POST" onsubmit="return confirm('Hapus jadwal ini?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="event_name" value="x"><input type="hidden" name="event_date" value="<?= e($r['event_date']) ?>"><button class="btn small danger" name="action" value="delete">Hapus</button></form>
                    </td>
                </tr><?php endforeach; ?></tbody>
    </table>
</section>
<?php panitia_footer(); ?>