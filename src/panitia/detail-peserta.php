<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

$registrationId = (int) ($_GET['id'] ?? $_GET['registration_id'] ?? 0);
$detail = $registrationId > 0 ? panitia_registration_detail($registrationId) : null;

$teams = $detail ? db_fetch_all('SELECT * FROM teams WHERE registration_id = ? ORDER BY id', [$registrationId]) : [];
$mentor = $detail ? db_fetch('SELECT * FROM mentors WHERE registration_id = ? LIMIT 1', [$registrationId]) : null;
$submission = $detail ? db_fetch('SELECT * FROM submissions WHERE registration_id = ?', [$registrationId]) : null;
$files = $submission ? panitia_json_array($submission['original_names'] ?? null) : [];

panitia_header('Detail Peserta', 'verifikasi-peserta.php');

if (!$detail) {
    echo '<section class="card"><h2>Data peserta tidak ditemukan</h2><p class="muted">Pendaftaran tidak tersedia.</p><a class="btn secondary" href="verifikasi-peserta.php">Kembali</a></section>';
    panitia_footer();
    exit;
}
?>
<section class="card" style="margin-bottom:18px">
    <div class="actions" style="justify-content:space-between">
        <h2>Pendaftaran <?= e($detail['nomor_peserta']) ?></h2>
        <a class="btn secondary" href="verifikasi-peserta.php">Kembali</a>
    </div>
    <p><b>Status:</b> <?= panitia_badge($detail['status_verifikasi']) ?></p>
    <?php if (!empty($detail['catatan_verifikasi'])): ?><p><b>Catatan:</b> <?= e($detail['catatan_verifikasi']) ?></p><?php endif; ?>
    <div class="form-grid">
        <div>
            <h3>Data Ketua</h3>
            <p><b>Nama:</b> <?= e($detail['nama']) ?></p>
            <p><b>NIM:</b> <?= e($detail['nim'] ?: '-') ?></p>
            <p><b>Email:</b> <?= e($detail['email']) ?></p>
            <p><b>Fakultas:</b> <?= e($detail['fakultas'] ?: '-') ?></p>
            <p><b>Telepon:</b> <?= e($detail['phone'] ?: '-') ?></p>
        </div>
        <div>
            <h3>Data Lomba</h3>
            <p><b>Cabang:</b> <?= e($detail['nama_lomba']) ?> (<?= e($detail['jenis']) ?>)</p>
            <p><b>Kode:</b> <?= e($detail['kode_lomba']) ?></p>
            <p><b>TM:</b> <span class="badge <?= (int) $detail['tm_attendance'] ? 'ok' : 'no' ?>"><?= (int) $detail['tm_attendance'] ? 'Hadir' : 'Belum' ?></span></p>
            <p><b>Final:</b> <span class="badge <?= (int) $detail['final_attendance'] ? 'ok' : 'no' ?>"><?= (int) $detail['final_attendance'] ? 'Hadir' : 'Belum' ?></span></p>
            <p><b>Tanggal Daftar:</b> <?= e(date('d M Y H:i', strtotime((string) $detail['created_at']))) ?></p>
        </div>
    </div>
</section>

<?php if ($detail['jenis'] === 'tim'): ?>
    <section class="card" style="margin-bottom:18px">
        <h3>Anggota Tim</h3>
        <?php if (!$teams): ?><p class="muted">Tidak ada anggota tim tambahan.</p><?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Fakultas</th>
                        <th>Peran</th>
                    </tr>
                </thead>
                <tbody><?php foreach ($teams as $t): ?><tr>
                            <td><?= e($t['nama_anggota']) ?></td>
                            <td><?= e($t['nim_anggota']) ?></td>
                            <td><?= e($t['fakultas'] ?: '-') ?></td>
                            <td><?= e($t['peran']) ?></td>
                        </tr><?php endforeach; ?></tbody>
            </table>
        <?php endif; ?>
    </section>
<?php endif; ?>

<?php if ((int) $detail['requires_mentor'] === 1): ?>
    <section class="card" style="margin-bottom:18px">
        <h3>Pendamping</h3>
        <?php if ($mentor): ?>
            <p><b>Nama:</b> <?= e($mentor['nama_dosen']) ?> · <b>NIDN:</b> <?= e($mentor['nidn'] ?: '-') ?> · <b>Jabatan:</b> <?= e($mentor['jabatan'] ?: '-') ?></p>
        <?php else: ?><p class="muted">Belum ada data pendamping.</p><?php endif; ?>
    </section>
<?php endif; ?>

<section class="card">
    <h3>Karya Terunggah</h3>
    <?php if (!$submission): ?><p class="muted">Belum ada karya yang diunggah.</p><?php else: ?>
        <p><b>Status:</b> <?= e($submission['status']) ?> · <b>Diunggah:</b> <?= e(date('d M Y H:i', strtotime((string) $submission['uploaded_at']))) ?></p>
        <?php if ($files): ?>
            <ul><?php foreach ($files as $field => $name): ?><li><b><?= e(ucfirst((string) $field)) ?>:</b> <?= e((string) $name) ?></li><?php endforeach; ?></ul>
        <?php endif; ?>
    <?php endif; ?>
</section>
<?php panitia_footer(); ?>
