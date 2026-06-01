<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/_cron_helper.php';

cron_run('cleanup_temp_files.php', function (): string {
    $dirs = [
        __DIR__ . '/../storage/temp',
        __DIR__ . '/../public/uploads/temp',
        __DIR__ . '/../public/uploads/tmp',
    ];
    $threshold = time() - (7 * 24 * 60 * 60);
    $deleted = 0;

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            continue;
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getMTime() < $threshold && @unlink($file->getPathname())) {
                $deleted++;
            }
        }
    }

    cron_insert_activity('cron_cleanup_temp_files', 'File temp dihapus: ' . $deleted);
    return 'Deleted files: ' . $deleted;
});
