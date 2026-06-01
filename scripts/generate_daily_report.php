<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/_cron_helper.php';

cron_run('generate_daily_report.php', function (): string {
    $reportDir = __DIR__ . '/../storage/reports/daily';
    cron_ensure_dir($reportDir);

    $summary = [
        'generated_at' => date('c'),
        'users' => db_fetch('SELECT COUNT(*) total, SUM(role = "peserta") peserta, SUM(role = "juri") juri, SUM(role = "panitia") panitia, SUM(role = "admin") admin FROM users'),
        'registrations' => db_fetch('SELECT COUNT(*) total, SUM(status_verifikasi = "pending") pending, SUM(status_verifikasi = "diterima") diterima, SUM(status_verifikasi = "ditolak") ditolak FROM registrations'),
        'submissions' => db_fetch('SELECT COUNT(*) total, SUM(status = "submitted") submitted, SUM(status = "reviewed") reviewed FROM submissions'),
        'finalists' => db_fetch('SELECT COUNT(*) total FROM finalists'),
        'winners' => db_fetch('SELECT COUNT(*) total FROM winners'),
    ];

    $file = $reportDir . '/daily_report_' . date('Ymd') . '.json';
    file_put_contents($file, json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    cron_insert_activity('cron_generate_daily_report', 'Laporan harian dibuat: ' . basename($file));
    return 'Report created: ' . $file;
});
