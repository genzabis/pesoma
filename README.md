# 📋 Portal PESOMA 2026

Portal resmi **PESOMA III 2026** (Pekan Seni dan Olahraga Mahasiswa) — UIN Prof. K.H. Saifuddin Zuhri Purwokerto.

> Sistem pendaftaran, penilaian, dan publikasi 14 cabang lomba untuk 6 fakultas. Backend PHP murni + MySQL, tanpa framework JS.

---

## 🚀 Quick Start

```bash
# 1. Letakkan repo di htdocs XAMPP
git clone https://github.com/genzabis/pesoma.git d:/xampp/htdocs/pesoma

# 2. Import skema + seed data
mysql -u root pesoma < sql/database.sql
mysql -u root pesoma < sql/seeding.sql

# 3. Cek konfigurasi
# config/database.php  -> kredensial DB
# config/config.php    -> APP_URL (http://localhost/pesoma)
# config/constants.php -> ROLE_*, fakultas, batas upload

# 4. Buka di browser
start http://localhost/pesoma
```

Akun default admin: `admin@pesoma.local` / `AdminPesoma2026!`

---

## 🗂️ Struktur

```
pesoma/
├── index.php                 # Landing → /pages/beranda.php
├── pages/                    # Public site (Beranda, Cabang Lomba, Jadwal, dll)
├── src/
│   ├── auth/                 # Login, Register, Forgot Password
│   ├── admin/                # Dashboard admin + helper _layout.php
│   ├── panitia/              # Verifikasi peserta, finalis, pemenang, laporan
│   ├── juri/                 # Penilaian penyisihan & final
│   └── peserta/              # Daftar lomba, upload karya, tim, status
├── api/                      # Endpoint JSON (statistik, pengumuman, jadwal)
├── includes/                 # auth.php, session.php, header/footer/layout
├── config/                   # database, constants, config
├── assets/css/               # pesoma-public.css, dashboard-pesoma.css
├── design/                   # Gallery preview semua halaman + preview.php
├── public/uploads/           # Berkas peserta (artikel, ppt, video, poster)
├── scripts/                  # Cron jobs + helper verifikasi (final-check.ps1)
├── sql/                      # database.sql, seeding.sql, migration/
└── storage/logs/             # Log error
```

---

## 🎨 Design System

Editorial monochrome dengan tiga blok warna aksen.

| Token | Nilai | Pemakaian |
| --- | --- | --- |
| `--c-ink` | `#0f1115` | Teks utama, primary button |
| `--block-cream` | `#f3eadb` | Section accent (Cabang Lomba, sidebar, table header) |
| `--block-sage` | `#d9e1cb` | Section accent (Jadwal, blok finalis) |
| `--block-navy` | `#0c1733` | CTA section, footer informasi resmi |
| `--ff` | Plus Jakarta Sans / Inter | Body & display |
| `--ff-mono` | JetBrains Mono | Eyebrow, label, kode peserta |

Dua source of truth stylesheet:

- `assets/css/pesoma-public.css` — public site + auth pages
- `assets/css/dashboard-pesoma.css` — 4 dashboard role (admin/panitia/juri/peserta)

Komponen utama: `.btn` (pill), `.card` (flat), `.badge` (mono uppercase), `.table` (header cream), `.section-head` + `.section-eyebrow`, `.timeline`, `.list-item`.

---

## 👥 Role & Hak Akses

| Role | Endpoint | Fitur Utama |
| --- | --- | --- |
| Admin | `/src/admin/` | Kelola user, cabang lomba, aspek penilaian, jadwal, backup DB, log, pengaturan |
| Panitia | `/src/panitia/` | Verifikasi peserta, daftar karya, tentukan finalis, input pemenang, laporan |
| Juri | `/src/juri/` | Penilaian penyisihan/final per aspek, riwayat |
| Peserta | `/src/peserta/` | Daftar lomba, upload karya (preview file), tim saya, status |

Auth guard: `includes/auth.php` → `require_role(ROLE_*)` di tiap layout dashboard.

---

## 🔧 Verifikasi

Script PowerShell siap pakai untuk mengetes semua jalur sekaligus:

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File scripts/final-check.ps1
```

Output:
- PHP lint untuk semua file
- 8 public pages
- 3 auth pages
- 26 dashboard pages (lewat `design/preview.php` auto-login dev)
- Status `ALL CLEAR ✓` jika tidak ada error

Target saat ini: **39/39 pass, 0 PHP lint error.**

---

## 🖼️ Design Gallery

`design/index.html` — preview interaktif semua halaman dalam satu page sebagai iframe gallery, dikelompokkan per area (Public / Auth / Admin / Panitia / Juri / Peserta).

`design/preview.php` — endpoint dev yang otomatis set session sebagai user pertama dengan role tertentu lalu redirect ke halaman target. Iframe dashboard menampilkan tampilan asli, bukan halaman login.

> Hanya bisa diakses dari `localhost / 127.0.0.1 / ::1`. Jangan deploy folder `design/` ke production.

Buka: <http://localhost/pesoma/design/index.html>

---

## ⏰ Cron Jobs

Lihat `docs/cron.md` dan `scripts/`:

- `close_registration.php` — tutup pendaftaran setelah deadline
- `close_upload.php` — tutup pengumpulan karya
- `reminder_upload.php` — kirim email pengingat upload
- `notify_finalists.php` / `notify_winners.php` — pengumuman otomatis
- `cleanup_temp_files.php` / `cleanup_tokens.php` — housekeeping
- `backup_database.php` — backup harian
- `generate_daily_report.php` — laporan harian PDF/CSV

Untuk Windows: pakai `scripts/windows-task-scheduler.example.bat`.

---

## 🛡️ Keamanan

- CSRF token wajib di tiap form (`csrf_field()` + `verify_csrf()`)
- Session: `httponly`, `samesite=Lax`, timeout via `SESSION_TIMEOUT`
- Password disimpan dengan `password_hash` BCRYPT
- Upload divalidasi ekstensi + MIME (`finfo`) + ukuran maks
- Pre-check uniqueness email & NIM saat tambah/edit user (mencegah PDOException 1062)
- `design/preview.php` di-gate ke loopback only

---

## 📝 Lisensi

MIT License.

---

**Maintained by**: Tim Pengembang PESOMA 2026
**Versi**: 2.0.0 (editorial redesign)
**Terakhir diupdate**: 1 Juni 2026
