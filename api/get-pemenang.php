<?php

declare(strict_types=1);

require_once __DIR__ . '/_response.php';

api_require_method('GET');

$competitionId = api_int_param($_GET, 'competition_id', 0);
$kodeLomba = trim((string)($_GET['kode_lomba'] ?? ''));
$where = [];
$params = [];

if ($competitionId > 0) {
    $where[] = 'w.competition_id = ?';
    $params[] = $competitionId;
}
if ($kodeLomba !== '') {
    if (!preg_match('/^[A-Za-z0-9_-]{2,30}$/', $kodeLomba)) api_error('Kode lomba tidak valid.', 400);
    $where[] = 'c.kode_lomba = ?';
    $params[] = $kodeLomba;
}

$sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$rows = db_fetch_all("SELECT w.id, w.juara_ke, w.total_score, w.announced_at, r.id AS registration_id, r.nomor_peserta, u.nama AS nama_peserta, u.nim, u.fakultas, c.id AS competition_id, c.kode_lomba, c.nama_lomba FROM winners w JOIN registrations r ON r.id = w.registration_id JOIN users u ON u.id = r.user_id JOIN competitions c ON c.id = w.competition_id $sqlWhere ORDER BY c.nama_lomba ASC, w.juara_ke ASC", $params);

api_success('Daftar pemenang berhasil diambil.', ['items' => $rows, 'count' => count($rows)]);
