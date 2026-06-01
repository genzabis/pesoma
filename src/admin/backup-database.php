<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

function sql_quote_value(mixed $value): string
{
    if ($value === null) return 'NULL';
    return db()->quote((string)$value);
}

if (isset($_GET['download'])) {
    $filename = 'backup-pesoma-2026-' . date('Ymd-His') . '.sql';
    header('Content-Type: application/sql; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo "-- Backup Portal PESOMA 2026\n-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
    echo "SET FOREIGN_KEY_CHECKS=0;\n\n";
    $tables = db_fetch_all('SHOW TABLES');
    foreach ($tables as $tableRow) {
        $table = (string)array_values($tableRow)[0];
        $create = db_fetch('SHOW CREATE TABLE `' . str_replace('`', '``', $table) . '`');
        echo "DROP TABLE IF EXISTS `$table`;\n";
        echo ($create['Create Table'] ?? array_values($create ?? [])[1] ?? '') . ";\n\n";
        $rows = db_fetch_all('SELECT * FROM `' . str_replace('`', '``', $table) . '`');
        foreach ($rows as $row) {
            $cols = array_map(fn($c) => '`' . str_replace('`', '``', (string)$c) . '`', array_keys($row));
            $vals = array_map('sql_quote_value', array_values($row));
            echo 'INSERT INTO `' . $table . '` (' . implode(',', $cols) . ') VALUES (' . implode(',', $vals) . ");\n";
        }
        echo "\n";
    }
    echo "SET FOREIGN_KEY_CHECKS=1;\n";
    log_activity(admin_id(), ROLE_ADMIN, 'backup_database', 'Download backup database');
    exit;
}

$tables = db_fetch_all('SELECT table_name, table_rows, ROUND((data_length+index_length)/1024/1024,2) size_mb FROM information_schema.tables WHERE table_schema=? ORDER BY table_name', [DB_NAME]);
admin_header('Backup Database', 'backup-database.php');
?>
<section class="card">
    <h2>Backup Database</h2>
    <p class="muted">Unduh dump SQL berisi struktur dan data seluruh tabel database <b><?= e(DB_NAME) ?></b>. Simpan file ini di lokasi aman.</p>
    <a class="btn" href="backup-database.php?download=1">Download Backup SQL</a>
</section>
<section class="card">
    <h2>Ringkasan Tabel</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Tabel</th>
                <th>Estimasi Baris</th>
                <th>Ukuran MB</th>
            </tr>
        </thead>
        <tbody><?php foreach ($tables as $t): ?><tr>
                    <td><?= e($t['table_name']) ?></td>
                    <td><?= e((string)$t['table_rows']) ?></td>
                    <td><?= e((string)$t['size_mb']) ?></td>
                </tr><?php endforeach; ?></tbody>
    </table>
</section>
<?php admin_footer(); ?>