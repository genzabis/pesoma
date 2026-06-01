<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/auth-layout.php';

if (is_logged_in()) {
    redirect(dashboard_url_by_role($_SESSION['user']['role']));
}

// Data program studi per fakultas
$programStudiPerFakultas = [
    'FTIK'  => ['PAI' => 'Pendidikan Agama Islam', 'MPI' => 'Manajemen Pendidikan Islam', 'PGMI' => 'Pendidikan Guru Madrasah Ibtidaiyah', 'PIAUD' => 'Pendidikan Islam Anak Usia Dini', 'PBA' => 'Pendidikan Bahasa Arab', 'TBI' => 'Tadris Bahasa Inggris', 'TM' => 'Tadris Matematika'],
    'FASYA' => ['HKI' => 'Hukum Keluarga Islam', 'HES' => 'Hukum Ekonomi Syariah', 'HTN' => 'Hukum Tata Negara', 'PM' => 'Perbandingan Mazhab'],
    'FEBI'  => ['ES' => 'Ekonomi Syariah', 'PS' => 'Perbankan Syariah', 'MAZAWA' => 'Manajemen Zakat dan Wakaf'],
    'FAKDA' => ['KPI' => 'Komunikasi dan Penyiaran Islam', 'BKI' => 'Bimbingan dan Konseling Islam', 'MD' => 'Manajemen Dakwah', 'PMI' => 'Pengembangan Masyarakat Islam'],
    'FUAH'  => ['IAT' => 'Ilmu Al-Qur\'an dan Tafsir', 'SPI' => 'Sejarah Peradaban Islam', 'SAA' => 'Studi Agama-Agama'],
    'FST'   => ['INF' => 'Informatika', 'ARS' => 'Arsitektur', 'ILK' => 'Ilmu Lingkungan', 'PSI' => 'Perpustakaan dan Sains Informasi'],
];

$data = ['nim' => '', 'nama' => '', 'email' => '', 'fakultas' => '', 'program_studi' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['nim'] = trim($_POST['nim'] ?? '');
    $data['nama'] = trim($_POST['nama'] ?? '');
    $data['email'] = trim($_POST['email'] ?? '');
    $data['fakultas'] = trim($_POST['fakultas'] ?? '');
    $data['program_studi'] = trim($_POST['program_studi'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $passwordConfirmation = (string) ($_POST['password_confirmation'] ?? '');

    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Token keamanan tidak valid. Silakan coba lagi.';
    }
    if ($data['nim'] === '') {
        $errors[] = 'NIM wajib diisi.';
    }
    if ($data['nama'] === '') {
        $errors[] = 'Nama lengkap wajib diisi.';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid.';
    }
    if (!str_ends_with($data['email'], '@mhs.uinsaizu.ac.id')) {
        $errors[] = 'Email harus menggunakan email mahasiswa UIN SAIZU (format: NIM@mhs.uinsaizu.ac.id).';
    }
    if (!in_array($data['fakultas'], ALLOWED_FAKULTAS, true)) {
        $errors[] = 'Fakultas tidak valid.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }
    if ($password !== $passwordConfirmation) {
        $errors[] = 'Konfirmasi password tidak sama.';
    }

    if ($errors === []) {
        $existing = db_fetch('SELECT id, email, nim FROM users WHERE email = ? OR nim = ? LIMIT 1', [$data['email'], $data['nim']]);
        if ($existing) {
            if ($existing['email'] === $data['email']) {
                $errors[] = 'Email sudah terdaftar.';
            }
            if ($existing['nim'] === $data['nim']) {
                $errors[] = 'NIM sudah terdaftar.';
            }
        }
    }

    if ($errors === []) {
        db_query(
            'INSERT INTO users (nama, nim, email, fakultas, role, password, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)',
            [$data['nama'], $data['nim'], $data['email'], $data['fakultas'], ROLE_PESERTA, password_hash($password, PASSWORD_BCRYPT)]
        );
        log_activity((int) db_last_insert_id(), ROLE_PESERTA, 'register', 'Registrasi akun peserta baru.');
        flash('success', 'Registrasi berhasil. Silakan login menggunakan Email/NIM dan password Anda.');
        redirect(APP_URL . '/src/auth/login.php');
    }
}

auth_layout_start('Daftar Peserta', [
    'heading' => 'Daftar akun<br>peserta.',
]);
?>
<a class="auth-back" href="<?= e(APP_URL) ?>/pages/beranda.php">← Beranda</a>

<h2>Buat akun.</h2>
<p class="lead">Lengkapi data sesuai dengan data resmi mahasiswa UIN SAIZU. Anda dapat mendaftar lebih dari satu cabang lomba dari satu akun.</p>

<?php if ($errors): ?>
    <div class="auth-alert error">
        <div>
            <?php foreach ($errors as $error): ?>
                <div><?= e($error) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<form method="POST" action="">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="nama">Nama Lengkap</label>
        <input id="nama" name="nama" type="text" value="<?= e($data['nama']) ?>" placeholder="Nama sesuai KTM" required>
    </div>

    <div class="form-group">
        <label for="nim">NIM</label>
        <input id="nim" name="nim" type="text" value="<?= e($data['nim']) ?>" placeholder="Contoh: 2241010xxx" required>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div class="form-group">
            <label for="fakultas">Fakultas</label>
            <select id="fakultas" name="fakultas" required onchange="updateProgramStudi()">
                <option value="">Pilih</option>
                <?php foreach (ALLOWED_FAKULTAS as $fakultas): ?>
                    <option value="<?= e($fakultas) ?>" <?= $data['fakultas'] === $fakultas ? 'selected' : '' ?>><?= e($fakultas) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="program_studi">Program Studi</label>
            <select id="program_studi" name="program_studi" required>
                <option value="">Pilih</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="email">Email Mahasiswa</label>
        <input id="email" name="email" type="email" value="<?= e($data['email']) ?>" placeholder="nim@mhs.uinsaizu.ac.id" required>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div class="form-group">
            <label for="password">Kata Sandi</label>
            <div class="input-icon-wrapper">
                <input id="password" name="password" type="password" placeholder="Min. 6 karakter" minlength="6" required style="padding-left: 14px; padding-right: 38px;">
                <button type="button" class="input-suffix-icon" onclick="pesomaTogglePassword('password', this)"><i class="fa-regular fa-eye"></i></button>
            </div>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Konfirmasi</label>
            <div class="input-icon-wrapper">
                <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi sandi" minlength="6" required style="padding-left: 14px; padding-right: 38px;">
                <button type="button" class="input-suffix-icon" onclick="pesomaTogglePassword('password_confirmation', this)"><i class="fa-regular fa-eye"></i></button>
            </div>
        </div>
    </div>

    <button class="btn primary" type="submit" style="margin-top: 8px;">Daftar Sekarang</button>

    <p style="text-align: center; margin: 24px 0 0; font-size: 13.5px; color: var(--c-ink-soft);">
        Sudah punya akun? <a href="<?= e(APP_URL) ?>/src/auth/login.php" style="color: var(--c-ink); font-weight: 600;">Masuk</a>
    </p>
</form>

<script>
    const programStudiPerFakultas = <?= json_encode($programStudiPerFakultas) ?>;
    const preselectedProgramStudi = <?= json_encode($data['program_studi']) ?>;

    function updateProgramStudi() {
        const fakultasSelect = document.getElementById('fakultas');
        const programStudiSelect = document.getElementById('program_studi');
        const selectedFakultas = fakultasSelect.value;

        programStudiSelect.innerHTML = '<option value="">Pilih</option>';

        if (selectedFakultas && programStudiPerFakultas[selectedFakultas]) {
            const programs = programStudiPerFakultas[selectedFakultas];
            for (const [code, name] of Object.entries(programs)) {
                const option = document.createElement('option');
                option.value = code;
                option.textContent = name;
                if (preselectedProgramStudi && preselectedProgramStudi === code) {
                    option.selected = true;
                }
                programStudiSelect.appendChild(option);
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const fakultasSelect = document.getElementById('fakultas');
        if (fakultasSelect && fakultasSelect.value) {
            updateProgramStudi();
        }
    });
</script>
<?php
auth_layout_end();
