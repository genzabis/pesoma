# 📋 Portal PESOMA 2026

Portal resmi **PESOMA 2026** (Pekan Seni dan Olahraga Mahasiswa) — UIN Prof. K.H. Saifuddin Zuhri Purwokerto. 

Aplikasi web komprehensif untuk manajemen kompetisi seni dan olahraga mahasiswa, mencakup pendaftaran lomba, upload karya, verifikasi peserta, penjurian berbasis aspek berbobot, serta pengumuman finalis dan pemenang.

**Stack:** PHP 8.2 native + MySQL/MariaDB (PDO), tanpa framework eksternal. Dirancang untuk dijalankan di XAMPP atau server Linux dengan Apache + PHP 8.2+.

---

## 📌 Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Fitur per Peran](#fitur-per-peran)
- [Struktur Folder](#struktur-folder)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Setup & Instalasi](#setup--instalasi)
- [Akun Default](#akun-default)
- [Konfigurasi](#konfigurasi)
- [Database Schema](#database-schema)
- [Keamanan](#keamanan)
- [API Endpoints](#api-endpoints)
- [Cron & Tugas Terjadwal](#cron--tugas-terjadwal)
- [Troubleshooting](#troubleshooting)
- [Kontribusi & Lisensi](#kontribusi--lisensi)

---

## ✨ Fitur Utama

- **Manajemen Kompetisi** — Kelola cabang lomba, kategori, aspek penilaian, dan jadwal
- **Registrasi Peserta** — Daftar individu atau tim dengan validasi NIM & email
- **Upload Karya** — Peserta upload karya dengan tracking progress real-time
- **Penjurian Bertingkat** — Penyisihan dan final dengan scoring berbasis aspek berbobot
- **Verifikasi Peserta** — Panitia verifikasi dokumen dan kelengkapan peserta
- **Pengumuman Dinamis** — Publikasi finalis, pemenang, dan berita terkait
- **Laporan & Statistik** — Dashboard dengan grafik peserta, tim, dan hasil penilaian
- **Backup Otomatis** — Cron job untuk backup database harian
- **Audit Log** — Pencatatan aktivitas user untuk keamanan dan compliance
- **Responsive Design** — Kompatibel desktop, tablet, dan mobile


## 👥 Fitur per Peran

### 🎓 Peserta
- Daftar lomba (individu atau tim)
- Kelola anggota tim dan pendamping
- Upload karya dengan tracking progress
- Lihat status verifikasi dan penilaian
- Akses pengumuman finalis dan pemenang
- Download juknis dan materi lomba

### ⚖️ Juri
- Penilaian babak penyisihan dan final
- Scoring berbasis aspek berbobot
- Riwayat penilaian dan perubahan skor
- Export hasil penilaian

### 📋 Panitia
- Verifikasi dokumen peserta
- Kelola jadwal kompetisi
- Tentukan finalis berdasarkan skor
- Input pemenang per kategori
- Buat dan publikasikan pengumuman
- Laporan peserta, tim, dan statistik

### 🔐 Admin
- Kelola user (peserta, juri, panitia, admin)
- Kelola cabang lomba dan kategori
- Atur aspek penilaian per cabang
- Kelola jadwal dan deadline
- Pengaturan sistem dan email
- Audit log aktivitas user
- Backup dan restore database

### 🌐 Publik (Tanpa Login)
- Beranda dengan informasi umum
- Daftar dan detail cabang lomba
- Jadwal kompetisi
- Pengumuman finalis dan pemenang
- Download juknis (PDF)
- Halaman kontak dan tentang

## Struktur folder

```
pesoma/
├── api/          # Endpoint JSON (cek-email, cek-nim, get-jadwal, statistik, dll.)
├── assets/       # CSS & JS bersama (css/, js/) + gambar (images/)
├── config/       # config.php, constants.php, database.php (koneksi PDO + helper)
├── docs/         # Dokumentasi (cron, dll.)
├── includes/     # auth.php, session.php, functions.php, header/footer/navbar/sidebar, upload-handler.php
├── pages/        # Halaman publik (tanpa login)
├── public/       # uploads/ (berkas karya peserta)
├── scripts/      # Tugas terjadwal (cron): tutup pendaftaran/upload, notifikasi, backup, dll.
├── sql/          # database.sql (skema + seed inti) & seeding.sql (data contoh)
├── src/          # Modul berperan: admin/, panitia/, juri/, peserta/, auth/
└── index.php     # Redirect ke pages/beranda.php
```

## Setup

1. **Letakkan project** di `htdocs` XAMPP (mis. `D:\xampp\htdocs\pesoma`).
2. **Buat & isi database** (jalankan dari root project):
   ```bash
   mysql -u root pesoma_2026 < sql/database.sql
   mysql -u root pesoma_2026 < sql/seeding.sql   # opsional: data contoh
   ```
   `database.sql` sudah membuat database `pesoma_2026` beserta tabel, seed 14 cabang lomba, jadwal, dan akun admin.
3. **Konfigurasi** (opsional, default cocok untuk XAMPP lokal). Override via environment variable bila perlu:
   `APP_ENV`, `APP_URL`, `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`. Lihat `config/config.php`.
4. **Jalankan**: start Apache + MySQL di XAMPP, buka `http://localhost/pesoma/`.

## Akun default

| Peran   | Email                     | Password          |
|---------|---------------------------|-------------------|
| Admin   | `admin@pesoma.local`      | `admin`|
| Panitia | `panitia1@pesoma.local`   | `Pesoma2026`      |
| Juri    | `juri.seni@pesoma.local`  | `Pesoma2026`      |
| Peserta | `ahmad@student.local`     | `Pesoma2026`      |

> Akun selain admin hanya tersedia bila `sql/seeding.sql` diimpor. **Ganti semua password default sebelum production.**

## Keamanan

- Query memakai prepared statement (PDO, `db_query`/`db_fetch`/`db_fetch_all` di `config/database.php`).
- Proteksi CSRF via `csrf_field()` / `verify_csrf()` (`includes/session.php`).
- Output di-escape dengan `e()` (`includes/auth.php`).
- Session aman: `httponly`, `samesite=Lax`, regenerasi id saat login, timeout 30 menit.

## Cron / tugas terjadwal

Skrip di `scripts/` (mis. `close_registration.php`, `reminder_upload.php`, `notify_finalists.php`, `backup_database.php`) dijalankan via cron/Task Scheduler. Lihat `docs/cron.md`, `scripts/crontab.example`, dan `scripts/windows-task-scheduler.example.bat`.

## Catatan aset gambar

Berkas berikut masih **placeholder kosong** dan perlu diisi manual (berkas biner):
`assets/images/logo/logo-pesoma.png`, `logo-uin.png`, `favicon.ico`, dan `assets/images/banners/hero-bg.jpg`.

---

## 💻 Persyaratan Sistem

### Minimum
- **PHP** 8.2 atau lebih tinggi
- **MySQL** 5.7+ atau **MariaDB** 10.3+
- **Apache** 2.4+ dengan mod_rewrite aktif
- **Disk Space** minimal 500MB (untuk uploads)

### Rekomendasi Production
- **PHP** 8.2+ dengan extensions: `pdo_mysql`, `json`, `mbstring`, `curl`, `gd`
- **MySQL** 8.0+ atau **MariaDB** 10.5+
- **Apache** 2.4+ atau **Nginx** 1.18+
- **SSL/TLS** certificate (HTTPS)
- **Disk Space** 2GB+ (untuk uploads dan backup)
- **RAM** minimal 2GB

### Development (XAMPP)
- XAMPP 8.2+ (PHP 8.2, MySQL 8.0, Apache 2.4)
- Windows 10/11, macOS, atau Linux

---

## ⚙️ Konfigurasi

### File Konfigurasi Utama

**`config/config.php`** — Konfigurasi aplikasi:
```php
define('APP_NAME', 'Portal PESOMA 2026');
define('APP_ENV', 'development');  // 'development' atau 'production'
define('APP_URL', 'http://localhost/pesoma');
define('APP_TIMEZONE', 'Asia/Jakarta');
define('SESSION_TIMEOUT', 1800);  // 30 menit
define('UPLOAD_MAX_SIZE', 100 * 1024 * 1024);  // 100MB
```

**`config/database.php`** — Koneksi database PDO:
```php
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'pesoma_2026');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Environment Variables (Production)

Override konfigurasi via environment variable:
```bash
export APP_ENV=production
export APP_URL=https://pesoma.uin-purwokerto.ac.id
export DB_HOST=db.example.com
export DB_NAME=pesoma_prod
export DB_USER=pesoma_user
export DB_PASS=secure_password_here
```

### Upload Limits

Sesuaikan di `php.ini`:
```ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
memory_limit = 256M
```

---

## 🗄️ Database Schema

### Tabel Utama

| Tabel | Deskripsi |
|-------|-----------|
| `users` | User (peserta, juri, panitia, admin) |
| `competitions` | Cabang lomba |
| `aspek_penilaian` | Aspek scoring per cabang & babak |
| `registrations` | Pendaftaran peserta |
| `teams` | Tim peserta |
| `mentors` | Pendamping tim |
| `submissions` | Upload karya peserta |
| `scores_penyisihan` | Skor babak penyisihan |
| `scores_final` | Skor babak final |
| `finalists` | Daftar finalis |
| `winners` | Daftar pemenang |
| `announcements` | Pengumuman |
| `schedules` | Jadwal kompetisi |
| `juri_assignments` | Penugasan juri |
| `panitia_assignments` | Penugasan panitia |
| `activity_logs` | Log aktivitas user |
| `login_attempts` | Tracking login gagal |
| `password_resets` | Token reset password |

### Relasi Utama

```
users (1) ──→ (M) registrations
users (1) ──→ (M) teams
users (1) ──→ (M) scores_penyisihan
users (1) ──→ (M) scores_final
competitions (1) ──→ (M) registrations
competitions (1) ──→ (M) aspek_penilaian
registrations (1) ──→ (M) submissions
registrations (1) ──→ (M) finalists
registrations (1) ──→ (M) winners
teams (1) ──→ (M) mentors
```

---

## 🔒 Keamanan

### Proteksi Query
- Semua query menggunakan **prepared statement** (PDO)
- Helper: `db_query()`, `db_fetch()`, `db_fetch_all()` di `config/database.php`
- Tidak ada string interpolation dalam SQL

### Proteksi CSRF
- Token CSRF di setiap form via `csrf_field()`
- Verifikasi token via `verify_csrf()` di `includes/session.php`
- Token di-regenerasi setiap request

### Output Escaping
- Semua output di-escape dengan `e()` function
- Mencegah XSS injection
- Contoh: `<?= e($user['nama']) ?>`

### Session Security
- Cookie flag: `httponly=true`, `samesite=Lax`
- Session timeout: 30 menit (configurable)
- Session ID di-regenerate saat login
- Secure flag untuk HTTPS (production)

### Password Security
- Hash: `password_hash()` dengan algo `PASSWORD_BCRYPT`
- Verifikasi: `password_verify()`
- Min 8 karakter, kombinasi huruf/angka/simbol (recommended)

### Rate Limiting
- Login attempts: max 5 kali, lock 15 menit
- Tracking di tabel `login_attempts`

### File Upload Security
- Validasi MIME type
- Rename file dengan hash
- Simpan di folder `public/uploads/` (outside webroot recommended)
- Proteksi `.htaccess` di `storage/`

---

## 🔌 API Endpoints

### Public Endpoints (Tanpa Auth)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/get-jadwal.php` | Daftar jadwal kompetisi |
| GET | `/api/get-pengumuman.php` | Daftar pengumuman |
| GET | `/api/get-finalis.php` | Daftar finalis |
| GET | `/api/get-pemenang.php` | Daftar pemenang |
| POST | `/api/cek-email.php` | Cek email sudah terdaftar |
| POST | `/api/cek-nim.php` | Cek NIM sudah terdaftar |

### Protected Endpoints (Require Auth)

| Method | Endpoint | Deskripsi | Role |
|--------|----------|-----------|------|
| GET | `/api/get-statistik.php` | Statistik peserta & tim | admin, panitia |
| POST | `/api/upload-progress.php` | Track progress upload | peserta |

### Response Format

**Success (200)**:
```json
{
  "success": true,
  "data": { ... },
  "message": "OK"
}
```

**Error (400/500)**:
```json
{
  "success": false,
  "error": "Error message",
  "code": "ERROR_CODE"
}
```

---

## ⏰ Cron & Tugas Terjadwal

### Script Cron Tersedia

| Script | Fungsi | Jadwal Rekomendasi |
|--------|--------|-------------------|
| `backup_database.php` | Backup database | Harian, 01:00 |
| `close_registration.php` | Tutup pendaftaran otomatis | 15 menit sekali (saat deadline) |
| `close_upload.php` | Tutup upload otomatis | 15 menit sekali (saat deadline) |
| `reminder_upload.php` | Reminder upload ke peserta | Harian, 08:00 |
| `cleanup_tokens.php` | Bersihkan token expired | Tiap jam |
| `notify_finalists.php` | Notifikasi finalis | 15 menit sekali (hari pengumuman) |
| `notify_winners.php` | Notifikasi pemenang | 15 menit sekali (hari pengumuman) |
| `cleanup_temp_files.php` | Bersihkan file temp | Harian, 02:00 |
| `generate_daily_report.php` | Generate laporan harian | Harian, 23:55 |

### Setup Cron

**Linux (crontab)**:
```bash
crontab -e
# Tambahkan:
0 1 * * * php /var/www/pesoma/scripts/backup_database.php
0 8 * * * php /var/www/pesoma/scripts/reminder_upload.php
```

**Windows (Task Scheduler)**:
1. Buka Task Scheduler
2. Create Task → Actions → Program: `d:\xampp\php\php.exe`
3. Arguments: `d:\xampp\htdocs\pesoma\scripts\backup_database.php`
4. Atur trigger sesuai jadwal

Lihat `docs/cron.md` untuk detail lengkap.

---

## 🐛 Troubleshooting

### Database Connection Error
**Error**: "Koneksi database gagal"
- Pastikan MySQL/MariaDB running
- Cek konfigurasi di `config/config.php`
- Verifikasi username & password database
- Cek port (default 3306)

### Upload File Gagal
**Error**: "File terlalu besar" atau "Upload gagal"
- Cek `php.ini`: `upload_max_filesize`, `post_max_size`
- Verifikasi folder `public/uploads/` writable
- Cek disk space tersedia
- Lihat log di `storage/logs/php-error.log`

### Session Timeout
**Masalah**: Logout otomatis setelah 30 menit
- Ubah `SESSION_TIMEOUT` di `config/config.php`
- Pastikan server time sync dengan client
- Cek cookie settings di browser

### CSRF Token Error
**Error**: "Token tidak valid"
- Refresh halaman dan coba lagi
- Clear browser cache & cookies
- Pastikan session aktif
- Cek `includes/session.php` untuk debug

### Email Notifikasi Tidak Terkirim
**Masalah**: Cron email tidak terkirim
- Pastikan mail server/SMTP configured
- Cek `CRON_MAIL_DISABLED` environment variable
- Lihat log di `storage/logs/cron.log`
- Untuk production, gunakan PHPMailer atau library SMTP

### Permission Denied
**Error**: "Permission denied" pada folder
- Linux: `chmod 755 public/uploads/` dan `chmod 755 storage/logs/`
- Windows: Pastikan user Apache punya write access
- Cek ownership folder

---

## 📚 Dokumentasi Tambahan

- **Cron Setup**: Lihat `docs/cron.md`
- **Database Schema**: Lihat `sql/database.sql`
- **Seeding Data**: Lihat `sql/seeding.sql`
- **Crontab Example**: Lihat `scripts/crontab.example`
- **Windows Task Scheduler**: Lihat `scripts/windows-task-scheduler.example.bat`

---

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan:
1. Fork repository
2. Buat branch feature (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah **MIT License**. Lihat file `LICENSE` untuk detail.

---

## 📞 Support & Kontak

- **Email**: admin@pesoma.local
- **Website**: https://pesoma.uin-purwokerto.ac.id
- **Issues**: Buat issue di repository ini

---

**Terakhir diupdate**: 31 Mei 2026  
**Versi**: 1.0.0  
**Maintained by**: Tim Pengembang PESOMA 2026
#
