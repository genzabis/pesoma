<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/_cron_helper.php';

cron_run('notify_finalists.php', function (): string {
    if (!cron_should_run_once('notify_finalists_2026_05_26_0800', new DateTimeImmutable('2026-05-26 08:00:00'))) {
        return 'Skipped; not scheduled or already sent.';
    }

    $rows = db_fetch_all(
        'SELECT f.id, r.nomor_peserta, u.nama, u.email, c.nama_lomba
         FROM finalists f
         JOIN registrations r ON r.id = f.registration_id
         JOIN users u ON u.id = r.user_id
         JOIN competitions c ON c.id = f.competition_id
         WHERE f.notification_sent = 0'
    );

    $sent = 0;
    foreach ($rows as $row) {
        $message = "Selamat {$row['nama']},\n\nAnda dinyatakan sebagai finalis PESOMA 2026 cabang {$row['nama_lomba']}. Nomor peserta: {$row['nomor_peserta']}. Silakan pantau jadwal final pada dashboard dan halaman pengumuman.\n";
        if (cron_mail((string) $row['email'], 'Pengumuman Finalis PESOMA 2026', $message)) {
            db_query('UPDATE finalists SET notification_sent = 1 WHERE id = ?', [(int) $row['id']]);
            $sent++;
        }
    }

    cron_insert_activity('cron_notify_finalists', 'Notifikasi finalis terkirim: ' . $sent . ' email.');
    return 'Emails sent: ' . $sent;
});
