<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('upload-karya.php');
}

/**
 * Field upload yang diizinkan per cabang lomba beserta ekstensinya.
 *
 * @return array<string,array<int,string>>
 */
function upload_fields_for(array $competition): array
{
    $kode = strtoupper((string) $competition['kode_lomba']);
    $nama = strtolower((string) $competition['nama_lomba']);
    if ($kode === 'INOVASI') return ['artikel' => ['docx'], 'ppt' => ['pptx'], 'prototipe' => ['jpg', 'jpeg', 'png', 'mp4', 'zip']];
    if ($kode === 'FILM') return ['video' => ['mp4'], 'poster' => ['jpg', 'jpeg', 'pdf']];
    if ($kode === 'POSTER') return ['poster' => ['jpg', 'jpeg', 'pdf']];
    if ($kode === 'VOKAL' || str_contains($nama, 'puisi')) return ['video' => ['mp4']];
    return ['karya' => ['doc', 'docx', 'ppt', 'pptx', 'pdf', 'jpg', 'jpeg', 'png', 'mp4', 'zip']];
}

/**
 * Daftar MIME yang sah untuk sebuah ekstensi.
 *
 * @return array<int,string>
 */
function allowed_mimes_for_ext(string $ext): array
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

function upload_subdir_for(string $field, string $ext): string
{
    if ($ext === 'mp4') return 'video';
    if (in_array($ext, ['jpg', 'jpeg', 'png'], true)) return $field === 'poster' ? 'poster' : 'prototipe';
    if (in_array($ext, ['ppt', 'pptx'], true)) return 'ppt';
    if ($field === 'artikel' || in_array($ext, ['doc', 'docx', 'pdf'], true)) return 'artikel';
    return 'prototipe';
}

if (!verify_csrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Token keamanan tidak valid.');
    redirect('upload-karya.php');
}

$selectedRegId = (int) ($_POST['registration_id'] ?? 0);
$registration = $selectedRegId ? db_fetch(
    'SELECT r.*, c.nama_lomba, c.kode_lomba, c.jenis, c.upload_deadline, c.is_upload_open
     FROM registrations r
     JOIN competitions c ON c.id = r.competition_id
     WHERE r.id = ? AND r.user_id = ?',
    [$selectedRegId, current_user_id()]
) : null;

$errors = [];
if (!$registration) {
    $errors[] = 'Pendaftaran tidak ditemukan.';
}

if ($registration && (int) $registration['is_upload_open'] !== 1) {
    $errors[] = 'Upload karya untuk lomba ini sedang ditutup.';
}

if ($registration && !empty($registration['upload_deadline']) && time() > strtotime((string) $registration['upload_deadline'])) {
    $errors[] = 'Batas waktu upload telah lewat.';
}

$paths = [];
$originals = [];

if ($registration && $errors === []) {
    $uploadDir = __DIR__ . '/../../public/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    foreach (upload_fields_for($registration) as $field => $allowedExts) {
        if (empty($_FILES[$field]['name'])) {
            continue;
        }
        if (!is_uploaded_file($_FILES[$field]['tmp_name'])) {
            $errors[] = 'File ' . $field . ' tidak valid.';
            continue;
        }
        $ext = strtolower(pathinfo((string) $_FILES[$field]['name'], PATHINFO_EXTENSION));
        $max = $ext === 'mp4' ? UPLOAD_VIDEO_MAX_SIZE : UPLOAD_DOC_MAX_SIZE;
        if (!in_array($ext, $allowedExts, true)) {
            $errors[] = 'Ekstensi file ' . $field . ' tidak valid.';
            continue;
        }
        if ((int) $_FILES[$field]['size'] > $max) {
            $errors[] = 'Ukuran file ' . $field . ' melebihi batas.';
            continue;
        }
        if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload file ' . $field . ' gagal.';
            continue;
        }
        $mime = $finfo->file($_FILES[$field]['tmp_name']) ?: '';
        if (!in_array($mime, allowed_mimes_for_ext($ext), true)) {
            $errors[] = 'Tipe MIME file ' . $field . ' tidak diizinkan.';
            continue;
        }
        $subdir = upload_subdir_for($field, $ext);
        $targetDir = $uploadDir . $subdir . '/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }
        $filename = uniqid('', true) . '_' . time() . '.' . $ext;
        if (!move_uploaded_file($_FILES[$field]['tmp_name'], $targetDir . $filename)) {
            $errors[] = 'Gagal menyimpan file ' . $field . '.';
            continue;
        }
        $paths[$field] = 'public/uploads/' . $subdir . '/' . $filename;
        $originals[$field] = basename((string) $_FILES[$field]['name']);
    }
    if (!$paths) {
        $errors[] = 'Minimal satu file karya wajib diupload.';
    }
}

if ($errors !== []) {
    flash('error', implode(' ', $errors));
    redirect('upload-karya.php?registration_id=' . $selectedRegId);
}

db_query(
    'INSERT INTO submissions (registration_id, file_paths, original_names, status) VALUES (?, ?, ?, "submitted")
     ON DUPLICATE KEY UPDATE file_paths = VALUES(file_paths), original_names = VALUES(original_names), status = "submitted", uploaded_at = CURRENT_TIMESTAMP',
    [$selectedRegId, json_encode($paths), json_encode($originals)]
);
log_activity(current_user_id(), ROLE_PESERTA, 'submission_upload', 'Upload karya untuk ' . $registration['nama_lomba']);
flash('success', 'Karya berhasil diupload.');
redirect('upload-karya.php?registration_id=' . $selectedRegId);
