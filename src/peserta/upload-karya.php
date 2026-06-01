<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

$errors = [];
$registrations = peserta_registrations();
$selectedRegId = (int) ($_POST['registration_id'] ?? $_GET['registration_id'] ?? ($registrations[0]['id'] ?? 0));
$registration = null;
foreach ($registrations as $reg) {
    if ((int) $reg['id'] === $selectedRegId) {
        $registration = $reg;
        break;
    }
}

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) $errors[] = 'Token keamanan tidak valid.';
    if (!$registration) $errors[] = 'Pendaftaran tidak ditemukan.';
    $paths = [];
    $originals = [];
    if ($registration && $errors === []) {
        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
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
            if (!is_dir($targetDir)) mkdir($targetDir, 0775, true);
            $filename = uniqid('', true) . '_' . time() . '.' . $ext;
            if (!move_uploaded_file($_FILES[$field]['tmp_name'], $targetDir . $filename)) {
                $errors[] = 'Gagal menyimpan file ' . $field . '.';
                continue;
            }
            $paths[$field] = 'public/uploads/' . $subdir . '/' . $filename;
            $originals[$field] = basename((string) $_FILES[$field]['name']);
        }
        if (!$paths) $errors[] = 'Minimal satu file karya wajib diupload.';
    }
    if ($registration && $errors === []) {
        db_query('INSERT INTO submissions (registration_id, file_paths, original_names, status) VALUES (?, ?, ?, "submitted") ON DUPLICATE KEY UPDATE file_paths = VALUES(file_paths), original_names = VALUES(original_names), status = "submitted", uploaded_at = CURRENT_TIMESTAMP', [$selectedRegId, json_encode($paths), json_encode($originals)]);
        log_activity(current_user_id(), ROLE_PESERTA, 'submission_upload', 'Upload karya untuk ' . $registration['nama_lomba']);
        flash('success', 'Karya berhasil diupload.');
        redirect('upload-karya.php?registration_id=' . $selectedRegId);
    }
}

$submission = $registration ? db_fetch('SELECT * FROM submissions WHERE registration_id = ?', [$selectedRegId]) : null;
peserta_header('Upload Karya', 'upload-karya.php');
foreach ($errors as $error) echo '<div class="alert error">' . e($error) . '</div>';
?>
<section class="card">
    <?php if (!$registrations): ?><p class="muted">Anda belum mendaftar lomba. Silakan daftar lomba terlebih dahulu.</p><a class="btn" href="daftar-lomba.php">Daftar Lomba</a><?php else: ?>
        <form method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="field"><label>Pilih Pendaftaran</label><select name="registration_id" onchange="location.href='upload-karya.php?registration_id='+this.value"><?php foreach ($registrations as $reg): ?><option value="<?= (int) $reg['id'] ?>" <?= $selectedRegId === (int) $reg['id'] ? 'selected' : '' ?>><?= e($reg['nama_lomba']) ?> - <?= e($reg['nomor_peserta']) ?></option><?php endforeach; ?></select></div>
            <?php if ($registration): ?><p>Batas upload: <span class="deadline"><?= e(date('d M Y H:i', strtotime($registration['upload_deadline']))) ?> WIB</span></p><?php foreach (upload_fields_for($registration) as $field => $exts): ?><div class="field"><label><?= e(ucfirst($field)) ?> (<?= e(implode(', ', $exts)) ?>)</label><input type="file" name="<?= e($field) ?>"></div><?php endforeach; ?><button class="btn" type="submit">Upload Karya</button><?php endif; ?>
        </form>
        <?php if ($submission): ?>
            <hr>
            <h3>File Terunggah</h3>
            <pre><?= e(json_encode(json_decode($submission['original_names'] ?: '{}', true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre><?php endif; ?>
    <?php endif; ?>
</section>
<?php peserta_footer(); ?>