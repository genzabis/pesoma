<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

require_role(ROLE_PESERTA);

function peserta_nav_items(): array
{
    return [
        'dashboard.php' => 'Dashboard',
        'daftar-lomba.php' => 'Daftar Lomba',
        'upload-karya.php' => 'Upload Karya',
        'status-pendaftaran.php' => 'Status Pendaftaran',
        'tim-saya.php' => 'Tim Saya',
        'pengumuman-saya.php' => 'Pengumuman Saya',
        '../auth/logout.php' => 'Logout',
    ];
}

function peserta_nav_icon(string $href): string
{
    return match (basename($href)) {
        'dashboard.php' => 'fa-solid fa-chart-line',
        'daftar-lomba.php' => 'fa-solid fa-trophy',
        'upload-karya.php' => 'fa-solid fa-cloud-arrow-up',
        'status-pendaftaran.php' => 'fa-solid fa-clipboard-check',
        'tim-saya.php' => 'fa-solid fa-users',
        'pengumuman-saya.php' => 'fa-solid fa-bell',
        'logout.php' => 'fa-solid fa-right-from-bracket',
        default => 'fa-solid fa-circle',
    };
}

function peserta_header(string $title, string $active = ''): void
{
    $active = $active ?: basename($_SERVER['SCRIPT_NAME']);
?>
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="theme-color" content="#0f172a">
        <title><?= e($title) ?> - <?= e(APP_NAME) ?></title>
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
                --danger: #b91c1c;
                --warn: #92400e;
                --ok: #166534;
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
                background: linear-gradient(180deg, #fcfdfc 0%, #f4f8f6 52%, #eef5f1 100%);
                line-height: 1.65;
            }

            .app {
                display: grid;
                grid-template-columns: 280px 1fr;
                min-height: 100vh;
            }

            .sidebar {
                background: linear-gradient(180deg, var(--primary), #07351f);
                color: #fff;
                padding: 28px 20px;
                position: sticky;
                top: 0;
                height: 100vh;
                overflow-y: auto;
                box-shadow: 2px 0 12px rgba(15, 81, 50, .12);
            }

            .brand {
                font-size: 18px;
                font-weight: 900;
                margin-bottom: 12px;
                display: flex;
                align-items: center;
                gap: 10px;
                letter-spacing: -.02em;
            }

            .brand i {
                font-size: 26px;
                background: rgba(255, 255, 255, .2);
                width: 40px;
                height: 40px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .role {
                opacity: .85;
                margin-bottom: 28px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .02em;
            }

            .nav {
                display: grid;
                gap: 6px;
            }

            .nav a {
                color: rgba(255, 255, 255, .85);
                text-decoration: none;
                padding: 12px 16px;
                border-radius: 10px;
                font-weight: 600;
                font-size: 14px;
                transition: var(--transition);
                display: flex;
                align-items: center;
                gap: 10px;
                position: relative;
            }

            .nav a:hover {
                background: rgba(255, 255, 255, .12);
                color: #fff;
            }

            .nav a.active {
                background: rgba(255, 255, 255, .25);
                color: #fff;
                font-weight: 700;
                box-shadow: inset 0 0 12px rgba(0, 0, 0, .1);
            }

            .main {
                padding: 32px;
                overflow-y: auto;
            }

            .topbar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                margin-bottom: 28px;
            }

            .topbar h1 {
                margin: 0;
                color: var(--primary);
                font-size: 32px;
                font-weight: 900;
                letter-spacing: -.03em;
            }

            .user-chip {
                background: rgba(255, 255, 255, .98);
                border: 1.5px solid rgba(15, 81, 50, .1);
                border-radius: 14px;
                padding: 12px 18px;
                font-weight: 700;
                font-size: 14px;
                color: var(--text-primary);
                box-shadow: 0 8px 24px rgba(15, 81, 50, .1);
            }

            .grid {
                display: grid;
                grid-template-columns: repeat(12, 1fr);
                gap: 20px;
            }

            .card {
                background: rgba(255, 255, 255, .98);
                border: 1px solid rgba(15, 81, 50, .06);
                border-radius: 20px;
                padding: 28px;
                box-shadow: 0 8px 32px rgba(15, 81, 50, .08);
                transition: var(--transition);
            }

            .card:hover {
                box-shadow: 0 16px 48px rgba(15, 81, 50, .12);
                transform: translateY(-2px);
            }

            .span-3 {
                grid-column: span 3;
            }

            .span-4 {
                grid-column: span 4;
            }

            .span-6 {
                grid-column: span 6;
            }

            .span-8 {
                grid-column: span 8;
            }

            .span-12 {
                grid-column: span 12;
            }

            .card h2,
            .card h3 {
                margin-top: 0;
                color: var(--primary);
                font-weight: 800;
            }

            .card h2 {
                font-size: 22px;
            }

            .card h3 {
                font-size: 18px;
            }

            .muted {
                color: var(--text-secondary);
                font-size: 14px;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                border: none;
                border-radius: 12px;
                background: var(--primary);
                color: #fff;
                padding: 12px 20px;
                font-weight: 700;
                font-size: 14px;
                text-decoration: none;
                cursor: pointer;
                transition: var(--transition);
                box-shadow: 0 8px 24px rgba(15, 81, 50, .2);
            }

            .btn:hover {
                background: #1a7c52;
                transform: translateY(-3px);
                box-shadow: 0 12px 32px rgba(15, 81, 50, .28);
            }

            .btn:active {
                transform: translateY(-1px);
            }

            .btn.secondary {
                background: rgba(15, 81, 50, .1);
                color: var(--primary);
                box-shadow: 0 4px 12px rgba(15, 81, 50, .08);
                border: 1.5px solid rgba(15, 81, 50, .12);
            }

            .btn.secondary:hover {
                background: rgba(15, 81, 50, .16);
                box-shadow: 0 8px 20px rgba(15, 81, 50, .14);
            }

            .btn.danger {
                background: #fee2e2;
                color: var(--danger);
                box-shadow: none;
            }

            .btn.danger:hover {
                background: #fecaca;
            }

            .btn.small {
                padding: 8px 12px;
                font-size: 13px;
            }

            .alert {
                padding: 14px 16px;
                border-radius: 12px;
                margin: 0 0 16px;
                font-weight: 700;
                font-size: 14px;
                border: 1px solid;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .alert.success {
                background: #dcfce7;
                color: var(--ok);
                border-color: #bbf7d0;
            }

            .alert.error {
                background: #fee2e2;
                color: var(--danger);
                border-color: #fecaca;
            }

            .badge {
                display: inline-block;
                border-radius: 8px;
                padding: 6px 12px;
                font-weight: 800;
                font-size: 12px;
                letter-spacing: .01em;
            }

            .badge.pending {
                background: #fef3c7;
                color: var(--warn);
            }

            .badge.diterima,
            .badge.ok {
                background: #dcfce7;
                color: var(--ok);
            }

            .badge.ditolak,
            .badge.no {
                background: #fee2e2;
                color: var(--danger);
            }

            .table {
                width: 100%;
                border-collapse: collapse;
            }

            .table th,
            .table td {
                padding: 12px;
                border-bottom: 1px solid var(--border);
                text-align: left;
                vertical-align: top;
            }

            .table th {
                color: var(--primary);
                background: rgba(15, 81, 50, .04);
                font-weight: 800;
                font-size: 13px;
            }

            .table td {
                font-size: 14px;
            }

            .table small {
                color: var(--text-secondary);
                font-size: 12px;
            }

            .form-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }

            .field {
                margin-bottom: 16px;
            }

            .field label {
                display: block;
                font-weight: 800;
                color: var(--primary);
                margin-bottom: 8px;
                font-size: 14px;
            }

            .field input,
            .field select,
            .field textarea {
                width: 100%;
                padding: 12px 16px;
                border: 1.5px solid var(--border);
                border-radius: 12px;
                background: #fff;
                font-family: inherit;
                font-size: 14px;
                transition: var(--transition);
            }

            .field input:focus,
            .field select:focus,
            .field textarea:focus {
                outline: none;
                border-color: var(--primary);
                background: #fff;
                box-shadow: 0 0 0 4px rgba(15, 81, 50, .12);
            }

            .actions {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                align-items: center;
                margin-top: 16px;
            }

            .team-row {
                display: grid;
                grid-template-columns: 1.2fr .8fr .8fr .9fr auto;
                gap: 10px;
                margin-bottom: 10px;
            }

            .deadline {
                font-weight: 900;
                color: var(--warn);
            }

            @media(max-width: 1024px) {
                .app {
                    grid-template-columns: 1fr;
                }

                .sidebar {
                    position: static;
                    height: auto;
                    padding: 20px;
                }

                .main {
                    padding: 24px;
                }

                .span-3,
                .span-4,
                .span-6,
                .span-8 {
                    grid-column: span 12;
                }

                .form-grid,
                .team-row {
                    grid-template-columns: 1fr;
                }

                .topbar {
                    align-items: flex-start;
                    flex-direction: column;
                }

                .topbar h1 {
                    font-size: 24px;
                }
            }

            @media(max-width: 640px) {
                .main {
                    padding: 16px;
                }

                .grid {
                    gap: 16px;
                }

                .card {
                    padding: 16px;
                }

                .topbar h1 {
                    font-size: 20px;
                }
            }
        </style>
    </head>

    <body>
        <div class="app">
            <aside class="sidebar">
                <div class="brand"><i class="fa-solid fa-user-graduate" aria-hidden="true"></i> PESOMA 2026</div>
                <div class="role">Panel Peserta</div>
                <nav class="nav">
                    <?php foreach (peserta_nav_items() as $href => $label): ?>
                        <a class="<?= basename($href) === $active ? 'active' : '' ?>" href="<?= e($href) ?>"><i class="<?= e(peserta_nav_icon($href)) ?>" aria-hidden="true"></i><span><?= e($label) ?></span></a>
                    <?php endforeach; ?>
                </nav>
            </aside>
            <main class="main">
                <div class="topbar">
                    <h1><?= e($title) ?></h1>
                    <div class="user-chip"><?= e($_SESSION['user']['nama'] ?? 'Peserta') ?></div>
                </div>
                <?php if ($msg = flash('success')): ?><div class="alert success"><?= e($msg) ?></div><?php endif; ?>
                <?php if ($msg = flash('error')): ?><div class="alert error"><?= e($msg) ?></div><?php endif; ?>
            <?php
        }

        function peserta_footer(): void
        {
            echo '</main></div></body></html>';
        }

        function badge_status(?string $status): string
        {
            $status = $status ?: 'pending';
            return '<span class="badge ' . e($status) . '">' . e(ucfirst($status)) . '</span>';
        }

        function current_user_id(): int
        {
            return (int) ($_SESSION['user']['id'] ?? 0);
        }

        function peserta_registrations(): array
        {
            return db_fetch_all(
                'SELECT r.*, c.nama_lomba, c.kode_lomba, c.jenis, c.kategori, c.max_anggota, c.requires_mentor, c.upload_deadline
         FROM registrations r
         JOIN competitions c ON c.id = r.competition_id
         WHERE r.user_id = ?
         ORDER BY r.created_at DESC',
                [current_user_id()]
            );
        }
