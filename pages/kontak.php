<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';

public_header('Kontak', 'kontak.php');
?>

<!-- Hero -->
<section class="hero is-page">
    <div class="container">
        <div class="hero-inner">
            <div class="hero-content">
                <div class="hero-eyebrow"><span class="dot"></span>Hubungi Panitia</div>
                <h1>Pertanyaan?<br>Kami siap bantu.</h1>
                <p class="hero-desc">
                    Pertanyaan seputar pendaftaran, ketentuan lomba, atau teknis upload karya — kirim pesan ke saluran resmi panitia di bawah ini.
                </p>
                <div class="hero-actions">
                    <a href="mailto:pesoma@uinsaizu.ac.id" class="btn primary">Kirim Email</a>
                    <a href="<?= e(APP_URL) ?>/pages/unduh-juknis.php" class="btn secondary">Baca Juknis Dulu</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="section" style="padding: 0;">
    <div class="container">
        <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="stat">
                <span class="stat-value">Email</span>
                <span class="stat-label">Saluran Resmi</span>
            </div>
            <div class="stat">
                <span class="stat-value">08–16</span>
                <span class="stat-label">WIB · Senin–Jumat</span>
            </div>
            <div class="stat">
                <span class="stat-value">UIN</span>
                <span class="stat-label">SAIZU Purwokerto</span>
            </div>
        </div>
    </div>
</section>

<!-- Saluran Kontak (cream) -->
<section class="section is-cream">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">Saluran Komunikasi</span>
            <h2 class="section-title">Cara menghubungi<br>panitia.</h2>
        </div>
        <div class="grid-2" style="gap: 24px;">
            <div class="card">
                <div class="card-icon"><i class="fa-regular fa-envelope"></i></div>
                <h3>Email Panitia</h3>
                <p>Untuk pertanyaan administrasi, teknis upload, dan informasi umum seputar PESOMA III.</p>
                <a href="mailto:pesoma@uinsaizu.ac.id" class="btn secondary small" style="width: fit-content; margin-top: auto;">pesoma@uinsaizu.ac.id</a>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fa-solid fa-building-columns"></i></div>
                <h3>Sekretariat</h3>
                <p>Gedung Student Center, UIN Prof. K.H. Saifuddin Zuhri Purwokerto. Layanan tatap muka Senin–Jumat pukul 08.00–16.00 WIB.</p>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">Pertanyaan Umum</span>
            <h2 class="section-title">FAQ.</h2>
            <p class="section-desc">Pertanyaan yang sering diajukan calon peserta. Sebelum mengirim email, cek dulu di sini.</p>
        </div>
        <div class="grid-2" style="gap: 24px;">
            <div class="card">
                <h3>Bagaimana cara mendaftar?</h3>
                <p>Buat akun di portal, lengkapi data diri (NIM, fakultas, email kampus), pilih cabang lomba, lalu unggah berkas KTM dan KRS aktif.</p>
            </div>
            <div class="card">
                <h3>Apakah berbayar?</h3>
                <p>Tidak. PESOMA III gratis untuk seluruh mahasiswa aktif Program Sarjana UIN Prof. K.H. Saifuddin Zuhri Purwokerto.</p>
            </div>
            <div class="card">
                <h3>Bagaimana cara upload karya?</h3>
                <p>Setelah pendaftaran disetujui panitia, menu Upload Karya akan aktif di dashboard peserta dengan format file dan deadline yang sudah ditentukan.</p>
            </div>
            <div class="card">
                <h3>Kapan pengumuman finalis?</h3>
                <p>Pengumuman finalis dijadwalkan 26 Mei 2026, dan pengumuman pemenang resmi pada 30 Juni 2026 di Auditorium kampus.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2>Masih ada<br>yang ingin ditanyakan?</h2>
        <p>Hubungi panitia melalui email resmi. Kami berusaha membalas dalam waktu 1×24 jam pada hari kerja.</p>
        <a href="mailto:pesoma@uinsaizu.ac.id" class="btn primary">Kirim Email</a>
    </div>
</section>

<?php public_footer(); ?>
