<?php

declare(strict_types=1);

require_once __DIR__ . '/_response.php';

api_require_method('POST');
$input = api_input();
$uploadId = trim((string)($input['upload_id'] ?? $input['PHP_SESSION_UPLOAD_PROGRESS'] ?? ''));

if ($uploadId === '' || !preg_match('/^[A-Za-z0-9_-]{3,100}$/', $uploadId)) {
    api_error('upload_id tidak valid.', 400, ['progress' => 0]);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$key = ini_get('session.upload_progress.prefix') . $uploadId;
$info = $_SESSION[$key] ?? null;

if (!is_array($info)) {
    api_success('Progress upload belum tersedia atau upload sudah selesai.', [
        'upload_id' => $uploadId,
        'active' => false,
        'progress' => 0,
        'bytes_processed' => 0,
        'content_length' => 0,
    ]);
}

$bytes = (int)($info['bytes_processed'] ?? 0);
$length = (int)($info['content_length'] ?? 0);
$progress = $length > 0 ? min(100, round(($bytes / $length) * 100, 2)) : 0;

api_success('Progress upload berhasil diambil.', [
    'upload_id' => $uploadId,
    'active' => !($info['done'] ?? false),
    'done' => (bool)($info['done'] ?? false),
    'progress' => $progress,
    'bytes_processed' => $bytes,
    'content_length' => $length,
]);
