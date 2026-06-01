<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/_cron_helper.php';

cron_run('reminder_upload.php', function (): string {
    $deadline = new DateTimeImmutable('2026-05-18 23:59:00');
    $today = new DateTimeImmutable('today');
    $daysLeft = (int) $today->diff($deadline)->format('%r%a');
    if (!in_array($daysLeft, [7, 3, 1], true)) {
        return 'Skipped; not H-7/H-3/H-1.';
    }

    $rows = db_fetch_all(
        'SELECT r.id, r.nomor_peserta, u.nama, u.email, c.nama_lomba
         FROM registrations r
         JOIN users u ON u.id = r.user_id
         JOIN competitions c ON c.id = r.competition_id
         LEFT JOIN submissions s ON s.registration_id = r.id
         WHERE r.status_verifikasi = "diterima"
           AND s.id IS NULL
           AND (r.reminder_sent IS NULL OR r.reminder_sent <> CURDATE())'
    );

    $sent = 0;
    foreach ($rows as $row) {
        $subject = 'Reminder Upload Karya PESOMA 2026 H-' . $daysLeft;
        $message = "Yth. {$row['nama']},\n\nBatas upload karya PESOMA 2026 untuk {$row['nama_lomba']} adalah 18 Mei 2026 pukul 23:59 WIB. Silakan upload karya melalui dashboard peserta.\n\nNomor peserta: {$row['nomor_peserta']}\n";
        if (cron_mail((string) $row['email'], $subject, $message)) {
            db_query('UPDATE registrations SET reminder_sent = CURDATE(), updated_at = CURRENT_TIMESTAMP WHERE id = ?', [(int) $row['id']]);
            $sent++;
        }
    }

    cron_insert_activity('cron_reminder_upload', 'Reminder upload terkirim: ' . $sent . ' email.');
    return 'Emails sent: ' . $sent;
});
