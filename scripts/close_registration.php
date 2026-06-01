<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/_cron_helper.php';

cron_run('close_registration.php', function (): string {
    $deadline = '2026-04-27 23:59:00';
    if (date('Y-m-d H:i:s') < $deadline) {
        return 'Skipped; registration deadline not reached.';
    }

    $stmt = db_query(
        'UPDATE competitions SET registration_deadline = ?, updated_at = CURRENT_TIMESTAMP WHERE registration_deadline IS NULL OR registration_deadline > NOW()',
        [$deadline]
    );
    cron_insert_activity('cron_close_registration', 'Pendaftaran ditutup otomatis untuk ' . $stmt->rowCount() . ' cabang lomba.');
    return 'Updated competitions: ' . $stmt->rowCount();
});
