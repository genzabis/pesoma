<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

$errors = [];
$selectedId = (int) ($_POST['competition_id'] ?? $_GET['competition_id'] ?? 0);
$competitions = db_fetch_all('SELECT * FROM competitions WHERE is_active = 1 ORDER BY nama_lomba');
$competition = $selectedId ? db_fetch('SELECT * FROM competitions WHERE id = ? AND is_active = 1', [$selectedId]) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Token keamanan tidak valid.';
    }
    if (!$competition) {
        $errors[] = 'Cabang lomba tidak valid.';
    }
    $existing = $competition ? db_fetch('SELECT id FROM registrations WHERE user_id = ? AND competition_id = ?', [current_user_id(), $selectedId]) : null;
    if ($existing) {
        $errors[] = 'Anda sudah terdaftar pada cabang lomba ini.';
    }

    $teamNames = $_POST['team_nama'] ?? [];
    $teamNims = $_POST['team_nim'] ?? [];
    $teamFakultas = $_POST['team_fakultas'] ?? [];
    $teamRoles = $_POST['team_peran'] ?? [];
    $teamRows = [];
    if ($competition && $competition['jenis'] === 'tim') {
        for ($i = 0; $i < count($teamNames); $i++) {
            $name = trim((string) ($teamNames[$i] ?? ''));
            $nim = trim((string) ($teamNims[$i] ?? ''));
            $fakultas = trim((string) ($teamFakultas[$i] ?? ''));
            $peran = trim((string) ($teamRoles[$i] ?? 'Anggota'));
            if ($name === '' && $nim === '') {
                continue;
            }
            if ($name === '' || $nim === '' || !in_array($fakultas, ALLOWED_FAKULTAS, true)) {
                $errors[] = 'Data anggota tim wajib lengkap dan fakultas valid.';
                break;
            }
            $teamRows[] = compact('name', 'nim', 'fakultas', 'peran');
        }
        if (count($teamRows) > (int) $competition['max_anggota']) {
            $errors[] = 'Jumlah anggota melebihi batas maksimal lomba.';
        }
    }
    $mentorName = trim($_POST['mentor_name'] ?? '');
    $mentorNidn = trim($_POST['mentor_nidn'] ?? '');
    if ($competition && (int) $competition['requires_mentor'] === 1 && $mentorName === '') {
        $errors[] = 'Nama pendamping wajib diisi untuk lomba ini.';
    }

    if ($errors === []) {
        db()->beginTransaction();
        try {
            $nomor = sprintf('PESOMA-2026-%d-%d', $selectedId, current_user_id());
            db_query('INSERT INTO registrations (nomor_peserta, user_id, competition_id) VALUES (?, ?, ?)', [$nomor, current_user_id(), $selectedId]);
            $registrationId = (int) db_last_insert_id();
            foreach ($teamRows as $row) {
                db_query('INSERT INTO teams (registration_id, nama_anggota, nim_anggota, fakultas, peran) VALUES (?, ?, ?, ?, ?)', [$registrationId, $row['name'], $row['nim'], $row['fakultas'], $row['peran']]);
            }
            if ($mentorName !== '') {
                db_query('INSERT INTO mentors (registration_id, nama_dosen, nidn) VALUES (?, ?, ?)', [$registrationId, $mentorName, $mentorNidn]);
            }
            db()->commit();
            log_activity(current_user_id(), ROLE_PESERTA, 'registration_create', 'Mendaftar lomba ' . $competition['nama_lomba']);
            flash('success', 'Pendaftaran berhasil disimpan. Nomor peserta: ' . $nomor);
            redirect('dashboard.php');
        } catch (Throwable $e) {
            db()->rollBack();
            $errors[] = 'Gagal menyimpan pendaftaran: ' . $e->getMessage();
        }
    }
}

peserta_header('Form Pendaftaran Lomba', 'form-pendaftaran.php');
foreach ($errors as $error) {
    echo '<div class="alert error"><i class="fas fa-exclamation-circle"></i> ' . e($error) . '</div>';
}
?>
<section class="card span-12">
    <h2>Form Pendaftaran Lomba</h2>
    <form method="POST">
        <?= csrf_field() ?>
        <div class="field">
            <label for="competition_id">Pilih Cabang Lomba</label>
            <select id="competition_id" name="competition_id" onchange="location.href='form-pendaftaran.php?competition_id='+this.value" required>
                <option value="">-- Pilih lomba --</option>
                <?php foreach ($competitions as $item): ?>
                    <option value="<?= (int) $item['id'] ?>" <?= $selectedId === (int) $item['id'] ? 'selected' : '' ?>>
                        <?= e($item['nama_lomba']) ?> (<?= e($item['jenis']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if ($competition): ?>
            <div class="card span-12" style="margin-top:24px;background:rgba(26,157,110,.04);border-color:rgba(26,157,110,.12)">
                <h3 style="margin-top:0"><?= e($competition['nama_lomba']) ?></h3>
                <p class="muted"><?= e($competition['aturan']) ?></p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px">
                    <div>
                        <strong>Maks Anggota:</strong><br>
                        <span style="font-size:18px;color:var(--primary);font-weight:800"><?= (int) $competition['max_anggota'] ?> orang</span>
                    </div>
                    <div>
                        <strong>Pendamping:</strong><br>
                        <span style="font-size:18px;color:var(--primary);font-weight:800"><?= (int) $competition['requires_mentor'] === 1 ? '✓ Wajib' : '✗ Tidak wajib' ?></span>
                    </div>
                </div>
            </div>

            <?php if ($competition['jenis'] === 'tim'): ?>
                <h3 style="margin-top:32px">Anggota Tim</h3>
                <p class="muted">Isi anggota tim sesuai batas maksimal cabang lomba. Kosongkan jika tidak ada anggota tambahan.</p>
                <div style="overflow-x:auto">
                    <table class="table" style="margin-top:16px">
                        <thead>
                            <tr>
                                <th>Nama Anggota</th>
                                <th>NIM</th>
                                <th>Fakultas</th>
                                <th>Peran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < (int) $competition['max_anggota']; $i++): ?>
                                <tr>
                                    <td><input name="team_nama[]" placeholder="Nama lengkap" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:8px"></td>
                                    <td><input name="team_nim[]" placeholder="NIM" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:8px"></td>
                                    <td>
                                        <select name="team_fakultas[]" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:8px">
                                            <option value="">Pilih</option>
                                            <?php foreach (ALLOWED_FAKULTAS as $f): ?>
                                                <option value="<?= e($f) ?>"><?= e($f) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="team_peran[]" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:8px">
                                            <option>Penulis Naskah</option>
                                            <option>Presenter</option>
                                            <option>IT Support</option>
                                            <option>Anggota</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if ((int) $competition['requires_mentor'] === 1): ?>
                <h3 style="margin-top:32px">Pendamping (Dosen/Tendik)</h3>
                <div class="form-grid">
                    <div class="field">
                        <label for="mentor_name">Nama Dosen/Tendik</label>
                        <input id="mentor_name" name="mentor_name" placeholder="Nama lengkap" required>
                    </div>
                    <div class="field">
                        <label for="mentor_nidn">NIDN/NIP</label>
                        <input id="mentor_nidn" name="mentor_nidn" placeholder="NIDN atau NIP">
                    </div>
                </div>
            <?php endif; ?>

            <div style="display:flex;gap:12px;margin-top:32px">
                <button class="btn" type="submit"><i class="fas fa-check"></i> Simpan Pendaftaran</button>
                <a href="daftar-lomba.php" class="btn secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        <?php endif; ?>
    </form>
</section>
<?php peserta_footer(); ?>
