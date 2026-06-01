<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

if (is_logged_in()) {
    redirect(dashboard_url_by_role($_SESSION['user']['role']));
}

$identifier = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Token keamanan tidak valid. Silakan coba lagi.';
    }

    if ($identifier === '') {
        $errors[] = 'Email atau NIM wajib diisi.';
    }

    if ($password === '') {
        $errors[] = 'Password wajib diisi.';
    }

    if ($errors === []) {
        $user = find_user_by_identifier($identifier);

        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = 'Email/NIM atau password tidak sesuai.';
            log_activity($user['id'] ?? null, $user['role'] ?? null, 'login_failed', 'Percobaan login gagal untuk identifier: ' . $identifier);
        } else {
            login_user($user);
            db_query('UPDATE users SET last_login_at = NOW() WHERE id = ?', [$user['id']]);
            log_activity((int) $user['id'], $user['role'], 'login_success', 'User berhasil login.');
            redirect(dashboard_url_by_role($user['role']));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title>Login - <?= e(APP_NAME) ?></title>
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
            width: min(480px, 100%);
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

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-primary);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: -.01em;
        }

        input {
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

        input:focus {
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
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-color: #fecaca;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border-color: #bbf7d0;
        }

        .auth-links {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }

        .auth-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 16px;
            border-radius: 12px;
            background: rgba(15, 81, 50, .06);
            color: var(--primary);
            font-size: 13.5px;
            font-weight: 700;
            transition: var(--transition);
            text-align: center;
        }

        .auth-links a:hover {
            background: rgba(15, 81, 50, .12);
            transform: translateY(-1px);
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

        @media(max-width: 640px) {
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
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <a class="back-link" href="<?= e(APP_URL) ?>/pages/beranda.php"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
        <div class="auth-card">
            <div class="auth-header">
                <h1>Login PESOMA</h1>
                <p>Masuk sebagai peserta, panitia, juri, atau admin</p>
            </div>

            <?php if ($message = flash('success')): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= e($message) ?></div><?php endif; ?>
            <?php if ($message = flash('error')): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= e($message) ?></div><?php endif; ?>
            <?php foreach ($errors as $error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div><?php endforeach; ?>

            <form method="POST" action="">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="identifier">Email atau NIM</label>
                    <input id="identifier" name="identifier" type="text" value="<?= e($identifier) ?>" placeholder="Masukkan email atau NIM" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input id="password" name="password" type="password" placeholder="Masukkan password" required autocomplete="current-password">
                        <button type="button" class="password-toggle" onclick="togglePassword('password')"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
                <button class="btn" type="submit">Login</button>
            </form>

            <div class="auth-links">
                <a href="<?= e(APP_URL) ?>/src/auth/register.php"><i class="fas fa-user-plus"></i> Daftar Peserta Baru</a>
                <a href="<?= e(APP_URL) ?>/src/auth/forgot-password.php"><i class="fas fa-key"></i> Lupa Password?</a>
            </div>
        </div>
    </div>

    <script>
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
    </script>
</body>

</html>
