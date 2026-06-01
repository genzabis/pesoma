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

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/footer.php';

/**
 * Layout publik bersama untuk halaman di folder pages/.
 * Styling: assets/css/pesoma-public.css (self-contained, single source of truth).
 */
function public_header(string $title, string $active = ''): void
{
    $menu = [
        'beranda.php' => 'Beranda',
        'cabang-lomba.php' => 'Lomba',
        'jadwal.php' => 'Jadwal',
        'pengumuman.php' => 'Pengumuman',
        'tentang.php' => 'Tentang',
        'kontak.php' => 'Kontak',
    ];
?>
    <!DOCTYPE html>
    <html lang="id" class="scroll-smooth">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="theme-color" content="#0b2f9f">
        <meta name="description" content="Portal resmi PESOMA 2026 UIN Prof. K.H. Saifuddin Zuhri Purwokerto.">
        <title><?= e($title) ?> - <?= e(APP_NAME) ?></title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="<?= e(APP_URL) ?>/assets/css/pesoma-public.css?v=4">
    </head>

    <body>
        <header class="landing-header">
            <div class="navbar">
                <a href="<?= e(APP_URL) ?>/pages/beranda.php" class="brand">
                    <div class="brand-text">
                        <span>PESOMA 2026</span>
                        <small>UIN SAIZU Purwokerto</small>
                    </div>
                </a>
                <nav class="nav-links">
                    <?php foreach ($menu as $href => $label): ?>
                        <a href="<?= e(APP_URL) ?>/pages/<?= e($href) ?>" class="<?= $href === $active ? 'active' : '' ?>"><?= e($label) ?></a>
                    <?php endforeach; ?>
                    <?php if (is_logged_in()): ?>
                        <a href="<?= e(dashboard_url_by_role($_SESSION['user']['role'])) ?>" class="btn primary">Dashboard</a>
                    <?php else: ?>
                        <a href="<?= e(APP_URL) ?>/src/auth/login.php" class="btn primary">Login Portal</a>
                    <?php endif; ?>
                </nav>
            </div>
        </header>
        <main>
<?php
}
