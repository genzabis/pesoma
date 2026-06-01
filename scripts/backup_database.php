<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/_cron_helper.php';

cron_run('backup_database.php', function (): string {
    $backupDir = __DIR__ . '/../storage/backups';
    cron_ensure_dir($backupDir);

    $file = $backupDir . '/pesoma_' . date('Ymd_His') . '.sql';
    $pdo = db();
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    $handle = fopen($file, 'wb');
    if ($handle === false) {
        throw new RuntimeException('Gagal membuat file backup: ' . $file);
    }

    fwrite($handle, "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n");
    foreach ($tables as $table) {
        $create = $pdo->query('SHOW CREATE TABLE `' . str_replace('`', '``', (string) $table) . '`')->fetch(PDO::FETCH_ASSOC);
        fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n" . $create['Create Table'] . ";\n\n");

        $rows = $pdo->query('SELECT * FROM `' . str_replace('`', '``', (string) $table) . '`', PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $columns = array_map(fn($column) => '`' . str_replace('`', '``', (string) $column) . '`', array_keys($row));
            $values = array_map(fn($value) => $value === null ? 'NULL' : $pdo->quote((string) $value), array_values($row));
            fwrite($handle, 'INSERT INTO `' . $table . '` (' . implode(',', $columns) . ') VALUES (' . implode(',', $values) . ");\n");
        }
        fwrite($handle, "\n");
    }
    fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
    fclose($handle);

    cron_insert_activity('cron_backup_database', 'Backup database otomatis: ' . basename($file));
    return 'Backup created: ' . $file;
});
