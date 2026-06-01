<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

/**
 * Navbar publik bersama. Panggil render_navbar('slug-aktif.php') di dalam <body>.
 */
function render_navbar(string $active = ''): void
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
    <header class="header">
        <div class="container nav">
            <a class="brand" href="<?= e(APP_URL) ?>/pages/beranda.php">PESOMA 2026</a>
            <nav class="menu">
                <?php foreach ($menu as $href => $label): ?>
                    <a class="<?= $href === $active ? 'active' : '' ?>" href="<?= e(APP_URL) ?>/pages/<?= e($href) ?>"><?= e($label) ?></a>
                <?php endforeach; ?>
                <?php if (is_logged_in()): ?>
                    <a class="btn" href="<?= e(dashboard_url_by_role($_SESSION['user']['role'])) ?>">Dashboard</a>
                <?php else: ?>
                    <a class="btn" href="<?= e(APP_URL) ?>/src/auth/login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
<?php
}
