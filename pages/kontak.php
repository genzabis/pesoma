<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/footer.php';

public_header('Kontak', 'kontak.php');
?>
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-content">
            <span class="eyebrow">Hubungi Kami</span>
            <h1>Kontak Panitia PESOMA 2026</h1>
            <p>Punya pertanyaan seputar pendaftaran atau pelaksanaan lomba? Hubungi panitia melalui berbagai saluran komunikasi.</p>
            <div class="actions"><a class="btn" href="mailto:pesoma@uinsaizu.ac.id">Kirim Email</a><a class="btn secondary" href="pengumuman.php">Lihat Pengumuman</a></div>
            <div class="hero-note"><span>✓ Informasi resmi</span><span>✓ Bantuan peserta</span><span>✓ Sekretariat kampus</span></div>
        </div>
        <div class="hero-panel" aria-label="Highlight kontak">
            <div class="hero-panel-card"><span class="hero-panel-label">Email</span><strong>pesoma@uinsaizu.ac.id</strong><span>Gunakan email resmi untuk pertanyaan administrasi dan teknis.</span></div>
            <div class="hero-panel-card"><span class="hero-panel-label">Sekretariat</span><strong>Gedung Kemahasiswaan</strong><span>Pelayanan panitia tersedia pada hari dan jam kerja.</span></div>
        </div>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="page-highlight">
            <div class="stat"><strong>Email</strong><span>Saluran Resmi</span></div>
            <div class="stat"><strong>08-16</strong><span>Jam Layanan</span></div>
            <div class="stat"><strong>UIN</strong><span>SAIZU Purwokerto</span></div>
        </div>
        <div class="section-head">
            <div class="section-tag">Informasi Kontak</div>
            <h2 class="section-title">Hubungi Panitia PESOMA</h2>
            <p class="section-desc">Kami siap membantu menjawab pertanyaan Anda tentang pendaftaran, teknis, atau informasi lainnya.</p>
        </div>
        <div class="grid">
            <article class="card">
                <div class="card-top"><span class="card-icon"><i class="fas fa-location-dot"></i></span></div>
                <h3>Alamat</h3>
                <p>UIN Prof. K.H. Saifuddin Zuhri Purwokerto<br>Jl. Jend. A. Yani No. 40A, Purwokerto, Jawa Tengah</p>
            </article>
            <article class="card">
                <div class="card-top"><span class="card-icon"><i class="fas fa-envelope"></i></span></div>
                <h3>Email</h3>
                <p><a href="mailto:pesoma@uinsaizu.ac.id">pesoma@uinsaizu.ac.id</a></p>
            </article>
            <article class="card">
                <div class="card-top"><span class="card-icon"><i class="fas fa-building"></i></span></div>
                <h3>Sekretariat</h3>
                <p>Gedung Kemahasiswaan UIN Prof. K.H. Saifuddin Zuhri.<br>Senin–Jumat, 08.00–16.00 WIB.</p>
            </article>
        </div>
        <article class="card" style="margin-top:18px">
            <h3>Informasi Resmi</h3>
            <p class="muted">Pengumuman finalis dan pemenang dipublikasikan melalui halaman pengumuman portal ini. Pastikan memantau secara berkala.</p>
            <a class="btn" href="pengumuman.php">Lihat Pengumuman</a>
        </article>
    </div>
</section>
<?php public_footer(); ?>