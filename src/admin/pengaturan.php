<?php

declare(strict_types=1);

require_once __DIR__ . '/_layout.php';

$defaults = [
    'site_title' => APP_NAME,
    'event_theme' => 'Pekan Seni dan Olahraga Mahasiswa 2026',
    'registration_open' => '1',
    'upload_open' => '1',
    'tm_checkin_open' => '0',
    'similarity_limit' => '25',
    'max_upload_mb' => (string)(UPLOAD_MAX_SIZE / 1024 / 1024),
    'contact_admin' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        flash('error', 'Token tidak valid.');
        admin_redirect('pengaturan.php');
    }
    foreach ($defaults as $key => $default) {
        admin_save_setting($key, trim((string)($_POST[$key] ?? '0')));
    }
    log_activity(admin_id(), ROLE_ADMIN, 'update_settings', 'Memperbarui pengaturan aplikasi');
    flash('success', 'Pengaturan berhasil disimpan.');
    admin_redirect('pengaturan.php');
}

$settings = [];
foreach ($defaults as $key => $value) $settings[$key] = admin_setting($key, $value);
admin_header('Pengaturan', 'pengaturan.php');
?>
<section class="card">
    <h2>Pengaturan Aplikasi</h2>
    <form class="two" method="POST"><?= csrf_field() ?>
        <div class="field"><label>Judul Situs</label><input name="site_title" value="<?= e($settings['site_title']) ?>"></div>
        <div class="field"><label>Tema Event</label><input name="event_theme" value="<?= e($settings['event_theme']) ?>"></div>
        <div class="field"><label>Pendaftaran Dibuka</label><select name="registration_open">
                <option value="1" <?= $settings['registration_open'] === '1' ? 'selected' : '' ?>>Ya</option>
                <option value="0" <?= $settings['registration_open'] === '0' ? 'selected' : '' ?>>Tidak</option>
            </select></div>
        <div class="field"><label>Upload Karya Dibuka</label><select name="upload_open">
                <option value="1" <?= $settings['upload_open'] === '1' ? 'selected' : '' ?>>Ya</option>
                <option value="0" <?= $settings['upload_open'] === '0' ? 'selected' : '' ?>>Tidak</option>
            </select></div>
        <div class="field"><label>Check-in Technical Meeting</label><select name="tm_checkin_open">
                <option value="1" <?= $settings['tm_checkin_open'] === '1' ? 'selected' : '' ?>>Dibuka</option>
                <option value="0" <?= $settings['tm_checkin_open'] === '0' ? 'selected' : '' ?>>Ditutup</option>
            </select></div>
        <div class="field"><label>Batas Similarity (%)</label><input type="number" min="0" max="100" name="similarity_limit" value="<?= e($settings['similarity_limit']) ?>"></div>
        <div class="field"><label>Maks Upload (MB)</label><input type="number" min="1" name="max_upload_mb" value="<?= e($settings['max_upload_mb']) ?>"></div>
        <div class="field"><label>Kontak Admin</label><input name="contact_admin" value="<?= e($settings['contact_admin']) ?>" placeholder="email/WhatsApp"></div>
        <div class="field"><label>&nbsp;</label><button class="btn">Simpan Pengaturan</button></div>
    </form>
</section>
<?php admin_footer(); ?>