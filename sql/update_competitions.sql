-- =========================================================
-- UPDATE: 14 CABANG LOMBA SESUAI JUKNIS PESOMA 2026
-- Jalankan: mysql -u root pesoma_2026 < sql/update_competitions.sql
-- =========================================================

USE `pesoma_2026`;

-- Hapus data terkait terlebih dahulu (urutan aman untuk foreign key)
DELETE FROM `juri_assignments`;
DELETE FROM `panitia_assignments`;
DELETE FROM `winners`;
DELETE FROM `finalists`;
DELETE FROM `scores_final`;
DELETE FROM `scores_penyisihan`;
DELETE FROM `submissions`;
DELETE FROM `mentors`;
DELETE FROM `teams`;
DELETE FROM `registrations`;
DELETE FROM `competitions`;

-- INSERT 14 cabang lomba sesuai JUKNIS PESOMA 2026
INSERT INTO `competitions`
(`kode_lomba`, `nama_lomba`, `jenis`, `kategori`, `deskripsi`, `aturan`, `aspek_penilaian`, `min_anggota`, `max_anggota`, `need_mentor`, `requires_mentor`, `has_penyisihan`, `has_final`)
VALUES
-- 1. Karya Inovasi Sains dan Teknologi
('KISTEK', 'Karya Inovasi Sains dan Teknologi', 'tim', 'Karya Inovasi', 'Karya tulis ilmiah semi formal inovasi sains dan teknologi berbasis SDGs dan Ekoteologi.', 'Tim tepat 3 orang: Penulis Naskah, Presenter, IT/Design/Product Support. Wajib 1 pendamping dosen/tendik (bukan panitia). Artikel 15-25 halaman, Times New Roman 12pt, spasi 1.5, margin 4-4-3-3, sitasi APA 7th (Mendeley/Zotero), plagiasi maksimal 25%. Upload DOCX, PPTX, dan prototipe produk.', JSON_ARRAY(JSON_OBJECT('nama','Kekuatan argumentasi','bobot',30), JSON_OBJECT('nama','Penyampaian dan kreativitas','bobot',25), JSON_OBJECT('nama','Penguasaan materi','bobot',25), JSON_OBJECT('nama','Tampilan PPT dan Prototipe Produk','bobot',20)), 3, 3, 1, 1, 1, 1),

-- 2. Karya Inovasi Sosial Keagamaan
('KISOS', 'Karya Inovasi Sosial Keagamaan', 'tim', 'Karya Inovasi', 'Karya tulis ilmiah semi formal inovasi sosial keagamaan berbasis SDGs dan Ekoteologi.', 'Tim tepat 3 orang: Penulis Naskah, Presenter, IT/Design/Product Support. Wajib 1 pendamping dosen/tendik (bukan panitia). Artikel 15-25 halaman, TNR 12pt, spasi 1.5, margin 4-4-3-3, sitasi APA 7th, plagiasi maksimal 25%. Upload DOCX, PPTX, dan prototipe.', JSON_ARRAY(JSON_OBJECT('nama','Kekuatan argumentasi','bobot',30), JSON_OBJECT('nama','Penyampaian dan kreativitas','bobot',25), JSON_OBJECT('nama','Penguasaan materi','bobot',25), JSON_OBJECT('nama','Tampilan PPT dan Prototipe Produk','bobot',20)), 3, 3, 1, 1, 1, 1),

-- 3. Karya Inovasi Media Pembelajaran
('KIMEDIA', 'Karya Inovasi Media Pembelajaran', 'tim', 'Karya Inovasi', 'Karya inovasi media pembelajaran untuk jenjang SD/MI, SMP/MTs, SMA/MA/SMK/MAK berbasis SDGs dan Ekoteologi.', 'Tim tepat 3 orang: Penulis Naskah, Presenter, IT/Design/Product Support. Wajib 1 pendamping dosen/tendik (bukan panitia). Artikel 15-25 halaman, hasil inovasi berupa prototipe produk siap dipresentasikan. Upload DOCX, PPTX, dan prototipe.', JSON_ARRAY(JSON_OBJECT('nama','Kekuatan argumentasi','bobot',30), JSON_OBJECT('nama','Penyampaian dan kreativitas','bobot',25), JSON_OBJECT('nama','Penguasaan materi','bobot',25), JSON_OBJECT('nama','Tampilan PPT dan Prototipe Produk','bobot',20)), 3, 3, 1, 1, 1, 1),

-- 4. Karya Inovasi Berbasis Al-Qur'an
('KIQURAN', 'Karya Inovasi Berbasis Al-Qur''an', 'tim', 'Karya Inovasi', 'Karya inovasi berbasis Al-Qur''an yang dapat diterapkan masyarakat luas, berbasis SDGs dan Ekoteologi.', 'Tim tepat 3 orang: Penulis Naskah, Presenter, IT/Design/Product Support. Wajib 1 pendamping dosen/tendik (bukan panitia). Artikel 15-25 halaman, hasil inovasi berupa prototipe produk. Upload DOCX, PPTX, dan prototipe.', JSON_ARRAY(JSON_OBJECT('nama','Kekuatan argumentasi','bobot',30), JSON_OBJECT('nama','Penyampaian dan kreativitas','bobot',25), JSON_OBJECT('nama','Penguasaan materi','bobot',25), JSON_OBJECT('nama','Tampilan PPT dan Prototipe Produk','bobot',20)), 3, 3, 1, 1, 1, 1),

-- 5. Story Telling
('STORY', 'Story Telling', 'individu', 'Seni', 'Lomba bercerita fakta sejarah keislaman dalam Bahasa Inggris (kategori putra dan putri).', 'Individu. Bahasa Inggris penuh, durasi maksimal 10 menit, tidak boleh membawa teks. Cerita fakta sejarah keislaman tanpa unsur SARA/pornografi. Teks diserahkan ke juri saat lomba.', JSON_ARRAY(JSON_OBJECT('nama','Ketepatan judul cerita','bobot',15), JSON_OBJECT('nama','Alur cerita','bobot',25), JSON_OBJECT('nama','Pengucapan Bahasa Inggris','bobot',25), JSON_OBJECT('nama','Kesesuaian dengan teks','bobot',15), JSON_OBJECT('nama','Penguasaan panggung','bobot',20)), 1, 1, 0, 0, 0, 1),

-- 6. Da'i - Da'iyah
('DAI', 'Da''i - Da''iyah', 'individu', 'Seni Islami', 'Lomba ceramah/dakwah kategori putra (Da''i) dan putri (Da''iyah).', 'Individu. Tema: Ekoteologi, Transformasi Digital, Penguatan Nilai Keluarga, atau Perdamaian Dunia. Durasi maksimal 10 menit. Teks ceramah diserahkan ke juri saat lomba.', JSON_ARRAY(JSON_OBJECT('nama','Kesesuaian judul dengan tema','bobot',25), JSON_OBJECT('nama','Pelafalan ayat dan hadits','bobot',25), JSON_OBJECT('nama','Kemampuan memahami judul dan tema','bobot',30), JSON_OBJECT('nama','Penguasaan panggung','bobot',20)), 1, 1, 0, 0, 0, 1),

-- 7. Pop Solo Islami
('POPSOLO', 'Pop Solo Islami', 'individu', 'Seni Musik', 'Lomba menyanyi lagu pop islami (kategori putra dan putri).', 'Individu. Babak penyisihan online (video), babak final offline. Lagu wajib dan pilihan dari daftar yang ditentukan. Video original tanpa editing/mixing/pitch correction. Wajah penyanyi harus terlihat jelas sepanjang video.', JSON_ARRAY(JSON_OBJECT('nama','Teknik Vokal','bobot',35), JSON_OBJECT('nama','Interpretasi Lagu','bobot',30), JSON_OBJECT('nama','Penampilan','bobot',35)), 1, 1, 0, 0, 1, 1),

-- 8. Tilawah (MTQ)
('TILAWAH', 'Tilawah (MTQ)', 'individu', 'Seni Islami', 'Musabaqoh Tilawatil Qur''an (kategori putra dan putri).', 'Individu. Qiro''at Imam Ashim Riwayat Hafs Thariq As-Syatibiyyah dengan martabat mujawwad. Durasi maksimal 10 menit. Peserta memilih maqro dari daftar yang ditentukan. Peserta wajib membawakan minimal 4 lagu.', JSON_ARRAY(JSON_OBJECT('nama','Bidang Tajwid','bobot',35), JSON_OBJECT('nama','Bidang Fashohah dan Adab','bobot',35), JSON_OBJECT('nama','Bidang Lagu dan Suara','bobot',30)), 1, 1, 0, 0, 0, 1),

-- 9. Tahfidz (MHQ)
('TAHFIDZ', 'Tahfidz (MHQ)', 'individu', 'Seni Islami', 'Musabaqoh Hifdzil Qur''an 30 Juz (kategori putra dan putri).', 'Individu. Qiro''at Imam Ashim Riwayat Hafs Thariq As-Syatibiyyah dengan martabat murottal. Peserta menjawab 5 pertanyaan dari juri tentang hafalan dan pemahaman.', JSON_ARRAY(JSON_OBJECT('nama','Tahfidz','bobot',40), JSON_OBJECT('nama','Tajwid','bobot',30), JSON_OBJECT('nama','Fashohah dan Adab','bobot',30)), 1, 1, 0, 0, 0, 1),

-- 10. Puisi
('PUISI', 'Puisi', 'individu', 'Seni Sastra', 'Lomba pembacaan puisi (kategori putra dan putri).', 'Individu. Tema: Budaya Banyumasan atau Ekoteologi. Babak penyisihan online (video MP4), babak final offline. Durasi maksimal 10 menit. Naskah puisi orisinal belum dipublikasikan.', JSON_ARRAY(JSON_OBJECT('nama','Interpretasi dan Pemahaman Tema','bobot',30), JSON_OBJECT('nama','Penghayatan','bobot',25), JSON_OBJECT('nama','Vokal','bobot',25), JSON_OBJECT('nama','Penampilan','bobot',20)), 1, 1, 0, 0, 1, 1),

-- 11. Lomba Film Pendek
('FILM', 'Lomba Film Pendek', 'tim', 'Seni Media', 'Kompetisi produksi film pendek bertema Budaya Banyumasan atau Ekoteologi.', 'Tim 3-5 mahasiswa. Durasi 7-15 menit (tanpa credit title). Format MP4 HD (1280x720). Bahasa Indonesia dengan subtitle jika menggunakan bahasa daerah. Wajib logo UIN SAIZU dan PESOMA III. Babak penyisihan online, babak final screening offline.', JSON_ARRAY(JSON_OBJECT('nama','Kesesuaian dengan tema','bobot',20), JSON_OBJECT('nama','Cerita/Skenario','bobot',25), JSON_OBJECT('nama','Sinematografi','bobot',20), JSON_OBJECT('nama','Tata suara','bobot',15), JSON_OBJECT('nama','Pesan dan dampak','bobot',20)), 3, 5, 0, 0, 1, 1),

-- 12. Lomba Poster
('POSTER', 'Lomba Poster', 'tim', 'Seni Visual', 'Kompetisi desain poster digital bertema Budaya Banyumasan atau Ekoteologi.', 'Tim 3 mahasiswa. Ukuran A3 (297x420mm) portrait, resolusi 300dpi, format JPG/PDF max 10MB. Wajib logo UIN SAIZU dan PESOMA III. Poster Wajib (promosi kampus) dan Poster Pilihan. Babak penyisihan online, babak final presentasi offline.', JSON_ARRAY(JSON_OBJECT('nama','Kesesuaian dengan tema','bobot',20), JSON_OBJECT('nama','Estetika visual','bobot',25), JSON_OBJECT('nama','Orisinalitas','bobot',25), JSON_OBJECT('nama','Keterbacaan','bobot',15), JSON_OBJECT('nama','Kualitas teknis','bobot',15)), 3, 3, 0, 0, 1, 1),

-- 13. Lomba Kaligrafi
('KALIGRAFI', 'Lomba Kaligrafi', 'individu', 'Seni Islami', 'Kompetisi kaligrafi (kategori putra dan putri).', 'Individu. Jenis khat: Naskhi atau Riq''ah. Materi ayat Al-Qur''an atau hadis ditentukan panitia saat lomba. Alat dan media disediakan peserta sendiri. Durasi 90 menit. Peserta tidak boleh membawa referensi tertulis.', JSON_ARRAY(JSON_OBJECT('nama','Ketepatan kaidah','bobot',30), JSON_OBJECT('nama','Keindahan','bobot',30), JSON_OBJECT('nama','Komposisi','bobot',25), JSON_OBJECT('nama','Kerapian','bobot',15)), 1, 1, 0, 0, 0, 1),

-- 14. Qiro'atul Kutub
('QIROATUL', 'Qiro''atul Kutub', 'individu', 'Seni Islami', 'Lomba membaca kitab kuning (kategori putra dan putri).', 'Individu. Membaca kitab klasik Islam berbahasa Arab tanpa harakat (gundul) disertai terjemahan dan pemahaman. Babak penyisihan online (video), babak final offline. Kitab ditentukan panitia saat technical meeting.', JSON_ARRAY(JSON_OBJECT('nama','Kelancaran membaca','bobot',25), JSON_OBJECT('nama','Ketepatan harakat (i''rab)','bobot',25), JSON_OBJECT('nama','Ketepatan terjemahan','bobot',25), JSON_OBJECT('nama','Pemahaman isi','bobot',15), JSON_OBJECT('nama','Adab dan penampilan','bobot',10)), 1, 1, 0, 0, 1, 1);

-- =========================================================
-- Verifikasi: Harus ada 14 cabang lomba
-- =========================================================
SELECT COUNT(*) as total_cabang_lomba FROM `competitions`;
