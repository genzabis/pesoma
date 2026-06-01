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
        $nama     = trim($_POST['nama_anggota'] ?? '');
        $nim      = trim($_POST['nim_anggota'] ?? '');
        $fakultas = trim($_POST['fakultas'] ?? '');
        $peran    = trim($_POST['peran'] ?? 'Anggota');
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

<div class="section-head">
    <span class="section-eyebrow">Manajemen Tim</span>
    <h2 class="section-title">Tim saya.</h2>
</div>

<?php if (!$grouped): ?>
    <section class="card">
        <p class="muted" style="margin: 0 0 16px;">Anda belum terdaftar pada lomba berkategori tim. Daftar dulu untuk bisa menambahkan anggota tim.</p>
        <a class="btn" href="daftar-lomba.php?jenis=tim">Lihat Lomba Tim</a>
    </section>
<?php else: ?>
    <?php foreach ($grouped as $registrationId => $group): ?>
        <?php
        $meta    = $group['meta'];
        $members = $group['members'] ?? [];
        $count   = count($members);
        $max     = (int) $meta['max_anggota'];
        $full    = $count >= $max;
        ?>
        <section class="card" style="margin-bottom: 18px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap; margin-bottom: 18px;">
                <div>
                    <span style="font-family: var(--ff-mono); font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: .12em; color: var(--c-ink-mute);"><?= e($meta['nomor_peserta']) ?></span>
                    <h3 style="margin: 4px 0 0;"><?= e($meta['nama_lomba']) ?></h3>
                </div>
                <div style="text-align: right;">
                    <div style="font-family: var(--ff-mono); font-size: 11px; text-transform: uppercase; letter-spacing: .12em; color: var(--c-ink-mute);">Anggota</div>
                    <div style="font-size: 24px; font-weight: 600; letter-spacing: -.02em;"><?= $count ?> / <?= $max ?></div>
                </div>
            </div>

            <?php if (!$members): ?>
                <p class="muted" style="margin: 0 0 18px;">Belum ada anggota. Tambahkan minimal satu anggota di bawah.</p>
            <?php else: ?>
                <div style="overflow-x: auto; margin-bottom: 18px;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>NIM</th>
                                <th>Fakultas</th>
                                <th>Peran</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $m): ?>
                                <tr>
                                    <td><?= e($m['nama_anggota']) ?></td>
                                    <td><?= e($m['nim_anggota']) ?></td>
                                    <td><span class="badge neutral"><?= e($m['fakultas']) ?></span></td>
                                    <td><?= e($m['peran']) ?></td>
                                    <td style="text-align: right;">
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Hapus anggota ini?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="registration_id" value="<?= (int) $registrationId ?>">
                                            <input type="hidden" name="team_id" value="<?= (int) $m['id'] ?>">
                                            <button class="btn small danger" type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if ($full): ?>
                <div class="alert info" style="margin: 0;">Tim sudah penuh (<?= $max ?> anggota). Hapus anggota terlebih dahulu sebelum menambah yang baru.</div>
            <?php else: ?>
                <div style="border-top: 1px solid var(--c-line); padding-top: 18px;">
                    <h4 style="margin: 0 0 12px; font-size: 14px; font-weight: 600;">Tambah Anggota</h4>
                    <form method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="registration_id" value="<?= (int) $registrationId ?>">

                        <div class="filters" style="grid-template-columns: 1.5fr 1fr 1fr 1fr auto;">
                            <div class="field" style="margin: 0;">
                                <label>Nama</label>
                                <input name="nama_anggota" placeholder="Nama lengkap anggota" required>
                            </div>
                            <div class="field" style="margin: 0;">
                                <label>NIM</label>
                                <input name="nim_anggota" placeholder="2241010xxx" required>
                            </div>
                            <div class="field" style="margin: 0;">
                                <label>Fakultas</label>
                                <select name="fakultas" required>
                                    <option value="">Pilih</option>
                                    <?php foreach (ALLOWED_FAKULTAS as $f): ?>
                                        <option value="<?= e($f) ?>"><?= e($f) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="field" style="margin: 0;">
                                <label>Peran</label>
                                <select name="peran">
                                    <option value="Penulis Naskah">Penulis Naskah</option>
                                    <option value="Presenter">Presenter</option>
                                    <option value="IT Support">IT Support</option>
                                    <option value="Anggota" selected>Anggota</option>
                                </select>
                            </div>
                            <button class="btn" type="submit" style="height: fit-content; align-self: end;">Tambah</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
<?php endif; ?>

<?php peserta_footer(); ?>
