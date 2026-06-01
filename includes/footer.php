<?php

declare(strict_types=1);

/**
 * Penutup layout publik bersama. Dipanggil di akhir halaman pages/
 * setelah public_header() dari includes/header.php.
 */
function public_footer(): void
{
?>
    </main>
    <footer class="footer">
        <div class="container footer-inner">
            <div class="footer-brand">
                <a class="brand" href="<?= e(APP_URL) ?>/pages/beranda.php" aria-label="Beranda PESOMA 2026">
                    <span class="brand-text">PESOMA 2026<small>Pekan Seni &amp; Olahraga Mahasiswa</small></span>
                </a>
                <p class="footer-desc">Platform resmi PESOMA 2026 untuk informasi lomba, jadwal kegiatan, publikasi pengumuman, dan pendaftaran peserta secara terintegrasi.</p>
            </div>
            <div>
                <h3 class="footer-title">Navigasi</h3>
                <div class="footer-links">
                    <a href="<?= e(APP_URL) ?>/pages/cabang-lomba.php">Cabang Lomba</a>
                    <a href="<?= e(APP_URL) ?>/pages/jadwal.php">Jadwal Kegiatan</a>
                    <a href="<?= e(APP_URL) ?>/pages/pengumuman.php">Pengumuman</a>
                    <a href="<?= e(APP_URL) ?>/pages/tentang.php">Tentang PESOMA</a>
                </div>
            </div>
            <div>
                <h3 class="footer-title">Informasi</h3>
                <div class="footer-meta">
                    <span>UIN Prof. K.H. Saifuddin Zuhri Purwokerto</span>
                    <span>Portal lomba mahasiswa terpusat</span>
                    <span>Pendaftaran, jadwal, dan pengumuman resmi</span>
                </div>
            </div>
        </div>
        <div class="container footer-bottom">
            <span>© 2026 PESOMA UIN Prof. K.H. Saifuddin Zuhri Purwokerto</span>
            <span>Didesain untuk pengalaman yang lebih modern dan informatif</span>
        </div>
    </footer>
</body>

</html>
<?php
}
