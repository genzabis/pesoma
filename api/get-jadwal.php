<?php

declare(strict_types=1);

require_once __DIR__ . '/_response.php';

api_require_method('GET');

$date = trim((string)($_GET['date'] ?? ''));
$from = trim((string)($_GET['from'] ?? ''));
$to = trim((string)($_GET['to'] ?? ''));
$limit = api_limit($_GET, 50, 100);

$where = ['is_public = 1'];
$params = [];

if ($date !== '') {
    if (!api_valid_date($date)) api_error('Parameter date harus berformat YYYY-MM-DD.', 400);
    $where[] = 'event_date = ?';
    $params[] = $date;
} else {
    if ($from !== '') {
        if (!api_valid_date($from)) api_error('Parameter from harus berformat YYYY-MM-DD.', 400);
        $where[] = 'event_date >= ?';
        $params[] = $from;
    }
    if ($to !== '') {
        if (!api_valid_date($to)) api_error('Parameter to harus berformat YYYY-MM-DD.', 400);
        $where[] = 'event_date <= ?';
        $params[] = $to;
    }
}

// LIMIT tidak bisa di-bind sebagai parameter saat EMULATE_PREPARES=false; $limit sudah
// dibatasi integer aman oleh api_limit() sehingga aman disisipkan via %d.
$rows = db_fetch_all('SELECT id, event_name, event_date, event_time, location, link, description FROM schedules WHERE ' . implode(' AND ', $where) . sprintf(' ORDER BY event_date ASC, event_time ASC LIMIT %d', $limit), $params);

api_success('Jadwal kegiatan berhasil diambil.', ['items' => $rows, 'count' => count($rows)]);
