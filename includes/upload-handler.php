<?php

declare(strict_types=1);


/**
 * PESOMA III 2026 - UIN Prof. K.H. Saifuddin Zuhri Purwokerto
 * Copyright (c) 2026 Tim Pengembang PESOMA III. All Rights Reserved.
 *
 * This file is part of a proprietary software project. Unauthorized
 * copying, redistribution, or use of this file, via any medium, is
 * strictly prohibited. See LICENSE for the full terms.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/constants.php';

/**
 * Helper upload file karya yang dapat dipakai ulang.
 * Mengembalikan path relatif tersimpan atau melempar RuntimeException bila gagal.
 */

/**
 * MIME yang sah untuk sebuah ekstensi.
 *
 * @return array<int,string>
 */
function uh_allowed_mimes(string $ext): array
{
    return match ($ext) {
        'doc' => ['application/msword', 'application/x-msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
        'ppt' => ['application/vnd.ms-powerpoint', 'application/mspowerpoint', 'application/powerpoint'],
        'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip'],
        'pdf' => ['application/pdf'],
        'jpg', 'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'mp4' => ['video/mp4', 'application/mp4'],
        'zip' => ['application/zip', 'application/x-zip-compressed', 'multipart/x-zip'],
        default => [],
    };
}

/**
 * Validasi & simpan satu file upload.
 *
 * @param array<string,mixed> $file       Entri dari $_FILES.
 * @param array<int,string>   $allowedExts Ekstensi yang diizinkan.
 * @param string              $subdir      Subfolder tujuan di public/uploads.
 * @return string Path relatif tersimpan (mis. public/uploads/artikel/xxx.docx).
 * @throws RuntimeException Bila file tidak valid.
 */
function uh_store_upload(array $file, array $allowedExts, string $subdir): string
{
    if (empty($file['name'])) {
        throw new RuntimeException('File tidak ditemukan.');
    }
    if (!is_uploaded_file($file['tmp_name'] ?? '')) {
        throw new RuntimeException('File tidak valid.');
    }
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload gagal.');
    }

    $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts, true) || !in_array($ext, ALLOWED_UPLOAD_EXTENSIONS, true)) {
        throw new RuntimeException('Ekstensi file tidak diizinkan.');
    }

    $max = $ext === 'mp4' ? UPLOAD_VIDEO_MAX_SIZE : UPLOAD_DOC_MAX_SIZE;
    if ((int) ($file['size'] ?? 0) > $max) {
        throw new RuntimeException('Ukuran file melebihi batas.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']) ?: '';
    if (!in_array($mime, uh_allowed_mimes($ext), true)) {
        throw new RuntimeException('Tipe MIME file tidak diizinkan.');
    }

    $baseDir = __DIR__ . '/../public/uploads/';
    $targetDir = $baseDir . trim($subdir, '/') . '/';
    if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
        throw new RuntimeException('Gagal menyiapkan direktori upload.');
    }

    $filename = uniqid('', true) . '_' . time() . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $targetDir . $filename)) {
        throw new RuntimeException('Gagal menyimpan file.');
    }

    return 'public/uploads/' . trim($subdir, '/') . '/' . $filename;
}
