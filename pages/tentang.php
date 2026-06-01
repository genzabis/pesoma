<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';

$totalLomba = (int) (db_fetch('SELECT COUNT(*) AS total FROM competitions WHERE is_active = 1')['total'] ?? 0);

public_header('Tentang', 'tentang.php');
?>

<!-- Hero -->
<section class="hero is-page">
    <div class="container">
        <div class="hero-inner">
            <div class="hero-content">
                <div class="hero-eyebrow"><span class="dot"></span>Pekan Seni & Inovasi Mahasiswa</div>
                <h1>Tentang<br>PESOMA III.</h1>
                <p class="hero-desc">
                    Ajang tahunan UIN Prof. K.H. Saifuddin Zuhri Purwokerto untuk mengembangkan potensi mahasiswa di bidang seni, ilmiah, dan inovasi. Tahun 2026 mengangkat tema "Inovasi dan Kreasi Mahasiswa: Sinergi Seni, Sains, dan Teknologi dalam Mewujudkan SDGs berbasis Ekoteologi".
                </p>
                <div class="hero-actions">
                    <a href="<?= e(APP_URL) ?>/pages/cabang-lomba.php" class="btn primary">Lihat Cabang Lomba</a>
                    <a href="<?= e(APP_URL) ?>/pages/kontak.php" class="btn secondary">Hubungi Kami</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="section" style="padding: 0;">
    <div class="container">
        <div class="stats-grid">
            <div class="stat">
                <span class="stat-value"><?= $totalLomba ?: 14 ?></span>
                <span class="stat-label">Cabang Aktif</span>
            </div>
            <div class="stat">
                <span class="stat-value">III</span>
                <span class="stat-label">Edisi 2026</span>
            </div>
            <div class="stat">
                <span class="stat-value">6</span>
                <span class="stat-label">Fakultas Peserta</span>
            </div>
            <div class="stat">
                <span class="stat-value">UIN</span>
                <span class="stat-label">SAIZU Purwokerto</span>
            </div>
        </div>
    </div>
</section>

<!-- Apa itu PESOMA (cream block) -->
<section class="section is-cream">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">Visi & Tujuan</span>
            <h2 class="section-title">Apa itu PESOMA?</h2>
            <p class="section-desc">Pekan Seni dan Inovasi Mahasiswa adalah ajang kompetisi tahunan yang diselenggarakan untuk mengembangkan potensi mahasiswa di bidang seni, ilmiah, dan inovasi. Tagline tahun ini: <em>"Unjuk Aksi, Raih Prestasi"</em>.</p>
        </div>
        <div class="grid-2" style="gap: 24px;">
            <div class="card">
                <div class="card-icon"><i class="fa-solid fa-bullseye"></i></div>
                <h3>Tujuan Pelaksanaan</h3>
                <ul>
                    <li>Mengembangkan kreativitas dan sportivitas mahasiswa.</li>
                    <li>Mewadahi bakat dan minat di bidang seni, ilmiah, dan inovasi.</li>
                    <li>Menjaring delegasi UIN SAIZU untuk ajang regional dan nasional.</li>
                    <li>Memperkuat silaturahim antar fakultas.</li>
                </ul>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fa-solid fa-layer-group"></i></div>
                <h3>Cakupan 14 Cabang</h3>
                <ul>
                    <li><strong>Karya Inovasi:</strong> Sains & Teknologi, Sosial Keagamaan, Media Pembelajaran, Berbasis Al-Qur'an.</li>
                    <li><strong>Seni Tutur:</strong> Story Telling, Da'i–Da'iyah, Puisi, Pop Solo Islami.</li>
                    <li><strong>Al-Qur'an & Kitab:</strong> Tilawah (MTQ), Tahfidz (MHQ), Qiro'atul Kutub, Kaligrafi.</li>
                    <li><strong>Visual:</strong> Film Pendek, Lomba Poster Beregu.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Fitur Portal -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">Platform Portal</span>
            <h2 class="section-title">Yang bisa dilakukan<br>di portal ini.</h2>
            <p class="section-desc">Satu portal terpadu untuk peserta, panitia, dan juri. Tanpa berkas tercecer, tanpa form Google Drive bertumpuk.</p>
        </div>
        <div class="grid-3">
            <div class="card">
                <div class="card-icon"><i class="fa-solid fa-user-plus"></i></div>
                <h3>Pendaftaran Online</h3>
                <p>Buat akun, daftarkan tim, dan unggah berkas KTM serta KRS langsung dari portal.</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                <h3>Upload Karya</h3>
                <p>Unggah artikel ilmiah, presentasi, video, dan lampiran prototipe sesuai jadwal pengumpulan.</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fa-solid fa-gavel"></i></div>
                <h3>Penjurian Transparan</h3>
                <p>Setiap aspek penilaian dan bobot penyisihan/final dapat dipantau peserta secara langsung.</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fa-solid fa-calendar-days"></i></div>
                <h3>Jadwal Terpusat</h3>
                <p>Sosialisasi, technical meeting, hingga yudisium pemenang dalam satu timeline resmi.</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fa-solid fa-bullhorn"></i></div>
                <h3>Pengumuman Resmi</h3>
                <p>Daftar peserta, finalis, dan juara dipublikasikan langsung tanpa lewat grup WhatsApp.</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fa-solid fa-chart-line"></i></div>
                <h3>Dashboard per Peran</h3>
                <p>Admin, panitia, juri, dan peserta punya dashboard sendiri sesuai tugas dan akses masing-masing.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2>Siap unjuk aksi<br>di PESOMA III?</h2>
        <p>Buat akun peserta, lengkapi data, dan pilih cabang lomba. Pendaftaran ditutup 27 April 2026.</p>
        <a href="<?= e(APP_URL) ?>/src/auth/register.php" class="btn primary">Daftar Sekarang</a>
    </div>
</section>

<?php public_footer(); ?>
