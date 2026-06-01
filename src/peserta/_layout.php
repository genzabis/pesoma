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
        <meta name="theme-color" content="#0c1733">
        <title><?= e($title) ?> — <?= e(APP_NAME) ?></title>
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
                <div class="role">Panel Peserta</div>
                <nav class="nav">
                    <?php foreach (peserta_nav_items() as $href => $label): ?>
                        <a class="<?= basename($href) === $active ? 'active' : '' ?>" href="<?= e($href) ?>"><i class="<?= e(peserta_nav_icon($href)) ?>" aria-hidden="true"></i><span><?= e($label) ?></span></a>
                    <?php endforeach; ?>
                </nav>
                <div class="sidebar-footer">UIN SAIZU · 2026</div>
            </aside>
            <main class="main">
                <div class="topbar">
                    <div>
                        <span class="topbar-eyebrow">Peserta</span>
                        <h1><?= e($title) ?></h1>
                    </div>
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
