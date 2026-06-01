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

require_role(ROLE_PANITIA);

function panitia_nav_items(): array
{
    return [
        'dashboard.php' => 'Dashboard',
        'verifikasi-peserta.php' => 'Verifikasi Peserta',
        'daftar-karya.php' => 'Daftar Karya',
        'tentukan-finalis.php' => 'Tentukan Finalis',
        'input-pemenang.php' => 'Input Pemenang',
        'kelola-jadwal.php' => 'Kelola Jadwal',
        'buat-pengumuman.php' => 'Buat Pengumuman',
        'laporan.php' => 'Laporan',
        '../auth/logout.php' => 'Logout',
    ];
}

function panitia_nav_icon(string $href): string
{
    return match (basename($href)) {
        'dashboard.php' => 'fa-solid fa-chart-line',
        'verifikasi-peserta.php' => 'fa-solid fa-user-check',
        'daftar-karya.php' => 'fa-solid fa-folder-open',
        'tentukan-finalis.php' => 'fa-solid fa-medal',
        'input-pemenang.php' => 'fa-solid fa-award',
        'kelola-jadwal.php' => 'fa-solid fa-calendar-days',
        'buat-pengumuman.php' => 'fa-solid fa-bullhorn',
        'laporan.php' => 'fa-solid fa-file-lines',
        'logout.php' => 'fa-solid fa-right-from-bracket',
        default => 'fa-solid fa-circle',
    };
}

function panitia_header(string $title, string $active = ''): void
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
                <div class="role">Panel Panitia</div>
                <nav class="nav">
                    <?php foreach (panitia_nav_items() as $href => $label): ?>
                        <a class="<?= basename($href) === $active ? 'active' : '' ?>" href="<?= e($href) ?>"><i class="<?= e(panitia_nav_icon($href)) ?>" aria-hidden="true"></i><span><?= e($label) ?></span></a>
                    <?php endforeach; ?>
                </nav>
                <div class="sidebar-footer">UIN SAIZU Â· 2026</div>
            </aside>
            <main class="main">
                <div class="topbar">
                    <div>
                        <span class="topbar-eyebrow">Panitia</span>
                        <h1><?= e($title) ?></h1>
                    </div>
                    <div class="user-chip"><?= e($_SESSION['user']['nama'] ?? 'Panitia') ?></div>
                </div>
                <?php if ($msg = flash('success')): ?><div class="alert success"><?= e($msg) ?></div><?php endif; ?>
                <?php if ($msg = flash('error')): ?><div class="alert error"><?= e($msg) ?></div><?php endif; ?>
            <?php
}

function panitia_footer(): void
{
    echo '</main></div></body></html>';
}

function panitia_user_id(): int
{
    return (int) ($_SESSION['user']['id'] ?? 0);
}

function panitia_badge(?string $status): string
{
    $status = $status ?: 'pending';
    return '<span class="badge ' . e($status) . '">' . e(ucfirst($status)) . '</span>';
}

function panitia_competitions(): array
{
    return db_fetch_all('SELECT id, kode_lomba, nama_lomba, jenis FROM competitions WHERE is_active = 1 ORDER BY nama_lomba');
}

function panitia_json_array(?string $json): array
{
    $data = json_decode((string) $json, true);
    return is_array($data) ? $data : [];
}

function panitia_registration_detail(int $registrationId): ?array
{
    return db_fetch('SELECT r.*, u.nim, u.nama, u.email, u.fakultas, u.phone, c.nama_lomba, c.kode_lomba, c.jenis, c.requires_mentor FROM registrations r JOIN users u ON u.id = r.user_id JOIN competitions c ON c.id = r.competition_id WHERE r.id = ?', [$registrationId]);
}

function panitia_count_teams(int $registrationId): int
{
    $row = db_fetch('SELECT COUNT(*) AS total FROM teams WHERE registration_id = ?', [$registrationId]);
    return (int) ($row['total'] ?? 0);
}
