<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/_cron_helper.php';

cron_run('notify_winners.php', function (): string {
    if (!cron_should_run_once('notify_winners_2026_06_30_1000', new DateTimeImmutable('2026-06-30 10:00:00'))) {
        return 'Skipped; not scheduled or already sent.';
    }

    $rows = db_fetch_all(
        'SELECT w.id, w.juara_ke, r.nomor_peserta, u.nama, u.email, c.nama_lomba
         FROM winners w
         JOIN registrations r ON r.id = w.registration_id
         JOIN users u ON u.id = r.user_id
         JOIN competitions c ON c.id = w.competition_id
         WHERE w.notification_sent = 0'
    );

    $sent = 0;
    foreach ($rows as $row) {
        $message = "Selamat {$row['nama']},\n\nAnda meraih Juara {$row['juara_ke']} PESOMA 2026 cabang {$row['nama_lomba']}. Nomor peserta: {$row['nomor_peserta']}.\n";
        if (cron_mail((string) $row['email'], 'Pengumuman Pemenang PESOMA 2026', $message)) {
            db_query('UPDATE winners SET notification_sent = 1 WHERE id = ?', [(int) $row['id']]);
            $sent++;
        }
    }

    cron_insert_activity('cron_notify_winners', 'Notifikasi pemenang terkirim: ' . $sent . ' email.');
    return 'Emails sent: ' . $sent;
});
