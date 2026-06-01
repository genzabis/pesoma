-- =========================================================
-- Database Schema PESOMA 2026
-- Pekan Seni dan Olahraga Mahasiswa
-- UIN Prof. K.H. Saifuddin Zuhri Purwokerto
-- Stack: PHP Native + MySQL/MariaDB
-- =========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `pesoma_2026`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `pesoma_2026`;

-- =========================================================
-- DROP TABLES (urutan aman untuk foreign key)
-- =========================================================
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `login_attempts`;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `juri_assignments`;
DROP TABLE IF EXISTS `panitia_assignments`;
DROP TABLE IF EXISTS `winners`;
DROP TABLE IF EXISTS `finalists`;
DROP TABLE IF EXISTS `scores_final`;
DROP TABLE IF EXISTS `scores_penyisihan`;
DROP TABLE IF EXISTS `submissions`;
DROP TABLE IF EXISTS `mentors`;
DROP TABLE IF EXISTS `teams`;
DROP TABLE IF EXISTS `registrations`;
DROP TABLE IF EXISTS `announcements`;
DROP TABLE IF EXISTS `schedules`;
DROP TABLE IF EXISTS `aspek_penilaian`;
DROP TABLE IF EXISTS `competitions`;
DROP TABLE IF EXISTS `users`;


-- =========================================================
-- 1. USERS
-- =========================================================
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(150) NOT NULL,
  `nim` VARCHAR(20) NULL,
  `email` VARCHAR(150) NOT NULL,
  `fakultas` ENUM('FTIK','FAKDA','FASYA','FEBI','FUAH','FST') NULL,
  `role` ENUM('admin','panitia','juri','peserta') NOT NULL DEFAULT 'peserta',
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(30) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login_at` DATETIME NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  UNIQUE KEY `uq_users_nim` (`nim`),
  KEY `idx_users_role` (`role`),
  KEY `idx_users_fakultas` (`fakultas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 2. COMPETITIONS
-- =========================================================
CREATE TABLE `competitions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_lomba` VARCHAR(30) NOT NULL,
  `nama_lomba` VARCHAR(150) NOT NULL,
  `jenis` ENUM('individu','tim') NOT NULL DEFAULT 'individu',
  `kategori` VARCHAR(80) NULL,
  `deskripsi` TEXT NULL,
  `aturan` TEXT NULL,
  `juknis_file` VARCHAR(255) NULL,
  `aspek_penilaian` JSON NULL,
  `min_anggota` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `max_anggota` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `need_mentor` TINYINT(1) NOT NULL DEFAULT 0,
  `requires_mentor` TINYINT(1) NOT NULL DEFAULT 0,
  `has_penyisihan` TINYINT(1) NOT NULL DEFAULT 1,
  `has_final` TINYINT(1) NOT NULL DEFAULT 1,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,

  `is_upload_open` TINYINT(1) NOT NULL DEFAULT 1,
  `registration_deadline` DATETIME NULL DEFAULT '2026-04-27 23:59:59',
  `upload_deadline` DATETIME NULL DEFAULT '2026-05-18 23:59:59',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_competitions_kode` (`kode_lomba`),
  KEY `idx_competitions_jenis` (`jenis`),
  KEY `idx_competitions_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 2b. ASPEK PENILAIAN (per cabang lomba & babak)
-- =========================================================
CREATE TABLE `aspek_penilaian` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `competition_id` BIGINT UNSIGNED NOT NULL,
  `babak` ENUM('penyisihan','final') NOT NULL DEFAULT 'penyisihan',
  `aspek_name` VARCHAR(150) NOT NULL,
  `bobot_persen` DECIMAL(5,2) NOT NULL DEFAULT 0,
  `urutan` INT UNSIGNED NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_aspek_competition_babak` (`competition_id`, `babak`),
  CONSTRAINT `fk_aspek_competition` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 3. REGISTRATIONS
-- =========================================================

CREATE TABLE `registrations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nomor_peserta` VARCHAR(40) NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `competition_id` BIGINT UNSIGNED NOT NULL,
  `status_verifikasi` ENUM('pending','diterima','ditolak') NOT NULL DEFAULT 'pending',
  `catatan_verifikasi` TEXT NULL,
  `verified_by` BIGINT UNSIGNED NULL,
  `verified_at` DATETIME NULL,
  `tm_attendance` TINYINT(1) NOT NULL DEFAULT 0,
  `tm_checked_by` BIGINT UNSIGNED NULL,
  `tm_checked_at` DATETIME NULL,
  `final_attendance` TINYINT(1) NOT NULL DEFAULT 0,
  `final_checked_by` BIGINT UNSIGNED NULL,
  `final_checked_at` DATETIME NULL,
  `reminder_sent` DATE NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_registrations_nomor` (`nomor_peserta`),
  UNIQUE KEY `uq_registrations_user_competition` (`user_id`, `competition_id`),
  KEY `idx_registrations_competition` (`competition_id`),
  KEY `idx_registrations_status` (`status_verifikasi`),
  CONSTRAINT `fk_registrations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_registrations_competition` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_registrations_verified_by` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_registrations_tm_by` FOREIGN KEY (`tm_checked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_registrations_final_by` FOREIGN KEY (`final_checked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 4. TEAMS
-- =========================================================
CREATE TABLE `teams` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `registration_id` BIGINT UNSIGNED NOT NULL,
  `nama_anggota` VARCHAR(150) NOT NULL,
  `nim_anggota` VARCHAR(20) NOT NULL,
  `fakultas` ENUM('FTIK','FAKDA','FASYA','FEBI','FUAH','FST') NULL,
  `peran` VARCHAR(80) NOT NULL DEFAULT 'Anggota',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_teams_registration_nim` (`registration_id`, `nim_anggota`),
  KEY `idx_teams_registration` (`registration_id`),
  CONSTRAINT `fk_teams_registration` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 5. MENTORS
-- =========================================================
CREATE TABLE `mentors` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `registration_id` BIGINT UNSIGNED NOT NULL,
  `nama_dosen` VARCHAR(150) NOT NULL,
  `nidn` VARCHAR(40) NULL,
  `jabatan` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_mentors_registration` (`registration_id`),
  CONSTRAINT `fk_mentors_registration` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 6. SUBMISSIONS
-- =========================================================
CREATE TABLE `submissions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `registration_id` BIGINT UNSIGNED NOT NULL,
  `file_paths` JSON NOT NULL,
  `original_names` JSON NULL,
  `similarity_score` DECIMAL(5,2) NULL,
  `status` ENUM('draft','submitted','reviewed') NOT NULL DEFAULT 'submitted',
  `uploaded_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_submissions_registration` (`registration_id`),
  KEY `idx_submissions_status` (`status`),
  CONSTRAINT `fk_submissions_registration` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 7. SCORES PENYISIHAN
-- =========================================================
CREATE TABLE `scores_penyisihan` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `submission_id` BIGINT UNSIGNED NOT NULL,
  `juri_id` BIGINT UNSIGNED NOT NULL,
  `nilai_per_aspek` JSON NOT NULL,
  `total` DECIMAL(6,2) NOT NULL DEFAULT 0,
  `komentar` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_scores_penyisihan_submission_juri` (`submission_id`, `juri_id`),
  KEY `idx_scores_penyisihan_juri` (`juri_id`),
  CONSTRAINT `fk_scores_penyisihan_submission` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_scores_penyisihan_juri` FOREIGN KEY (`juri_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 8. SCORES FINAL
-- =========================================================
CREATE TABLE `scores_final` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `registration_id` BIGINT UNSIGNED NOT NULL,
  `juri_id` BIGINT UNSIGNED NOT NULL,
  `nilai_per_aspek` JSON NOT NULL,
  `total` DECIMAL(6,2) NOT NULL DEFAULT 0,
  `komentar` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_scores_final_registration_juri` (`registration_id`, `juri_id`),
  KEY `idx_scores_final_juri` (`juri_id`),
  CONSTRAINT `fk_scores_final_registration` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_scores_final_juri` FOREIGN KEY (`juri_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 9. FINALISTS
-- =========================================================
CREATE TABLE `finalists` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `registration_id` BIGINT UNSIGNED NOT NULL,
  `competition_id` BIGINT UNSIGNED NOT NULL,
  `rank_penyisihan` INT UNSIGNED NULL,
  `published_by` BIGINT UNSIGNED NULL,
  `announced_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notification_sent` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_finalists_registration` (`registration_id`),
  KEY `idx_finalists_competition` (`competition_id`),
  CONSTRAINT `fk_finalists_registration` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_finalists_competition` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_finalists_published_by` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 10. WINNERS
-- =========================================================
CREATE TABLE `winners` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `registration_id` BIGINT UNSIGNED NOT NULL,
  `competition_id` BIGINT UNSIGNED NOT NULL,
  `juara_ke` TINYINT UNSIGNED NOT NULL,
  `total_score` DECIMAL(6,2) NULL,
  `published_by` BIGINT UNSIGNED NULL,
  `announced_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notification_sent` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_winners_competition_rank` (`competition_id`, `juara_ke`),
  UNIQUE KEY `uq_winners_registration` (`registration_id`),
  CONSTRAINT `fk_winners_registration` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_winners_competition` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_winners_published_by` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 11. SCHEDULES
-- =========================================================
CREATE TABLE `schedules` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_name` VARCHAR(180) NOT NULL,
  `event_date` DATE NOT NULL,
  `event_time` TIME NULL,
  `location` VARCHAR(180) NULL,
  `link` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `is_public` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_schedules_date` (`event_date`),
  KEY `idx_schedules_public` (`is_public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 12. ANNOUNCEMENTS
-- =========================================================
CREATE TABLE `announcements` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(180) NOT NULL,
  `content` TEXT NOT NULL,
  `type` ENUM('finalis','winner','umum') NOT NULL DEFAULT 'umum',
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `published_by` BIGINT UNSIGNED NULL,
  `published_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_announcements_type` (`type`),
  KEY `idx_announcements_published` (`is_published`, `published_at`),
  CONSTRAINT `fk_announcements_published_by` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 13. JURI ASSIGNMENTS
-- =========================================================
CREATE TABLE `juri_assignments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `juri_id` BIGINT UNSIGNED NOT NULL,
  `competition_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_juri_assignments` (`juri_id`, `competition_id`),
  KEY `idx_juri_assignments_competition` (`competition_id`),
  CONSTRAINT `fk_juri_assignments_juri` FOREIGN KEY (`juri_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_juri_assignments_competition` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 14. ACTIVITY LOGS
-- =========================================================
CREATE TABLE `activity_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NULL,
  `role` VARCHAR(30) NULL,
  `action` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_activity_logs_user` (`user_id`),
  KEY `idx_activity_logs_action` (`action`),
  KEY `idx_activity_logs_created` (`created_at`),
  CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- SECURITY & OPERATIONAL SUPPORT TABLES
-- =========================================================
CREATE TABLE `login_attempts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(150) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `attempt_count` INT UNSIGNED NOT NULL DEFAULT 1,
  `last_attempt_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `blocked_until` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_login_attempts_email_ip` (`email`, `ip_address`),
  KEY `idx_login_attempts_blocked` (`blocked_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_resets` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(150) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_password_resets_token` (`token`),
  KEY `idx_password_resets_email` (`email`),
  KEY `idx_password_resets_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `panitia_assignments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `panitia_id` BIGINT UNSIGNED NOT NULL,
  `competition_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_panitia_assignments` (`panitia_id`, `competition_id`),
  CONSTRAINT `fk_panitia_assignments_panitia` FOREIGN KEY (`panitia_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_panitia_assignments_competition` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- SEED: 14 CABANG LOMBA (sesuai JUKNIS PESOMA 2026)

-- =========================================================
INSERT INTO `competitions`
(`kode_lomba`, `nama_lomba`, `jenis`, `kategori`, `deskripsi`, `aturan`, `aspek_penilaian`, `min_anggota`, `max_anggota`, `need_mentor`, `requires_mentor`, `has_penyisihan`, `has_final`)
VALUES
('KISTEK', 'Karya Inovasi Sains dan Teknologi', 'tim', 'Karya Inovasi', 'Karya tulis ilmiah semi formal inovasi sains dan teknologi berbasis SDGs dan Ekoteologi.', 'Tim tepat 3 orang: Penulis Naskah, Presenter, IT/Design/Product Support. Wajib 1 pendamping dosen/tendik (bukan panitia). Artikel 15-25 halaman, Times New Roman 12pt, spasi 1.5, margin 4-4-3-3, sitasi APA 7th (Mendeley/Zotero), plagiasi maksimal 25%. Upload DOCX, PPTX, dan prototipe produk.', JSON_ARRAY(JSON_OBJECT('nama','Kekuatan argumentasi','bobot',30), JSON_OBJECT('nama','Penyampaian dan kreativitas','bobot',25), JSON_OBJECT('nama','Penguasaan materi','bobot',25), JSON_OBJECT('nama','Tampilan PPT dan Prototipe Produk','bobot',20)), 3, 3, 1, 1, 1, 1),
('KISOS', 'Karya Inovasi Sosial Keagamaan', 'tim', 'Karya Inovasi', 'Karya tulis ilmiah semi formal inovasi sosial keagamaan berbasis SDGs dan Ekoteologi.', 'Tim tepat 3 orang: Penulis Naskah, Presenter, IT/Design/Product Support. Wajib 1 pendamping dosen/tendik (bukan panitia). Artikel 15-25 halaman, TNR 12pt, spasi 1.5, margin 4-4-3-3, sitasi APA 7th, plagiasi maksimal 25%. Upload DOCX, PPTX, dan prototipe.', JSON_ARRAY(JSON_OBJECT('nama','Kekuatan argumentasi','bobot',30), JSON_OBJECT('nama','Penyampaian dan kreativitas','bobot',25), JSON_OBJECT('nama','Penguasaan materi','bobot',25), JSON_OBJECT('nama','Tampilan PPT dan Prototipe Produk','bobot',20)), 3, 3, 1, 1, 1, 1),
('KIMEDIA', 'Karya Inovasi Media Pembelajaran', 'tim', 'Karya Inovasi', 'Karya inovasi media pembelajaran untuk jenjang SD/MI, SMP/MTs, SMA/MA/SMK/MAK berbasis SDGs dan Ekoteologi.', 'Tim tepat 3 orang: Penulis Naskah, Presenter, IT/Design/Product Support. Wajib 1 pendamping dosen/tendik (bukan panitia). Artikel 15-25 halaman, hasil inovasi berupa prototipe produk siap dipresentasikan. Upload DOCX, PPTX, dan prototipe.', JSON_ARRAY(JSON_OBJECT('nama','Kekuatan argumentasi','bobot',30), JSON_OBJECT('nama','Penyampaian dan kreativitas','bobot',25), JSON_OBJECT('nama','Penguasaan materi','bobot',25), JSON_OBJECT('nama','Tampilan PPT dan Prototipe Produk','bobot',20)), 3, 3, 1, 1, 1, 1),
('KIQURAN', 'Karya Inovasi Berbasis Al-Qur''an', 'tim', 'Karya Inovasi', 'Karya inovasi berbasis Al-Qur''an yang dapat diterapkan masyarakat luas, berbasis SDGs dan Ekoteologi.', 'Tim tepat 3 orang: Penulis Naskah, Presenter, IT/Design/Product Support. Wajib 1 pendamping dosen/tendik (bukan panitia). Artikel 15-25 halaman, hasil inovasi berupa prototipe produk. Upload DOCX, PPTX, dan prototipe.', JSON_ARRAY(JSON_OBJECT('nama','Kekuatan argumentasi','bobot',30), JSON_OBJECT('nama','Penyampaian dan kreativitas','bobot',25), JSON_OBJECT('nama','Penguasaan materi','bobot',25), JSON_OBJECT('nama','Tampilan PPT dan Prototipe Produk','bobot',20)), 3, 3, 1, 1, 1, 1),
('STORY', 'Story Telling', 'individu', 'Seni', 'Lomba bercerita fakta sejarah keislaman dalam Bahasa Inggris (kategori putra dan putri).', 'Individu. Bahasa Inggris penuh, durasi maksimal 10 menit, tidak boleh membawa teks. Cerita fakta sejarah keislaman tanpa unsur SARA/pornografi. Teks diserahkan ke juri saat lomba.', JSON_ARRAY(JSON_OBJECT('nama','Ketepatan judul cerita','bobot',15), JSON_OBJECT('nama','Alur cerita','bobot',25), JSON_OBJECT('nama','Pengucapan Bahasa Inggris','bobot',25), JSON_OBJECT('nama','Kesesuaian dengan teks','bobot',15), JSON_OBJECT('nama','Penguasaan panggung','bobot',20)), 1, 1, 0, 0, 0, 1),

('DAI', 'Da''i - Da''iyah', 'individu', 'Seni Islami', 'Lomba ceramah/dakwah kategori putra (Da''i) dan putri (Da''iyah).', 'Individu. Tema: Ekoteologi, Transformasi Digital, Penguatan Nilai Keluarga, atau Perdamaian Dunia. Durasi maksimal 10 menit. Teks ceramah diserahkan ke juri saat lomba.', JSON_ARRAY(JSON_OBJECT('nama','Kesesuaian judul dengan tema','bobot',25), JSON_OBJECT('nama','Pelafalan ayat dan hadits','bobot',25), JSON_OBJECT('nama','Kemampuan memahami judul dan tema','bobot',30), JSON_OBJECT('nama','Penguasaan panggung','bobot',20)), 1, 1, 0, 0, 0, 1),

('POSTER', 'Desain Poster', 'individu', 'Seni Visual', 'Kompetisi desain poster bertema PESOMA.', 'Peserta mengunggah JPG/PNG/PDF.', JSON_ARRAY(JSON_OBJECT('nama','Kesesuaian tema','bobot',30), JSON_OBJECT('nama','Kreativitas visual','bobot',30), JSON_OBJECT('nama','Pesan komunikasi','bobot',25), JSON_OBJECT('nama','Teknis desain','bobot',15)), 1, 0, 0, 0, 1, 1),
('FOTOGRAFI', 'Fotografi', 'individu', 'Seni Visual', 'Kompetisi fotografi mahasiswa.', 'Peserta mengunggah JPG/PNG dengan deskripsi karya.', JSON_ARRAY(JSON_OBJECT('nama','Kesesuaian tema','bobot',25), JSON_OBJECT('nama','Komposisi','bobot',30), JSON_OBJECT('nama','Teknik fotografi','bobot',25), JSON_OBJECT('nama','Cerita karya','bobot',20)), 1, 0, 0, 0, 1, 1),
('FILM', 'Film Pendek', 'tim', 'Seni Media', 'Kompetisi produksi film pendek.', 'Tim maksimal 3 mahasiswa. Upload video/link dan dokumen pendukung.', JSON_ARRAY(JSON_OBJECT('nama','Ide cerita','bobot',25), JSON_OBJECT('nama','Sinematografi','bobot',25), JSON_OBJECT('nama','Penyutradaraan','bobot',20), JSON_OBJECT('nama','Editing dan audio','bobot',20), JSON_OBJECT('nama','Pesan moral','bobot',10)), 3, 0, 0, 0, 1, 1),
('VOKAL', 'Vokal Solo', 'individu', 'Seni Musik', 'Kompetisi menyanyi solo.', 'Peserta individu mengunggah video audisi.', JSON_ARRAY(JSON_OBJECT('nama','Teknik vokal','bobot',35), JSON_OBJECT('nama','Interpretasi lagu','bobot',25), JSON_OBJECT('nama','Penghayatan','bobot',20), JSON_OBJECT('nama','Penampilan','bobot',20)), 1, 0, 0, 0, 1, 1),
('FUTSAL', 'Futsal', 'tim', 'Olahraga', 'Kompetisi olahraga futsal.', 'Tim mengikuti technical meeting dan aturan pertandingan.', JSON_ARRAY(JSON_OBJECT('nama','Kedisiplinan','bobot',20), JSON_OBJECT('nama','Sportivitas','bobot',30), JSON_OBJECT('nama','Performa pertandingan','bobot',50)), 3, 0, 0, 0, 0, 1),
('BADMINTON', 'Badminton', 'individu', 'Olahraga', 'Kompetisi bulu tangkis.', 'Peserta mengikuti drawing dan aturan pertandingan.', JSON_ARRAY(JSON_OBJECT('nama','Performa pertandingan','bobot',60), JSON_OBJECT('nama','Teknik permainan','bobot',25), JSON_OBJECT('nama','Sportivitas','bobot',15)), 1, 0, 0, 0, 0, 1),
('TENIS_MEJA', 'Tenis Meja', 'individu', 'Olahraga', 'Kompetisi tenis meja.', 'Peserta mengikuti drawing dan aturan pertandingan.', JSON_ARRAY(JSON_OBJECT('nama','Performa pertandingan','bobot',60), JSON_OBJECT('nama','Teknik permainan','bobot',25), JSON_OBJECT('nama','Sportivitas','bobot',15)), 1, 0, 0, 0, 0, 1),
('INOVASI', 'Inovasi Produk Mahasiswa', 'tim', 'Inovasi', 'Kompetisi inovasi/prototipe produk mahasiswa.', 'Tim maksimal 3 mahasiswa dan wajib 1 pendamping dosen/tendik.', JSON_ARRAY(JSON_OBJECT('nama','Kebaruan inovasi','bobot',30), JSON_OBJECT('nama','Manfaat produk','bobot',25), JSON_OBJECT('nama','Kelayakan prototipe','bobot',25), JSON_OBJECT('nama','Presentasi dan argumentasi','bobot',20)), 3, 1, 1, 1, 1, 1);

-- SEED: Jadwal utama PESOMA 2026
INSERT INTO `schedules` (`event_name`, `event_date`, `event_time`, `location`, `link`, `description`) VALUES
('Sosialisasi PESOMA 2026', '2026-03-01', '09:00:00', 'UIN SAIZU Purwokerto', NULL, 'Sosialisasi cabang lomba dan tata cara pendaftaran.'),
('Pembukaan Pendaftaran', '2026-03-15', '08:00:00', 'Online', 'https://bit.ly/PendaftaranPesertaPesoma2026', 'Pendaftaran peserta dibuka.'),
('Penutupan Pendaftaran', '2026-04-27', '23:59:00', 'Online', NULL, 'Pendaftaran ditutup otomatis.'),
('Technical Meeting', '2026-05-05', '09:00:00', 'UIN SAIZU Purwokerto', NULL, 'Technical meeting wajib peserta.'),
('Deadline Upload Karya', '2026-05-18', '23:59:00', 'Online', 'https://bit.ly/Formpengumpulankarya2026', 'Batas akhir pengumpulan karya.'),
('Pengumuman Finalis', '2026-05-26', '08:00:00', 'Website PESOMA 2026', NULL, 'Finalis diumumkan melalui website.'),
('Babak Final', '2026-06-10', '08:00:00', 'UIN SAIZU Purwokerto', NULL, 'Final dilaksanakan offline.'),
('Pengumuman Pemenang', '2026-06-30', '10:00:00', 'Website PESOMA 2026', NULL, 'Pemenang diumumkan melalui website.');

-- SEED: Admin default. Password: AdminPesoma2026!
INSERT INTO `users` (`nama`, `nim`, `email`, `fakultas`, `role`, `password`, `is_active`) VALUES
('Administrator PESOMA', NULL, 'admin@pesoma.local', NULL, 'admin', '$2y$10$hBsFmCK6n8EZLPsIW/EOju7WUVhLo0ODqkAb7UT/BQGnVvsX6s81q', 1);

-- SEED: Pengumuman awal
INSERT INTO `announcements` (`title`, `content`, `type`, `published_by`) VALUES
('Selamat Datang di Portal PESOMA 2026', 'Portal PESOMA 2026 digunakan untuk pendaftaran, upload karya, penjurian, dan pengumuman resmi.', 'umum', 1);
