<?php

declare(strict_types=1);

/**
 * Penutup layout publik bersama. Dipanggil di akhir halaman pages/
 * setelah public_header() dari includes/header.php.
 * Styling: assets/css/pesoma-public.css.
 */
function public_footer(): void
{
?>
    </main>
    <footer class="public-footer">
        <div class="public-footer-grid">
            <div class="public-footer-brand">
                <a class="brand" href="<?= e(APP_URL) ?>/pages/beranda.php">
                    <div class="brand-text">
                        <span>PESOMA 2026</span>
                        <small>UIN SAIZU Purwokerto</small>
                    </div>
                </a>
                <p class="public-footer-desc">
                    Platform resmi PESOMA 2026 untuk informasi lomba, jadwal kegiatan, publikasi pengumuman, dan pendaftaran peserta secara terintegrasi.
                </p>
                <div class="public-footer-socials" aria-label="Media sosial">
                    <a href="mailto:pesoma@uinsaizu.ac.id" aria-label="Email"><i class="fa-regular fa-envelope"></i></a>
                    <a href="https://uinsaizu.ac.id" target="_blank" rel="noopener" aria-label="Website Kampus"><i class="fa-solid fa-globe"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                </div>
            </div>
            <div class="public-footer-col">
                <h3>Navigasi</h3>
                <a href="<?= e(APP_URL) ?>/pages/cabang-lomba.php">Cabang Lomba</a>
                <a href="<?= e(APP_URL) ?>/pages/jadwal.php">Jadwal Kegiatan</a>
                <a href="<?= e(APP_URL) ?>/pages/pengumuman.php">Pengumuman</a>
                <a href="<?= e(APP_URL) ?>/pages/tentang.php">Tentang PESOMA</a>
                <a href="<?= e(APP_URL) ?>/pages/unduh-juknis.php">Unduh Juknis</a>
            </div>
            <div class="public-footer-col">
                <h3>Kontak</h3>
                <span><i class="fa-regular fa-envelope"></i> pesoma@uinsaizu.ac.id</span>
                <span><i class="fa-solid fa-phone"></i> +62 812-0000-2026</span>
                <span><i class="fa-solid fa-location-dot"></i> Gedung Student Center, UIN SAIZU Purwokerto</span>
                <span><i class="fa-regular fa-clock"></i> Senin–Jumat · 08.00–16.00 WIB</span>
            </div>
        </div>
        <div class="public-footer-bottom">
            <span>© 2026 PESOMA UIN Prof. K.H. Saifuddin Zuhri Purwokerto. All Rights Reserved.</span>
            <span>UIN SAIZU Purwokerto</span>
        </div>
    </footer>
    </body>

    </html>
<?php
}
