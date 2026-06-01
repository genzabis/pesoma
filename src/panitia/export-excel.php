<?php

declare(strict_types=1);
require_once __DIR__ . '/_layout.php';

function csv_download(string $filename, array $headers, array $rows): never
{
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo "\xEF\xBB\xBF";
    $out = fopen('php://output', 'w');
    fputcsv($out, $headers, ';');
    foreach ($rows as $row) {
        fputcsv($out, $row, ';');
    }
    fclose($out);
    exit;
}

$type = $_GET['type'] ?? 'peserta';
$competitionId = (int)($_GET['competition_id'] ?? 0);
$params = [];
$where = '';
if ($competitionId > 0) {
    $where = ' WHERE r.competition_id = ?';
    $params[] = $competitionId;
}

if ($type === 'nilai') {
    $data = db_fetch_all('SELECT c.nama_lomba, r.nomor_peserta, u.nim, u.nama, COALESCE(AVG(sp.total),0) nilai_penyisihan, COALESCE(AVG(sf.total),0) nilai_final, f.rank_penyisihan, w.juara_ke FROM registrations r JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=r.competition_id LEFT JOIN submissions s ON s.registration_id=r.id LEFT JOIN scores_penyisihan sp ON sp.submission_id=s.id LEFT JOIN scores_final sf ON sf.registration_id=r.id LEFT JOIN finalists f ON f.registration_id=r.id LEFT JOIN winners w ON w.registration_id=r.id' . $where . ' GROUP BY c.nama_lomba,r.nomor_peserta,u.nim,u.nama,f.rank_penyisihan,w.juara_ke ORDER BY c.nama_lomba, nilai_penyisihan DESC', $params);
    $rows = array_map(fn($r) => [$r['nama_lomba'], $r['nomor_peserta'], $r['nim'], $r['nama'], number_format((float)$r['nilai_penyisihan'], 2, '.', ''), number_format((float)$r['nilai_final'], 2, '.', ''), $r['rank_penyisihan'] ?: '', $r['juara_ke'] ?: ''], $data);
    csv_download('laporan-nilai-pesoma-2026.csv', ['Cabang Lomba', 'No Peserta', 'NIM', 'Nama', 'Nilai Penyisihan', 'Nilai Final', 'Rank Finalis', 'Juara'], $rows);
}

if ($type === 'karya') {
    $data = db_fetch_all('SELECT c.nama_lomba, r.nomor_peserta, u.nim, u.nama, s.status, s.similarity_score, s.file_paths, s.original_names, s.uploaded_at FROM submissions s JOIN registrations r ON r.id=s.registration_id JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=r.competition_id' . $where . ' ORDER BY c.nama_lomba,u.nama', $params);
    $rows = array_map(function ($r) {
        $files = json_decode((string)$r['file_paths'], true);
        $names = json_decode((string)$r['original_names'], true);
        return [$r['nama_lomba'], $r['nomor_peserta'], $r['nim'], $r['nama'], $r['status'], $r['similarity_score'] ?? '', is_array($names) ? implode(' | ', $names) : '', is_array($files) ? implode(' | ', $files) : '', $r['uploaded_at']];
    }, $data);
    csv_download('laporan-karya-pesoma-2026.csv', ['Cabang Lomba', 'No Peserta', 'NIM', 'Nama', 'Status Karya', 'Similarity', 'Nama File', 'Path File', 'Tanggal Upload'], $rows);
}

$data = db_fetch_all('SELECT c.nama_lomba, c.jenis, r.nomor_peserta, r.status_verifikasi, r.created_at, u.nim, u.nama, u.email, u.fakultas, u.phone, (SELECT COUNT(*) FROM teams t WHERE t.registration_id=r.id) jumlah_anggota, (SELECT nama_dosen FROM mentors m WHERE m.registration_id=r.id LIMIT 1) pendamping, s.uploaded_at FROM registrations r JOIN users u ON u.id=r.user_id JOIN competitions c ON c.id=r.competition_id LEFT JOIN submissions s ON s.registration_id=r.id' . $where . ' ORDER BY c.nama_lomba,u.nama', $params);
$rows = array_map(fn($r) => [$r['nama_lomba'], $r['jenis'], $r['nomor_peserta'], $r['nim'], $r['nama'], $r['email'], $r['phone'], $r['fakultas'], $r['status_verifikasi'], $r['jumlah_anggota'], $r['pendamping'] ?: '', $r['uploaded_at'] ?: '', $r['created_at']], $data);
csv_download('laporan-peserta-pesoma-2026.csv', ['Cabang Lomba', 'Jenis', 'No Peserta', 'NIM', 'Nama', 'Email', 'HP', 'Fakultas', 'Status', 'Jumlah Anggota', 'Pendamping', 'Upload Karya', 'Tanggal Daftar'], $rows);
