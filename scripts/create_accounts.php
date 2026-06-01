<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Admin account
db_query('INSERT INTO users (email, password, nama, role, is_active) VALUES (?, ?, ?, ?, 1) ON DUPLICATE KEY UPDATE password=VALUES(password)', [
    'admin@pesoma.local',
    password_hash('admin', PASSWORD_BCRYPT),
    'Administrator',
    'admin'
]);

// Panitia account
db_query('INSERT INTO users (email, password, nama, role, is_active) VALUES (?, ?, ?, ?, 1) ON DUPLICATE KEY UPDATE password=VALUES(password)', [
    'panitia1@pesoma.local',
    password_hash('Pesoma2026', PASSWORD_BCRYPT),
    'Panitia 1',
    'panitia'
]);

// Juri account
db_query('INSERT INTO users (email, password, nama, role, is_active) VALUES (?, ?, ?, ?, 1) ON DUPLICATE KEY UPDATE password=VALUES(password)', [
    'juri.seni@pesoma.local',
    password_hash('Pesoma2026', PASSWORD_BCRYPT),
    'Juri Seni',
    'juri'
]);

// Peserta account
db_query('INSERT INTO users (email, password, nama, nim, fakultas, role, is_active) VALUES (?, ?, ?, ?, ?, ?, 1) ON DUPLICATE KEY UPDATE password=VALUES(password)', [
    'ahmad@student.local',
    password_hash('Pesoma2026', PASSWORD_BCRYPT),
    'Ahmad Rizki',
    '2024001',
    'Fakultas Sains dan Teknologi',
    'peserta'
]);

echo 'Akun berhasil dibuat!' . PHP_EOL;
echo '- Admin: admin@pesoma.local / admin' . PHP_EOL;
echo '- Panitia: panitia1@pesoma.local / Pesoma2026' . PHP_EOL;
echo '- Juri: juri.seni@pesoma.local / Pesoma2026' . PHP_EOL;
echo '- Peserta: ahmad@student.local / Pesoma2026' . PHP_EOL;
