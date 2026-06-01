<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/footer.php';

$totalLomba = (int) (db_fetch('SELECT COUNT(*) AS total FROM competitions WHERE is_active = 1')['total'] ?? 0);

public_header('Tentang', 'tentang.php');
?>
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-content">
            <span class="eyebrow">Pekan Seni &amp; Olahraga Mahasiswa</span>
            <h1>Tentang PESOMA 2026</h1>
            <p>Ajang tahunan mahasiswa UIN Prof. K.H. Saifuddin Zuhri Purwokerto dalam berbagai cabang lomba seni, olahraga, dan inovasi.</p>
            <div class="actions"><a class="btn" href="cabang-lomba.php">Lihat Cabang Lomba</a><a class="btn secondary" href="kontak.php">Hubungi Kami</a></div>
            <div class="hero-note"><span>✓ Seni dan olahraga</span><span>✓ Riset dan inovasi</span><span>✓ Portal terpadu</span></div>
        </div>
        <div class="hero-panel" aria-label="Highlight tentang PESOMA">
            <div class="hero-panel-card"><span class="hero-panel-label">Tujuan</span><strong>Wadah prestasi mahasiswa</strong><span>PESOMA mendorong kreativitas, sportivitas, kolaborasi, dan keunggulan mahasiswa.</span></div>
            <div class="hero-panel-card"><span class="hero-panel-label">Cakupan</span><strong><?= $totalLomba ?> cabang lomba aktif</strong><span>Kompetisi disusun untuk berbagai bidang minat dan potensi mahasiswa.</span></div>
        </div>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="page-highlight">
            <div class="stat"><strong><?= $totalLomba ?></strong><span>Cabang Aktif</span></div>
            <div class="stat"><strong>2026</strong><span>Tahun Pelaksanaan</span></div>
            <div class="stat"><strong>UIN</strong><span>SAIZU Purwokerto</span></div>
        </div>
        <div class="section-head">
            <div class="section-tag">Informasi</div>
            <h2 class="section-title">Mengenal PESOMA 2026</h2>
            <p class="section-desc">Pelajari lebih lanjut tentang visi, misi, dan cakupan kegiatan PESOMA 2026.</p>
        </div>
        <div class="grid">
            <article class="card">
                <div class="card-top"><span class="card-icon"><i class="fas fa-star"></i></span></div>
                <h3>Apa itu PESOMA?</h3>
                <p>PESOMA (Pekan Seni dan Olahraga Mahasiswa) 2026 adalah ajang tahunan yang mempertemukan mahasiswa UIN Prof. K.H. Saifuddin Zuhri Purwokerto dalam berbagai cabang lomba seni, olahraga, karya inovasi, serta keilmuan berbasis nilai keislaman.</p>
            </article>
            <article class="card">
                <div class="card-top"><span class="card-icon"><i class="fas fa-bullseye"></i></span></div>
                <h3>Tujuan</h3>
                <p>Menumbuhkan kreativitas, sportivitas, dan kolaborasi antarmahasiswa, sekaligus menjadi wadah penyaluran bakat dalam bidang seni, olahraga, dan inovasi yang berlandaskan SDGs dan ekoteologi.</p>
            </article>
            <article class="card">
                <div class="card-top"><span class="card-icon"><i class="fas fa-trophy"></i></span></div>
                <h3>Cakupan Lomba</h3>
                <p>Saat ini terdapat <strong><?= $totalLomba ?></strong> cabang lomba aktif yang dapat diikuti, mulai dari karya inovasi, seni islami, seni visual, hingga olahraga.</p>
                <a class="btn" href="cabang-lomba.php">Lihat Cabang Lomba</a>
            </article>
        </div>
        <article class="card" style="margin-top:18px">
            <h3>Penyelenggara</h3>
            <p>Kegiatan ini diselenggarakan oleh UIN Prof. K.H. Saifuddin Zuhri Purwokerto. Informasi resmi, pengumuman finalis, dan pemenang dipublikasikan melalui portal ini.</p>
            <a class="btn secondary" href="kontak.php">Hubungi Kami</a>
        </article>
    </div>
</section>
<?php public_footer(); ?>