<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        flash('error', 'Token keamanan tidak valid.');
        redirect('verifikasi-peserta.php');
    }
    $id = (int) ($_POST['registration_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $status = $action === 'terima' ? 'diterima' : ($action === 'tolak' ? 'ditolak' : '');
    if ($id > 0 && in_array($action, ['tm_hadir', 'tm_batal', 'final_hadir', 'final_batal'], true)) {
        $field = str_starts_with($action, 'tm_') ? 'tm' : 'final';
        $present = str_ends_with($action, '_hadir') ? 1 : 0;
        db_query("UPDATE registrations SET {$field}_attendance=?, {$field}_checked_by=?, {$field}_checked_at=NOW() WHERE id=?", [$present, panitia_user_id(), $id]);
        log_activity(panitia_user_id(), ROLE_PANITIA, 'checkin_' . $field, ($present ? 'Hadir ' : 'Batal hadir ') . 'registration #' . $id);
        flash('success', 'Status check-in berhasil diperbarui.');
    } elseif ($id > 0 && $status) {
        db_query('UPDATE registrations SET status_verifikasi=?, catatan_verifikasi=?, verified_by=?, verified_at=NOW() WHERE id=?', [$status, trim((string)($_POST['catatan'] ?? '')), panitia_user_id(), $id]);
        log_activity(panitia_user_id(), ROLE_PANITIA, 'verifikasi_peserta', strtoupper($status) . ' registration #' . $id);
        flash('success', 'Status pendaftaran berhasil diperbarui.');
    }
    redirect('verifikasi-peserta.php?competition_id=' . (int)($_POST['filter_competition_id'] ?? 0) . '&status=' . urlencode((string)($_POST['filter_status'] ?? '')));
}

$competitions = panitia_competitions();
$competitionId = (int) ($_GET['competition_id'] ?? 0);
$status = $_GET['status'] ?? '';
$where = [];
$params = [];
if ($competitionId) {
    $where[] = 'r.competition_id=?';
    $params[] = $competitionId;
}
if (in_array($status, ['pending', 'diterima', 'ditolak'], true)) {
    $where[] = 'r.status_verifikasi=?';
    $params[] = $status;
}
$sql = 'SELECT r.*, u.nim,u.nama,u.email,u.fakultas, c.nama_lomba,c.jenis, (SELECT COUNT(*) FROM teams t WHERE t.registration_id=r.id) jumlah_tim, (SELECT nama_dosen FROM mentors m WHERE m.registration_id=r.id LIMIT 1) pendamping FROM registrations r JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=r.competition_id' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY r.created_at DESC';
$rows = db_fetch_all($sql, $params);
panitia_header('Verifikasi Peserta', 'verifikasi-peserta.php');
?>
<div class="grid">
    <section class="card span-12">
        <h2>Filter Peserta</h2>
        <form method="GET" style="display:grid;grid-template-columns:1fr 1fr auto auto;gap:12px;align-items:flex-end">
            <div class="field" style="margin-bottom:0">
                <label for="competition_id">Cabang Lomba</label>
                <select id="competition_id" name="competition_id">
                    <option value="0">Semua</option>
                    <?php foreach ($competitions as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= $competitionId === (int)$c['id'] ? 'selected' : '' ?>>
                            <?= e($c['nama_lomba']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field" style="margin-bottom:0">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">Semua</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="diterima" <?= $status === 'diterima' ? 'selected' : '' ?>>Diterima</option>
                    <option value="ditolak" <?= $status === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                </select>
            </div>
            <button class="btn" type="submit"><i class="fas fa-filter"></i> Filter</button>
            <a class="btn secondary" href="verifikasi-peserta.php"><i class="fas fa-redo"></i> Reset</a>
        </form>
    </section>

    <?php if (!$rows): ?>
        <section class="card span-12">
            <div style="text-align:center;padding:60px 20px;color:var(--text-secondary)">
                <i class="fas fa-inbox" style="font-size:64px;margin-bottom:16px;opacity:.3"></i>
                <p style="font-size:16px">Tidak ada peserta yang sesuai dengan filter</p>
            </div>
        </section>
    <?php else: ?>
        <section class="card span-12">
            <h2 style="margin-top:0">Daftar Peserta (<?= count($rows) ?> peserta)</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Tanggal</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Cabang</th>
                        <th style="text-align:center">Tim</th>
                        <th style="text-align:center">Status</th>
                        <th style="text-align:center">TM</th>
                        <th style="text-align:center">Final</th>
                        <th style="width:80px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $i => $r): 
                        $teams = db_fetch_all('SELECT * FROM teams WHERE registration_id=?', [(int)$r['id']]);
                        $mentor = db_fetch('SELECT * FROM mentors WHERE registration_id=? LIMIT 1', [(int)$r['id']]); 
                    ?>
                        <tr>
                            <td style="font-weight:700;color:var(--primary)"><?= $i + 1 ?></td>
                            <td><small><?= e(date('d M', strtotime($r['created_at']))) ?></small></td>
                            <td><strong><?= e($r['nim']) ?></strong></td>
                            <td><?= e($r['nama']) ?></td>
                            <td><small><?= e($r['nama_lomba']) ?></small></td>
                            <td style="text-align:center"><span class="badge info"><?= (int)$r['jumlah_tim'] ?></span></td>
                            <td style="text-align:center"><?= panitia_badge($r['status_verifikasi']) ?></td>
                            <td style="text-align:center">
                                <span class="badge <?= (int)$r['tm_attendance'] ? 'ok' : 'no' ?>" style="font-size:11px">
                                    <?= (int)$r['tm_attendance'] ? '✓' : '✗' ?>
                                </span>
                            </td>
                            <td style="text-align:center">
                                <span class="badge <?= (int)$r['final_attendance'] ? 'ok' : 'no' ?>" style="font-size:11px">
                                    <?= (int)$r['final_attendance'] ? '✓' : '✗' ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn small secondary" onclick="document.getElementById('m<?= (int)$r['id'] ?>').showModal()" type="button" style="width:100%">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php foreach ($rows as $r): 
                $teams = db_fetch_all('SELECT * FROM teams WHERE registration_id=?', [(int)$r['id']]);
                $mentor = db_fetch('SELECT * FROM mentors WHERE registration_id=? LIMIT 1', [(int)$r['id']]); 
            ?>
                <dialog class="modal" id="m<?= (int)$r['id'] ?>">
                    <div class="modal-body">
                        <div class="modal-head">
                            <h3 style="margin:0"><?= e($r['nomor_peserta']) ?> - <?= e($r['nama']) ?></h3>
                            <button class="close" onclick="this.closest('dialog').close()"><i class="fas fa-times"></i></button>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;padding:12px;background:rgba(26,157,110,.04);border-radius:8px;font-size:13px">
                            <div><strong>NIM:</strong> <?= e($r['nim']) ?></div>
                            <div><strong>Email:</strong> <?= e($r['email']) ?></div>
                            <div><strong>Fakultas:</strong> <?= e($r['fakultas']) ?></div>
                            <div><strong>Cabang:</strong> <?= e($r['nama_lomba']) ?></div>
                        </div>

                        <h4 style="margin-bottom:8px">Anggota Tim</h4>
                        <?php if (!$teams): ?>
                            <p class="muted" style="font-size:13px;margin:0">Peserta individual (tidak ada anggota tim)</p>
                        <?php else: ?>
                            <table class="table" style="font-size:12px;margin-bottom:16px">
                                <tr><th>Nama</th><th>NIM</th><th>Peran</th></tr>
                                <?php foreach ($teams as $t): ?>
                                    <tr>
                                        <td><?= e($t['nama_anggota']) ?></td>
                                        <td><?= e($t['nim_anggota']) ?></td>
                                        <td><?= e($t['peran']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php endif; ?>

                        <h4 style="margin-bottom:8px">Pendamping</h4>
                        <p style="font-size:13px;margin:0">
                            <?php if ($mentor): ?>
                                <strong><?= e($mentor['nama_dosen']) ?></strong><br>
                                <small><?= e($mentor['nidn']) ?></small>
                            <?php else: ?>
                                <span class="muted">Tidak ada</span>
                            <?php endif; ?>
                        </p>

                        <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border)">
                            <form method="POST" style="display:grid;gap:8px">
                                <?= csrf_field() ?>
                                <input type="hidden" name="registration_id" value="<?= (int)$r['id'] ?>">
                                <input type="hidden" name="filter_competition_id" value="<?= $competitionId ?>">
                                <input type="hidden" name="filter_status" value="<?= e($status) ?>">

                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                                    <button name="action" value="terima" class="btn small" type="submit" style="background:var(--ok)">✓ Terima</button>
                                    <button name="action" value="tolak" class="btn small danger" type="submit">✗ Tolak</button>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                                    <button name="action" value="<?= (int)$r['tm_attendance'] ? 'tm_batal' : 'tm_hadir' ?>" class="btn small secondary" type="submit">
                                        <?= (int)$r['tm_attendance'] ? 'Batal TM' : 'Hadir TM' ?>
                                    </button>
                                    <button name="action" value="<?= (int)$r['final_attendance'] ? 'final_batal' : 'final_hadir' ?>" class="btn small secondary" type="submit">
                                        <?= (int)$r['final_attendance'] ? 'Batal Final' : 'Hadir Final' ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </dialog>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</div>
<?php panitia_footer(); ?>
