<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

if (is_logged_in()) {
    redirect(dashboard_url_by_role($_SESSION['user']['role']));
}

// Data program studi per fakultas
$programStudiPerFakultas = [
    'FTIK' => ['PAI' => 'Pendidikan Agama Islam', 'MPI' => 'Manajemen Pendidikan Islam', 'PGMI' => 'Pendidikan Guru Madrasah Ibtidaiyah', 'PIAUD' => 'Pendidikan Islam Anak Usia Dini', 'PBA' => 'Pendidikan Bahasa Arab', 'TBI' => 'Tadris Bahasa Inggris', 'TM' => 'Tadris Matematika'],
    'FASYA' => ['HKI' => 'Hukum Keluarga Islam', 'HES' => 'Hukum Ekonomi Syariah', 'HTN' => 'Hukum Tata Negara', 'PM' => 'Perbandingan Mazhab'],
    'FEBI' => ['ES' => 'Ekonomi Syariah', 'PS' => 'Perbankan Syariah', 'MAZAWA' => 'Manajemen Zakat dan Wakaf'],
    'FAKDA' => ['KPI' => 'Komunikasi dan Penyiaran Islam', 'BKI' => 'Bimbingan dan Konseling Islam', 'MD' => 'Manajemen Dakwah', 'PMI' => 'Pengembangan Masyarakat Islam'],
    'FUAH' => ['IAT' => 'Ilmu Al-Qur\'an dan Tafsir', 'SPI' => 'Sejarah Peradaban Islam', 'SAA' => 'Studi Agama-Agama'],
    'FST' => ['INF' => 'Informatika', 'ARS' => 'Arsitektur', 'ILK' => 'Ilmu Lingkungan', 'PSI' => 'Perpustakaan dan Sains Informasi'],
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
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title>Registrasi Peserta - <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a9d6e;
            --primary-dark: #0f7a52;
            --primary-light: #2fb87f;
            --accent: #c99a2e;
            --accent-light: #f3c969;
            --bg-primary: #f5f8f6;
            --bg-secondary: #fbfdfb;
            --text-primary: #132019;
            --text-secondary: #647268;
            --border: #dfe8e2;
            --shadow-sm: 0 2px 8px rgba(15, 81, 50, .08);
            --shadow-md: 0 8px 24px rgba(15, 81, 50, .12);
            --shadow-lg: 0 24px 70px rgba(15, 81, 50, .14);
            --shadow-xl: 0 34px 90px rgba(7, 53, 31, .24);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: "Plus Jakarta Sans", system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
            font-size: 15px;
            color: var(--text-primary);
            background:
                radial-gradient(circle at top left, rgba(243, 201, 105, .18), transparent 24rem),
                linear-gradient(180deg, #fcfdfc 0%, #f4f8f6 52%, #eef5f1 100%);
            line-height: 1.65;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background: linear-gradient(180deg, rgba(255, 255, 255, .22), transparent 28%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .auth-container {
            position: relative;
            z-index: 1;
            width: min(560px, 100%);
        }

        .auth-card {
            border: 1px solid rgba(15, 81, 50, .08);
            border-radius: 28px;
            background: rgba(255, 255, 255, .96);
            backdrop-filter: blur(10px);
            box-shadow: 0 12px 34px rgba(15, 81, 50, .08);
            padding: 28px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 16px;
        }

        .auth-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f5132, #1a7c52);
            color: #fff;
            font-size: 20px;
            box-shadow: 0 8px 18px rgba(15, 81, 50, .18);
        }

        .auth-header h1 {
            margin: 0;
            color: var(--primary);
            font-size: 28px;
            font-weight: 900;
            letter-spacing: -.02em;
        }

        .auth-header p {
            margin: 8px 0 0;
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-primary);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: -.01em;
        }

        input,
        select {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            background: #fff;
            color: var(--text-primary);
            font-family: inherit;
            font-size: 14px;
            transition: var(--transition);
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(26, 157, 110, .1);
        }

        input::placeholder {
            color: var(--text-secondary);
        }

        .btn {
            width: 100%;
            min-height: 46px;
            padding: 12px 20px;
            border: none;
            border-radius: 14px;
            background: var(--primary);
            color: #fff;
            font-family: inherit;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 10px 24px rgba(15, 81, 50, .16);
        }

        .btn:hover {
            background: #1a7c52;
            transform: translateY(-2px);
            box-shadow: 0 14px 26px rgba(15, 81, 50, .22);
        }

        .btn:active {
            transform: translateY(0);
        }

        .alert {
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-size: 13.5px;
            font-weight: 600;
            border: 1px solid;
            background: #fee2e2;
            color: #991b1b;
            border-color: #fecaca;
        }

        .auth-links {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
            text-align: center;
        }

        .auth-links p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 13.5px;
        }

        .auth-links a {
            color: var(--primary);
            font-weight: 700;
            transition: var(--transition);
        }

        .auth-links a:hover {
            color: #1a7c52;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 24px;
            color: var(--primary);
            font-size: 13px;
            font-weight: 700;
            transition: var(--transition);
        }

        .back-link:hover {
            transform: translateX(-2px);
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 16px;
            padding: 4px 8px;
            transition: var(--transition);
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        .password-wrapper input {
            padding-right: 40px;
        }

        @media(max-width: 680px) {
            body {
                padding: 16px;
            }

            .auth-card {
                padding: 28px 20px;
            }

            .auth-header h1 {
                font-size: 24px;
            }

            .auth-header {
                margin-bottom: 24px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <a class="back-link" href="<?= e(APP_URL) ?>/pages/beranda.php"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
        <div class="auth-card">
            <div class="auth-header">
                <h1>Registrasi Peserta</h1>
                <p>Buat akun peserta PESOMA</p>
            </div>

            <?php foreach ($errors as $error): ?><div class="alert"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div><?php endforeach; ?>

            <form method="POST" action="">
                <?= csrf_field() ?>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nim">NIM</label>
                        <input id="nim" name="nim" type="text" value="<?= e($data['nim']) ?>" placeholder="Masukkan NIM" required>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input id="nama" name="nama" type="text" value="<?= e($data['nama']) ?>" placeholder="Masukkan nama lengkap" required>
                    </div>
                    <div class="form-group full">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="<?= e($data['email']) ?>" placeholder="Masukkan email" required>
                    </div>
                    <div class="form-group full">
                        <label for="fakultas">Fakultas</label>
                        <select id="fakultas" name="fakultas" required onchange="updateProgramStudi()">
                            <option value="">Pilih fakultas</option>
                            <?php foreach (ALLOWED_FAKULTAS as $fakultas): ?>
                                <option value="<?= e($fakultas) ?>" <?= $data['fakultas'] === $fakultas ? 'selected' : '' ?>><?= e($fakultas) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group full">
                        <label for="program_studi">Program Studi</label>
                        <select id="program_studi" name="program_studi" required>
                            <option value="">Pilih program studi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-wrapper">
                            <input id="password" name="password" type="password" placeholder="Minimal 6 karakter" minlength="6" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <div class="password-wrapper">
                            <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi password" minlength="6" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                </div>
                <button class="btn" type="submit"><i class="fas fa-user-check"></i> Daftar Sekarang</button>
            </form>

            <div class="auth-links">
                <p>Sudah punya akun? <a href="<?= e(APP_URL) ?>/src/auth/login.php">Login di sini</a></p>
            </div>
        </div>
    </div>

    <script>
        const programStudiPerFakultas = <?= json_encode($programStudiPerFakultas) ?>;

        function updateProgramStudi() {
            const fakultasSelect = document.getElementById('fakultas');
            const programStudiSelect = document.getElementById('program_studi');
            const selectedFakultas = fakultasSelect.value;

            // Clear program studi options
            programStudiSelect.innerHTML = '<option value="">Pilih program studi</option>';

            if (selectedFakultas && programStudiPerFakultas[selectedFakultas]) {
                const programs = programStudiPerFakultas[selectedFakultas];
                for (const [code, name] of Object.entries(programs)) {
                    const option = document.createElement('option');
                    option.value = code;
                    option.textContent = name;
                    programStudiSelect.appendChild(option);
                }
            }
        }

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const button = event.target.closest('.password-toggle');
            const icon = button.querySelector('i');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Initialize program studi on page load if fakultas is already selected
        document.addEventListener('DOMContentLoaded', function() {
            const fakultasSelect = document.getElementById('fakultas');
            if (fakultasSelect.value) {
                updateProgramStudi();
            }
        });
    </script>
</body>

</html>
