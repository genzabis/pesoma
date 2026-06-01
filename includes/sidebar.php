<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

/**
 * Sidebar dashboard bersama berbasis peran.
 * Panggil render_sidebar(ROLE_*, 'slug-aktif.php') di dalam panel dashboard.
 */
function render_sidebar(string $role, string $active = ''): void
{
    $items = match ($role) {
        ROLE_ADMIN => [
            'dashboard.php' => 'Dashboard',
            'kelola-user.php' => 'Kelola User',
            'kelola-cabang-lomba.php' => 'Kelola Cabang Lomba',
            'kelola-aspek.php' => 'Aspek Penilaian',
            'kelola-jadwal.php' => 'Kelola Jadwal',
            'log-aktivitas.php' => 'Log Aktivitas',
            'pengaturan.php' => 'Pengaturan',
            '../auth/logout.php' => 'Logout',
        ],
        ROLE_PANITIA => [
            'dashboard.php' => 'Dashboard',
            'verifikasi-peserta.php' => 'Verifikasi Peserta',
            'daftar-karya.php' => 'Daftar Karya',
            'tentukan-finalis.php' => 'Tentukan Finalis',
            'input-pemenang.php' => 'Input Pemenang',
            'kelola-jadwal.php' => 'Kelola Jadwal',
            'buat-pengumuman.php' => 'Buat Pengumuman',
            'laporan.php' => 'Laporan',
            '../auth/logout.php' => 'Logout',
        ],
        ROLE_JURI => [
            'dashboard.php' => 'Dashboard',
            'penilaian-penyisihan.php' => 'Penilaian Penyisihan',
            'penilaian-final.php' => 'Penilaian Final',
            'riwayat-penilaian.php' => 'Riwayat Penilaian',
            '../auth/logout.php' => 'Logout',
        ],
        default => [
            'dashboard.php' => 'Dashboard',
            'daftar-lomba.php' => 'Daftar Lomba',
            'upload-karya.php' => 'Upload Karya',
            'status-pendaftaran.php' => 'Status Pendaftaran',
            'tim-saya.php' => 'Tim Saya',
            'pengumuman-saya.php' => 'Pengumuman Saya',
            '../auth/logout.php' => 'Logout',
        ],
    };
?>
    <aside class="sidebar">
        <div class="brand">PESOMA 2026</div>
        <div class="role">Panel <?= e(label_role($role)) ?></div>
        <nav class="nav">
            <?php foreach ($items as $href => $label): ?>
                <a class="<?= basename($href) === $active ? 'active' : '' ?>" href="<?= e($href) ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
        </nav>
    </aside>
<?php
}
