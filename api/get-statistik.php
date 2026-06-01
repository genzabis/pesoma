<?php

declare(strict_types=1);

require_once __DIR__ . '/_response.php';

api_require_method('GET');

$totalUsers = (int)(db_fetch('SELECT COUNT(*) AS total FROM users')['total'] ?? 0);
$totalPeserta = (int)(db_fetch('SELECT COUNT(*) AS total FROM users WHERE role = ?', ['peserta'])['total'] ?? 0);
$totalRegistrasi = (int)(db_fetch('SELECT COUNT(*) AS total FROM registrations')['total'] ?? 0);
$totalKarya = (int)(db_fetch('SELECT COUNT(*) AS total FROM submissions')['total'] ?? 0);
$totalCabang = (int)(db_fetch('SELECT COUNT(*) AS total FROM competitions WHERE is_active = 1')['total'] ?? 0);
$totalFinalis = (int)(db_fetch('SELECT COUNT(*) AS total FROM finalists')['total'] ?? 0);
$totalPemenang = (int)(db_fetch('SELECT COUNT(*) AS total FROM winners')['total'] ?? 0);

$registrasiByStatus = db_fetch_all('SELECT status_verifikasi, COUNT(*) AS total FROM registrations GROUP BY status_verifikasi ORDER BY status_verifikasi');
$pesertaByCabang = db_fetch_all('SELECT c.id AS competition_id, c.kode_lomba, c.nama_lomba, COUNT(r.id) AS total FROM competitions c LEFT JOIN registrations r ON r.competition_id = c.id GROUP BY c.id, c.kode_lomba, c.nama_lomba ORDER BY c.nama_lomba');

api_success('Statistik berhasil diambil.', [
    'summary' => [
        'total_users' => $totalUsers,
        'total_peserta' => $totalPeserta,
        'total_registrasi' => $totalRegistrasi,
        'total_karya' => $totalKarya,
        'total_cabang_lomba' => $totalCabang,
        'total_finalis' => $totalFinalis,
        'total_pemenang' => $totalPemenang,
    ],
    'registrasi_by_status' => $registrasiByStatus,
    'peserta_by_cabang' => $pesertaByCabang,
]);
