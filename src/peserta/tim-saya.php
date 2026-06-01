<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) $errors[] = 'Token keamanan tidak valid.';
    $action = $_POST['action'] ?? '';
    $registrationId = (int) ($_POST['registration_id'] ?? 0);
    $reg = db_fetch('SELECT r.*, c.max_anggota FROM registrations r JOIN competitions c ON c.id = r.competition_id WHERE r.id = ? AND r.user_id = ? AND c.jenis = "tim"', [$registrationId, current_user_id()]);
    if (!$reg) $errors[] = 'Pendaftaran tim tidak valid.';
    if ($reg && strtotime((string) $reg['created_at']) > strtotime('2026-04-27 23:59:59')) $errors[] = 'Deadline perubahan tim sudah lewat.';
    if ($reg && $action === 'add' && $errors === []) {
        $count = (int) (db_fetch('SELECT COUNT(*) AS total FROM teams WHERE registration_id = ?', [$registrationId])['total'] ?? 0);
        if ($count >= (int) $reg['max_anggota']) $errors[] = 'Anggota tim sudah mencapai batas maksimal.';
        $nama = trim($_POST['nama_anggota'] ?? '');
        $nim = trim($_POST['nim_anggota'] ?? '');
        $fakultas = trim($_POST['fakultas'] ?? '');
        $peran = trim($_POST['peran'] ?? 'Anggota');
        if ($nama === '' || $nim === '' || !in_array($fakultas, ALLOWED_FAKULTAS, true)) $errors[] = 'Data anggota wajib lengkap.';
        if ($errors === []) {
            db_query('INSERT INTO teams (registration_id, nama_anggota, nim_anggota, fakultas, peran) VALUES (?, ?, ?, ?, ?)', [$registrationId, $nama, $nim, $fakultas, $peran]);
            flash('success', 'Anggota tim ditambahkan.');
            redirect('tim-saya.php');
        }
    }
    if ($reg && $action === 'delete' && $errors === []) {
        db_query('DELETE FROM teams WHERE id = ? AND registration_id = ?', [(int) $_POST['team_id'], $registrationId]);
        flash('success', 'Anggota tim dihapus.');
        redirect('tim-saya.php');
    }
}

$teams = db_fetch_all('SELECT r.id AS registration_id, r.nomor_peserta, c.nama_lomba, c.max_anggota, t.* FROM registrations r JOIN competitions c ON c.id = r.competition_id LEFT JOIN teams t ON t.registration_id = r.id WHERE r.user_id = ? AND c.jenis = "tim" ORDER BY c.nama_lomba, t.created_at', [current_user_id()]);
$grouped = [];
foreach ($teams as $row) {
    $grouped[$row['registration_id']]['meta'] = $row;
    if ($row['id']) $grouped[$row['registration_id']]['members'][] = $row;
}

peserta_header('Tim Saya', 'tim-saya.php');
foreach ($errors as $error) echo '<div class="alert error">' . e($error) . '</div>';
?>
<?php if (!$grouped): ?><section class="card">
        <p class="muted">Anda belum terdaftar pada lomba tim.</p>
    </section><?php endif; ?>
<?php foreach ($grouped as $registrationId => $group): $meta = $group['meta'];
    $members = $group['members'] ?? []; ?>
    <section class="card" style="margin-bottom:18px">
        <h2><?= e($meta['nama_lomba']) ?></h2>
        <p><?= e($meta['nomor_peserta']) ?> · Maks <?= (int) $meta['max_anggota'] ?> anggota</p>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>Fakultas</th>
                    <th>Peran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody><?php foreach ($members as $m): ?><tr>
                        <td><?= e($m['nama_anggota']) ?></td>
                        <td><?= e($m['nim_anggota']) ?></td>
                        <td><?= e($m['fakultas']) ?></td>
                        <td><?= e($m['peran']) ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Hapus anggota ini?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="registration_id" value="<?= (int) $registrationId ?>"><input type="hidden" name="team_id" value="<?= (int) $m['id'] ?>"><button class="btn danger small">Hapus</button></form>
                        </td>
                    </tr><?php endforeach; ?></tbody>
        </table>
        <h3>Tambah Anggota</h3>
        <form method="POST" class="team-row"><?= csrf_field() ?><input type="hidden" name="action" value="add"><input type="hidden" name="registration_id" value="<?= (int) $registrationId ?>"><input name="nama_anggota" placeholder="Nama"><input name="nim_anggota" placeholder="NIM"><select name="fakultas">
                <option value="">Fakultas</option><?php foreach (ALLOWED_FAKULTAS as $f): ?><option value="<?= e($f) ?>"><?= e($f) ?></option><?php endforeach; ?>
            </select><select name="peran">
                <option>Penulis Naskah</option>
                <option>Presenter</option>
                <option>IT Support</option>
                <option>Anggota</option>
            </select><button class="btn small">Tambah</button></form>
    </section>
<?php endforeach;
peserta_footer(); ?>