<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

require_role(ROLE_JURI);

function juri_nav_items(): array
{
    return [
        'dashboard.php' => 'Dashboard',
        'penilaian-penyisihan.php' => 'Penilaian Penyisihan',
        'penilaian-final.php' => 'Penilaian Final',
        'riwayat-penilaian.php' => 'Riwayat Penilaian',
        '../auth/logout.php' => 'Logout',
    ];
}

function juri_nav_icon(string $href): string
{
    return match (basename($href)) {
        'dashboard.php' => 'fa-solid fa-chart-line',
        'penilaian-penyisihan.php' => 'fa-solid fa-pen-to-square',
        'penilaian-final.php' => 'fa-solid fa-star-half-stroke',
        'riwayat-penilaian.php' => 'fa-solid fa-clock-rotate-left',
        'logout.php' => 'fa-solid fa-right-from-bracket',
        default => 'fa-solid fa-circle',
    };
}

function juri_header(string $title, string $active = ''): void
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
                --blue: #1d4ed8;
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
                font-size: 20px;
                font-weight: 900;
                margin-bottom: 8px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .brand i {
                font-size: 24px;
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
                color: rgba(255, 255, 255, .9);
                text-decoration: none;
                padding: 11px 14px;
                border-radius: 12px;
                font-weight: 700;
                font-size: 14px;
                transition: var(--transition);
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .nav a:hover {
                background: rgba(255, 255, 255, .15);
                color: #fff;
            }

            .nav a.active {
                background: rgba(255, 255, 255, .2);
                color: #fff;
                font-weight: 800;
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
                font-size: 28px;
                font-weight: 900;
                letter-spacing: -.02em;
            }

            .user-chip {
                background: rgba(255, 255, 255, .96);
                border: 1px solid var(--border);
                border-radius: 12px;
                padding: 10px 16px;
                font-weight: 700;
                font-size: 14px;
                color: var(--text-primary);
                box-shadow: 0 4px 12px rgba(15, 81, 50, .08);
            }

            .grid {
                display: grid;
                grid-template-columns: repeat(12, 1fr);
                gap: 20px;
            }

            .card {
                background: rgba(255, 255, 255, .96);
                border: 1px solid rgba(15, 81, 50, .08);
                border-radius: 16px;
                padding: 24px;
                box-shadow: 0 4px 16px rgba(15, 81, 50, .06);
                transition: var(--transition);
                margin-bottom: 0;
            }

            .card:hover {
                box-shadow: 0 8px 24px rgba(15, 81, 50, .1);
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

            .stat {
                font-size: 42px;
                font-weight: 900;
                color: var(--primary);
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                border: none;
                border-radius: 12px;
                background: var(--primary);
                color: #fff;
                padding: 11px 18px;
                font-weight: 800;
                font-size: 14px;
                text-decoration: none;
                cursor: pointer;
                transition: var(--transition);
                box-shadow: 0 4px 12px rgba(15, 81, 50, .16);
            }

            .btn:hover {
                background: #1a7c52;
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(15, 81, 50, .22);
            }

            .btn:active {
                transform: translateY(0);
            }

            .btn.secondary {
                background: rgba(15, 81, 50, .08);
                color: var(--primary);
                box-shadow: none;
            }

            .btn.secondary:hover {
                background: rgba(15, 81, 50, .14);
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

            .badge.pending,
            .badge.warn {
                background: #fef3c7;
                color: var(--warn);
            }

            .badge.ok,
            .badge.done {
                background: #dcfce7;
                color: var(--ok);
            }

            .badge.info {
                background: #dbeafe;
                color: var(--blue);
            }

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
                padding: 12px 14px;
                border: 1px solid var(--border);
                border-radius: 12px;
                background: rgba(255, 255, 255, .6);
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
                box-shadow: 0 0 0 3px rgba(15, 81, 50, .1);
            }

            .actions {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                align-items: center;
                margin-top: 16px;
            }

            .score-row {
                display: grid;
                grid-template-columns: 1fr 120px 120px;
                gap: 12px;
                align-items: center;
                border-bottom: 1px solid var(--border);
                padding: 12px 0;
            }

            .score-row output {
                font-weight: 900;
                color: var(--primary);
            }

            .filters {
                display: grid;
                grid-template-columns: 1fr auto;
                gap: 12px;
                align-items: end;
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

                .topbar {
                    align-items: flex-start;
                    flex-direction: column;
                }

                .topbar h1 {
                    font-size: 24px;
                }

                .score-row,
                .filters {
                    grid-template-columns: 1fr;
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
                <div class="brand"><i class="fa-solid fa-gavel" aria-hidden="true"></i> PESOMA 2026</div>
                <div class="role">Panel Juri</div>
                <nav class="nav">
                    <?php foreach (juri_nav_items() as $href => $label): ?>
                        <a class="<?= basename($href) === $active ? 'active' : '' ?>" href="<?= e($href) ?>"><i class="<?= e(juri_nav_icon($href)) ?>" aria-hidden="true"></i><span><?= e($label) ?></span></a>
                    <?php endforeach; ?>
                </nav>
            </aside>
            <main class="main">
                <div class="topbar">
                    <h1><?= e($title) ?></h1>
                    <div class="user-chip"><?= e($_SESSION['user']['nama'] ?? 'Juri') ?></div>
                </div>
                <?php if ($msg = flash('success')): ?><div class="alert success"><?= e($msg) ?></div><?php endif; ?>
                <?php if ($msg = flash('error')): ?><div class="alert error"><?= e($msg) ?></div><?php endif; ?>
            <?php
        }

        function juri_footer(): void
        {
            echo '</main></div></body></html>';
        }

        function juri_user_id(): int
        {
            return (int)($_SESSION['user']['id'] ?? 0);
        }

        function juri_table_exists(string $table): bool
        {
            try {
                return (bool) db_fetch('SHOW TABLES LIKE ?', [$table]);
            } catch (Throwable $e) {
                return false;
            }
        }

        function juri_aspects(int $competitionId, string $babak): array
        {
            if (juri_table_exists('aspek_penilaian')) {
                $rows = db_fetch_all('SELECT aspek_name AS nama, bobot_persen AS bobot FROM aspek_penilaian WHERE competition_id=? AND babak=? ORDER BY urutan, id', [$competitionId, $babak]);
                if ($rows) {
                    return $rows;
                }
            }
            $row = db_fetch('SELECT aspek_penilaian FROM competitions WHERE id=?', [$competitionId]);
            $data = json_decode((string)($row['aspek_penilaian'] ?? '[]'), true);
            return is_array($data) ? $data : [];
        }

        function juri_total_from_post(array $aspects, array $scores): float
        {
            $total = 0.0;
            foreach ($aspects as $i => $aspect) {
                $nilai = max(0, min(100, (float)($scores[$i] ?? 0)));
                $bobot = (float)($aspect['bobot'] ?? 0);
                $total += $nilai * $bobot / 100;
            }
            return round($total, 2);
        }

        function juri_score_json(array $aspects, array $scores): string
        {
            $items = [];
            foreach ($aspects as $i => $aspect) {
                $nilai = max(0, min(100, (float)($scores[$i] ?? 0)));
                $bobot = (float)($aspect['bobot'] ?? 0);
                $items[] = [
                    'nama' => (string)($aspect['nama'] ?? $aspect['nama_aspek'] ?? 'Aspek ' . ($i + 1)),
                    'bobot' => $bobot,
                    'nilai' => $nilai,
                    'subtotal' => round($nilai * $bobot / 100, 2),
                ];
            }
            return json_encode($items, JSON_UNESCAPED_UNICODE);
        }

        function juri_status_badge(bool $done): string
        {
            return $done ? '<span class="badge ok">Sudah Dinilai</span>' : '<span class="badge warn">Belum Dinilai</span>';
        }
