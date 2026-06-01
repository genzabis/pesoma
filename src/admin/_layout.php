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

require_role(ROLE_ADMIN);

function admin_nav_items(): array
{
    return [
        'dashboard.php' => 'Dashboard',
        'kelola-user.php' => 'Kelola User',
        'kelola-cabang-lomba.php' => 'Kelola Cabang Lomba',
        'kelola-aspek.php' => 'Kelola Aspek Penilaian',
        'kelola-jadwal.php' => 'Kelola Jadwal',
        'backup-database.php' => 'Backup Database',
        'log-aktivitas.php' => 'Log Aktivitas',
        'pengaturan.php' => 'Pengaturan',
        '../auth/logout.php' => 'Logout',
    ];
}

function admin_nav_icon(string $href): string
{
    return match (basename($href)) {
        'dashboard.php' => 'fa-solid fa-chart-line',
        'kelola-user.php' => 'fa-solid fa-users-gear',
        'kelola-cabang-lomba.php' => 'fa-solid fa-trophy',
        'kelola-aspek.php' => 'fa-solid fa-clipboard-list',
        'kelola-jadwal.php' => 'fa-solid fa-calendar-days',
        'backup-database.php' => 'fa-solid fa-database',
        'log-aktivitas.php' => 'fa-solid fa-clock-rotate-left',
        'pengaturan.php' => 'fa-solid fa-gear',
        'logout.php' => 'fa-solid fa-right-from-bracket',
        default => 'fa-solid fa-circle',
    };
}

function admin_header(string $title, string $active = ''): void
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
                <div class="role">Panel Admin</div>
                <nav class="nav">
                    <?php foreach (admin_nav_items() as $href => $label): ?>
                        <a class="<?= basename($href) === $active ? 'active' : '' ?>" href="<?= e($href) ?>"><i class="<?= e(admin_nav_icon($href)) ?>" aria-hidden="true"></i><span><?= e($label) ?></span></a>
                    <?php endforeach; ?>
                </nav>
                <div class="sidebar-footer">UIN SAIZU Â· 2026</div>
            </aside>
            <main class="main">
                <div class="topbar">
                    <div>
                        <span class="topbar-eyebrow">Admin</span>
                        <h1><?= e($title) ?></h1>
                    </div>
                    <div class="user-chip"><?= e($_SESSION['user']['nama'] ?? 'Admin') ?></div>
                </div>
                <?php if ($msg = flash('success')): ?><div class="alert success"><?= e($msg) ?></div><?php endif; ?>
                <?php if ($msg = flash('error')): ?><div class="alert error"><?= e($msg) ?></div><?php endif; ?>
            <?php
}

function admin_footer(): void
{
    echo '</main></div></body></html>';
}

function admin_id(): int
{
    return (int)($_SESSION['user']['id'] ?? 0);
}

function admin_redirect(string $path): never
{
    redirect($path);
}

function admin_table_exists(string $table): bool
{
    try {
        return (bool) db_fetch('SHOW TABLES LIKE ?', [$table]);
    } catch (Throwable $e) {
        return false;
    }
}

function admin_column_exists(string $table, string $column): bool
{
    try {
        return (bool) db_fetch("SHOW COLUMNS FROM `$table` LIKE ?", [$column]);
    } catch (Throwable $e) {
        return false;
    }
}

function admin_exec_safe(string $sql): void
{
    try {
        db()->exec($sql);
    } catch (Throwable $e) {
        error_log('[ADMIN_SCHEMA] ' . $e->getMessage());
    }
}

function admin_ensure_schema(): void
{
    if (!admin_table_exists('aspek_penilaian')) {
        admin_exec_safe("CREATE TABLE aspek_penilaian (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, competition_id BIGINT UNSIGNED NOT NULL, babak ENUM('penyisihan','final') NOT NULL, aspek_name VARCHAR(160) NOT NULL, nama_aspek VARCHAR(160) GENERATED ALWAYS AS (aspek_name) VIRTUAL, bobot_persen DECIMAL(5,2) NOT NULL DEFAULT 0, bobot DECIMAL(5,2) GENERATED ALWAYS AS (bobot_persen) VIRTUAL, urutan INT UNSIGNED NOT NULL DEFAULT 1, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY(id), KEY idx_aspek_comp_babak (competition_id,babak), CONSTRAINT fk_aspek_competition FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }
    if (!admin_table_exists('settings')) {
        admin_exec_safe("CREATE TABLE settings (setting_key VARCHAR(100) NOT NULL, setting_value TEXT NULL, updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY(setting_key)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }
    foreach (
        [
            'min_anggota' => 'ALTER TABLE competitions ADD min_anggota TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER kategori',
            'need_mentor' => 'ALTER TABLE competitions ADD need_mentor TINYINT(1) NOT NULL DEFAULT 0 AFTER max_anggota',
            'has_penyisihan' => 'ALTER TABLE competitions ADD has_penyisihan TINYINT(1) NOT NULL DEFAULT 1 AFTER need_mentor',
            'has_final' => 'ALTER TABLE competitions ADD has_final TINYINT(1) NOT NULL DEFAULT 1 AFTER has_penyisihan',
        ] as $col => $sql
    ) {
        if (!admin_column_exists('competitions', $col)) admin_exec_safe($sql);
    }
}

function admin_setting(string $key, string $default = ''): string
{
    admin_ensure_schema();
    $row = db_fetch('SELECT setting_value FROM settings WHERE setting_key=?', [$key]);
    return (string)($row['setting_value'] ?? $default);
}

function admin_save_setting(string $key, string $value): void
{
    admin_ensure_schema();
    db_query('INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value), updated_at=CURRENT_TIMESTAMP', [$key, $value]);
}

admin_ensure_schema();
