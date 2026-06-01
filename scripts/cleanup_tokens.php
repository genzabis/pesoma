<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/_cron_helper.php';

cron_run('cleanup_tokens.php', function (): string {
    $stmt = db_query('DELETE FROM password_resets WHERE created_at < (NOW() - INTERVAL 1 HOUR)');
    cron_insert_activity('cron_cleanup_tokens', 'Token reset password expired dihapus: ' . $stmt->rowCount());
    return 'Deleted tokens: ' . $stmt->rowCount();
});
