<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/_cron_helper.php';

cron_run('close_upload.php', function (): string {
    $deadline = '2026-05-18 23:59:00';
    if (date('Y-m-d H:i:s') < $deadline) {
        return 'Skipped; upload deadline not reached.';
    }

    $stmt = db_query(
        'UPDATE competitions SET is_upload_open = 0, upload_deadline = ?, updated_at = CURRENT_TIMESTAMP WHERE is_upload_open = 1 OR upload_deadline IS NULL OR upload_deadline > ?',
        [$deadline, $deadline]
    );
    cron_insert_activity('cron_close_upload', 'Upload karya ditutup otomatis untuk ' . $stmt->rowCount() . ' cabang lomba.');
    return 'Updated competitions: ' . $stmt->rowCount();
});
