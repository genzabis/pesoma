<?php

declare(strict_types=1);

require_once __DIR__ . '/_response.php';

api_require_method('GET');

$competitionId = api_int_param($_GET, 'competition_id', 0);
$kodeLomba = trim((string)($_GET['kode_lomba'] ?? ''));
$where = [];
$params = [];

if ($competitionId > 0) {
    $where[] = 'f.competition_id = ?';
    $params[] = $competitionId;
}
if ($kodeLomba !== '') {
    if (!preg_match('/^[A-Za-z0-9_-]{2,30}$/', $kodeLomba)) api_error('Kode lomba tidak valid.', 400);
    $where[] = 'c.kode_lomba = ?';
    $params[] = $kodeLomba;
}

$sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$rows = db_fetch_all("SELECT f.id, f.rank_penyisihan, f.announced_at, r.id AS registration_id, r.nomor_peserta, u.nama AS nama_peserta, u.nim, u.fakultas, c.id AS competition_id, c.kode_lomba, c.nama_lomba FROM finalists f JOIN registrations r ON r.id = f.registration_id JOIN users u ON u.id = r.user_id JOIN competitions c ON c.id = f.competition_id $sqlWhere ORDER BY c.nama_lomba ASC, f.rank_penyisihan IS NULL ASC, f.rank_penyisihan ASC, u.nama ASC", $params);

api_success('Daftar finalis berhasil diambil.', ['items' => $rows, 'count' => count($rows)]);
