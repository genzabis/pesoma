-- =========================================================
-- Seeding data contoh PESOMA 2026 (untuk pengembangan/testing)
-- PRASYARAT: jalankan sql/database.sql terlebih dahulu.
--   mysql -u root pesoma_2026 < sql/database.sql
--   mysql -u root pesoma_2026 < sql/seeding.sql
--
-- Idempoten: aman dijalankan berulang (INSERT IGNORE / ON DUPLICATE KEY).
-- Semua akun contoh memakai password: Pesoma2026
-- =========================================================

USE `pesoma_2026`;

SET @PASS = '$2y$10$/BnxHQgf5g.bZJcPLWDg4ejs5VzObv1QhPNc5vcGgyDtEfvALY1bm';

-- ---------------------------------------------------------
-- USERS contoh (email unik -> INSERT IGNORE aman diulang)
-- ---------------------------------------------------------
INSERT IGNORE INTO `users` (`nama`, `nim`, `email`, `fakultas`, `role`, `password`, `phone`, `is_active`) VALUES
('Panitia Satu',  NULL,        'panitia1@pesoma.local', NULL,    'panitia', @PASS, '081200000001', 1),
('Juri Seni',     NULL,        'juri.seni@pesoma.local', NULL,   'juri',    @PASS, '081200000002', 1),
('Juri Inovasi',  NULL,        'juri.inovasi@pesoma.local', NULL,'juri',    @PASS, '081200000003', 1),
('Ahmad Peserta', '2024010001','ahmad@student.local',   'FTIK',  'peserta', @PASS, '081200000010', 1),
('Bunga Peserta', '2024010002','bunga@student.local',   'FEBI',  'peserta', @PASS, '081200000011', 1),
('Citra Peserta', '2024010003','citra@student.local',   'Dakwah','peserta', @PASS, '081200000012', 1);

-- ---------------------------------------------------------
-- JURI ASSIGNMENTS: tugaskan juri ke beberapa cabang lomba
-- ---------------------------------------------------------
INSERT IGNORE INTO `juri_assignments` (`juri_id`, `competition_id`)
SELECT u.id, c.id FROM `users` u JOIN `competitions` c ON c.kode_lomba IN ('POSTER', 'FOTOGRAFI', 'VOKAL')
WHERE u.email = 'juri.seni@pesoma.local';

INSERT IGNORE INTO `juri_assignments` (`juri_id`, `competition_id`)
SELECT u.id, c.id FROM `users` u JOIN `competitions` c ON c.kode_lomba IN ('KISTEK', 'INOVASI')
WHERE u.email = 'juri.inovasi@pesoma.local';

-- ---------------------------------------------------------
-- REGISTRATIONS contoh (unik per user+competition & nomor_peserta)
--   Ahmad -> POSTER (individu, diterima)
--   Bunga -> FOTOGRAFI (individu, pending)
--   Citra -> KISTEK (tim, diterima)
-- ---------------------------------------------------------
INSERT IGNORE INTO `registrations` (`nomor_peserta`, `user_id`, `competition_id`, `status_verifikasi`, `verified_at`)
SELECT 'PESOMA-2026-POSTER-AHMAD', u.id, c.id, 'diterima', NOW()
FROM `users` u JOIN `competitions` c ON c.kode_lomba = 'POSTER' WHERE u.email = 'ahmad@student.local';

INSERT IGNORE INTO `registrations` (`nomor_peserta`, `user_id`, `competition_id`, `status_verifikasi`)
SELECT 'PESOMA-2026-FOTO-BUNGA', u.id, c.id, 'pending'
FROM `users` u JOIN `competitions` c ON c.kode_lomba = 'FOTOGRAFI' WHERE u.email = 'bunga@student.local';

INSERT IGNORE INTO `registrations` (`nomor_peserta`, `user_id`, `competition_id`, `status_verifikasi`, `verified_at`)
SELECT 'PESOMA-2026-KISTEK-CITRA', u.id, c.id, 'diterima', NOW()
FROM `users` u JOIN `competitions` c ON c.kode_lomba = 'KISTEK' WHERE u.email = 'citra@student.local';

-- ---------------------------------------------------------
-- TEAMS & MENTOR untuk registrasi tim KISTEK milik Citra
-- ---------------------------------------------------------
INSERT IGNORE INTO `teams` (`registration_id`, `nama_anggota`, `nim_anggota`, `fakultas`, `peran`)
SELECT r.id, 'Citra Peserta', '2024010003', 'Dakwah', 'Penulis Naskah'
FROM `registrations` r WHERE r.nomor_peserta = 'PESOMA-2026-KISTEK-CITRA';

INSERT IGNORE INTO `teams` (`registration_id`, `nama_anggota`, `nim_anggota`, `fakultas`, `peran`)
SELECT r.id, 'Dewi Anggota', '2024010004', 'FTIK', 'Presenter'
FROM `registrations` r WHERE r.nomor_peserta = 'PESOMA-2026-KISTEK-CITRA';

INSERT IGNORE INTO `mentors` (`registration_id`, `nama_dosen`, `nidn`, `jabatan`)
SELECT r.id, 'Dr. Pembimbing', '2012345678', 'Dosen Pembimbing'
FROM `registrations` r WHERE r.nomor_peserta = 'PESOMA-2026-KISTEK-CITRA';

-- ---------------------------------------------------------
-- SUBMISSIONS contoh (unik per registration)
-- ---------------------------------------------------------
INSERT IGNORE INTO `submissions` (`registration_id`, `file_paths`, `original_names`, `status`)
SELECT r.id,
       JSON_OBJECT('poster', 'public/uploads/poster/contoh-poster.jpg'),
       JSON_OBJECT('poster', 'poster-ahmad.jpg'),
       'submitted'
FROM `registrations` r WHERE r.nomor_peserta = 'PESOMA-2026-POSTER-AHMAD';

INSERT IGNORE INTO `submissions` (`registration_id`, `file_paths`, `original_names`, `status`)
SELECT r.id,
       JSON_OBJECT('artikel', 'public/uploads/artikel/contoh-artikel.docx', 'ppt', 'public/uploads/ppt/contoh.pptx'),
       JSON_OBJECT('artikel', 'artikel-citra.docx', 'ppt', 'slide-citra.pptx'),
       'submitted'
FROM `registrations` r WHERE r.nomor_peserta = 'PESOMA-2026-KISTEK-CITRA';

-- ---------------------------------------------------------
-- SCORES PENYISIHAN contoh (juri seni menilai poster Ahmad)
-- ---------------------------------------------------------
INSERT IGNORE INTO `scores_penyisihan` (`submission_id`, `juri_id`, `nilai_per_aspek`, `total`, `komentar`)
SELECT s.id, u.id,
       JSON_ARRAY(JSON_OBJECT('nama','Kesesuaian tema','bobot',30,'nilai',85),
                  JSON_OBJECT('nama','Kreativitas visual','bobot',30,'nilai',80),
                  JSON_OBJECT('nama','Pesan komunikasi','bobot',25,'nilai',82),
                  JSON_OBJECT('nama','Teknis desain','bobot',15,'nilai',78)),
       81.70, 'Karya solid, komposisi rapi.'
FROM `submissions` s
JOIN `registrations` r ON r.id = s.registration_id AND r.nomor_peserta = 'PESOMA-2026-POSTER-AHMAD'
JOIN `users` u ON u.email = 'juri.seni@pesoma.local';

-- =========================================================
-- Selesai. Login uji:
--   admin@pesoma.local        / AdminPesoma2026!   (dari database.sql)
--   panitia1@pesoma.local     / Pesoma2026
--   juri.seni@pesoma.local    / Pesoma2026
--   ahmad@student.local       / Pesoma2026
-- =========================================================
