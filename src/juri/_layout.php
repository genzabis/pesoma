<?php

declare(strict_types=1);


/**
 * PESOMA III 2026 - UIN Prof. K.H. Saifuddin Zuhri Purwokerto
 * Copyright (c) 2026 Tim Pengembang PESOMA III. All Rights Reserved.
 *
 * This file is part of a proprietary software project. Unauthorized
 * copying, redistribution, or use of this file, via any medium, is
 * strictly prohibited. See LICENSE for the full terms.
 */

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
        <meta name="theme-color" content="#0c1733">
        <title><?= e($title) ?> â€” <?= e(APP_NAME) ?></title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="<?= e(APP_URL) ?>/assets/css/dashboard-pesoma.css?v=3">
    </head>

    <body>
        <div class="app">
            <aside class="sidebar">
                <div class="brand"><span>PESOMA III</span></div>
                <div class="role">Panel Juri</div>
                <nav class="nav">
                    <?php foreach (juri_nav_items() as $href => $label): ?>
                        <a class="<?= basename($href) === $active ? 'active' : '' ?>" href="<?= e($href) ?>"><i class="<?= e(juri_nav_icon($href)) ?>" aria-hidden="true"></i><span><?= e($label) ?></span></a>
                    <?php endforeach; ?>
                </nav>
                <div class="sidebar-footer">UIN SAIZU Â· 2026</div>
            </aside>
            <main class="main">
                <div class="topbar">
                    <div>
                        <span class="topbar-eyebrow">Juri</span>
                        <h1><?= e($title) ?></h1>
                    </div>
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
