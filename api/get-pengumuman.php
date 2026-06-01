<?php

declare(strict_types=1);

require_once __DIR__ . '/_response.php';

api_require_method('GET');

$type = trim((string)($_GET['type'] ?? ''));
$limit = api_limit($_GET, 10, 50);
$allowedTypes = ['umum', 'finalis', 'winner'];
$where = ['a.is_published = 1'];
$params = [];

if ($type !== '') {
    if (!in_array($type, $allowedTypes, true)) api_error('Tipe pengumuman tidak valid.', 400);
    $where[] = 'a.type = ?';
    $params[] = $type;
}

$params[] = $limit;
$rows = db_fetch_all('SELECT a.id, a.title, a.content, a.type, a.published_at, u.nama AS published_by FROM announcements a LEFT JOIN users u ON u.id = a.published_by WHERE ' . implode(' AND ', $where) . ' ORDER BY a.published_at DESC LIMIT ?', $params);

api_success('Pengumuman terbaru berhasil diambil.', ['items' => $rows, 'count' => count($rows)]);
