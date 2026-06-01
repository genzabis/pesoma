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
$existingPaths     = $submission ? (json_decode((string) ($submission['file_paths'] ?? '{}'), true) ?: []) : [];
$existingOriginals = $submission ? (json_decode((string) ($submission['original_names'] ?? '{}'), true) ?: []) : [];

function preview_kind_for(string $path): string
{
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) return 'image';
    if (in_array($ext, ['mp4', 'webm', 'mov'], true)) return 'video';
    if ($ext === 'pdf') return 'pdf';
    return 'file';
}

function preview_icon_for(string $kind): string
{
    return match ($kind) {
        'image' => 'fa-image',
        'video' => 'fa-video',
        'pdf'   => 'fa-file-pdf',
        default => 'fa-file',
    };
}

peserta_header('Upload Karya', 'upload-karya.php');
foreach ($errors as $error) echo '<div class="alert error">' . e($error) . '</div>';
?>

<div class="section-head">
    <span class="section-eyebrow">Pengumpulan Karya</span>
    <h2 class="section-title">Upload karya lomba.</h2>
</div>

<?php if (!$registrations): ?>
    <section class="card">
        <p class="muted" style="margin: 0 0 16px;">Anda belum mendaftar pada lomba apa pun. Daftar dulu untuk bisa mengunggah karya.</p>
        <a class="btn" href="daftar-lomba.php">Daftar Lomba</a>
    </section>
<?php else: ?>

    <div class="grid">
        <section class="card span-8">
            <h3>Form Pengumpulan</h3>
            <form method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="field">
                    <label>Pilih Pendaftaran</label>
                    <select name="registration_id" onchange="location.href='upload-karya.php?registration_id='+this.value">
                        <?php foreach ($registrations as $reg): ?>
                            <option value="<?= (int) $reg['id'] ?>" <?= $selectedRegId === (int) $reg['id'] ? 'selected' : '' ?>>
                                <?= e($reg['nama_lomba']) ?> · <?= e($reg['nomor_peserta']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($registration): ?>
                    <?php $deadline = strtotime((string) $registration['upload_deadline']); $now = time(); $expired = $deadline && $now > $deadline; ?>
                    <div class="alert <?= $expired ? 'error' : 'info' ?>" style="margin-bottom: 18px;">
                        <strong>Batas upload:</strong>&nbsp;<?= e(date('d M Y H:i', $deadline)) ?> WIB
                        <?php if ($expired): ?>· <span>Deadline sudah lewat</span><?php endif; ?>
                    </div>

                    <?php foreach (upload_fields_for($registration) as $field => $exts): ?>
                        <div class="field">
                            <label><?= e(ucfirst($field)) ?>
                                <span style="font-family: var(--ff-mono); font-size: 10px; font-weight: 500; text-transform: uppercase; letter-spacing: .12em; color: var(--c-ink-mute); margin-left: 8px;">
                                    <?= e(implode(' · ', $exts)) ?>
                                </span>
                            </label>
                            <input type="file" name="<?= e($field) ?>" accept=".<?= e(implode(',.', $exts)) ?>">
                            <?php if (!empty($existingOriginals[$field])): ?>
                                <div style="margin-top: 6px; font-size: 12px; color: var(--c-ink-mute);">
                                    Sudah terunggah: <strong style="color: var(--c-ink);"><?= e($existingOriginals[$field]) ?></strong>. Pilih file baru untuk mengganti.
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <button class="btn" type="submit"><i class="fa-solid fa-cloud-arrow-up"></i> Upload / Perbarui Karya</button>
                <?php endif; ?>
            </form>
        </section>

        <section class="card span-4">
            <h3>File Terunggah</h3>
            <?php if (!$existingPaths): ?>
                <p class="muted" style="margin: 0;">Belum ada file yang diupload untuk pendaftaran ini.</p>
            <?php else: ?>
                <div style="display: grid; gap: 14px;">
                    <?php foreach ($existingPaths as $field => $path): ?>
                        <?php
                        $kind = preview_kind_for((string) $path);
                        $url  = e(APP_URL . '/' . $path);
                        $name = e($existingOriginals[$field] ?? basename((string) $path));
                        ?>
                        <div style="border: 1px solid var(--c-line); border-radius: 12px; overflow: hidden;">
                            <div style="aspect-ratio: 16/10; background: #fafafa; display: grid; place-items: center; overflow: hidden;">
                                <?php if ($kind === 'image'): ?>
                                    <img src="<?= $url ?>" alt="<?= $name ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php elseif ($kind === 'video'): ?>
                                    <video src="<?= $url ?>" controls preload="metadata" style="width: 100%; height: 100%; object-fit: cover; background: #000;"></video>
                                <?php elseif ($kind === 'pdf'): ?>
                                    <iframe src="<?= $url ?>#toolbar=0" style="width: 100%; height: 100%; border: 0;"></iframe>
                                <?php else: ?>
                                    <i class="fa-solid <?= e(preview_icon_for($kind)) ?>" style="font-size: 38px; color: var(--c-ink-mute);"></i>
                                <?php endif; ?>
                            </div>
                            <div style="padding: 10px 12px;">
                                <div style="font-family: var(--ff-mono); font-size: 10px; text-transform: uppercase; letter-spacing: .12em; color: var(--c-ink-mute); margin-bottom: 2px;"><?= e($field) ?></div>
                                <div style="font-size: 13px; font-weight: 600; word-break: break-word;"><?= $name ?></div>
                                <div style="margin-top: 8px; display: flex; gap: 8px;">
                                    <a class="btn small secondary" href="<?= $url ?>" target="_blank" rel="noopener"><i class="fa-solid fa-up-right-from-square"></i> Buka</a>
                                    <a class="btn small secondary" href="<?= $url ?>" download><i class="fa-solid fa-download"></i> Unduh</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (!empty($submission['uploaded_at'])): ?>
                    <p class="muted" style="margin: 16px 0 0; font-family: var(--ff-mono); font-size: 11px; text-transform: uppercase; letter-spacing: .12em;">
                        Terakhir diunggah: <?= e(date('d M Y H:i', strtotime((string) $submission['uploaded_at']))) ?> WIB
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </div>

<?php endif; ?>

<?php peserta_footer(); ?>
