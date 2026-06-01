# Panduan Cron PESOMA 2026

Dokumen ini menjelaskan cara menjalankan script otomatis di folder `scripts/` untuk automasi tugas-tugas berkala seperti backup database, pengiriman notifikasi, dan pembersihan file sementara.

**Daftar Script Otomatis:**
- `backup_database.php` - Backup database harian
- `close_registration.php` - Tutup pendaftaran otomatis
- `close_upload.php` - Tutup upload karya otomatis
- `reminder_upload.php` - Pengingat upload untuk peserta
- `cleanup_tokens.php` - Pembersihan token yang kadaluarsa
- `notify_finalists.php` - Notifikasi finalis terpilih
- `notify_winners.php` - Notifikasi pemenang
- `cleanup_temp_files.php` - Pembersihan file sementara
- `generate_daily_report.php` - Laporan harian sistem

## PHP CLI

Di Windows lokal XAMPP, gunakan path penuh karena `php` belum masuk PATH:

```bat
d:\xampp\php\php.exe d:\xampp\htdocs\pesoma\scripts\generate_daily_report.php
```

Di Linux/server produksi:

```bash
php /var/www/pesoma/scripts/generate_daily_report.php
```

## Environment Variable Opsional

Script cron membaca konfigurasi berikut:

| Variable             | Fungsi                                                | Default                                                      |
| -------------------- | ----------------------------------------------------- | ------------------------------------------------------------ |
| `CRON_LOG_FILE`      | Override lokasi file log cron                         | `/var/log/pesoma/cron.log`, fallback `storage/logs/cron.log` |
| `CRON_ADMIN_EMAIL`   | Email admin penerima error                            | `admin@pesoma.local`                                         |
| `CRON_FROM_EMAIL`    | Email pengirim notifikasi                             | `no-reply@pesoma.local`                                      |
| `CRON_FROM_NAME`     | Nama pengirim notifikasi                              | `PESOMA Cron`                                                |
| `CRON_MAIL_DISABLED` | Set `1` untuk mematikan pengiriman email saat testing | kosong                                                       |

> Catatan: `mail()` PHP perlu mail server/SMTP yang aktif. Untuk produksi, pastikan server email sudah dikonfigurasi atau migrasikan ke SMTP library seperti PHPMailer.
>
> Di Windows/XAMPP, log otomatis memakai `storage/logs/cron.log` kecuali `CRON_LOG_FILE` diisi manual. Di Linux, default utama tetap `/var/log/pesoma/cron.log` dan fallback ke `storage/logs/cron.log` jika folder log tidak writable.

## Rekomendasi Jadwal

| Script                      | Jadwal                                      |
| --------------------------- | ------------------------------------------- |
| `backup_database.php`       | Harian, 01:00                               |
| `close_registration.php`    | Tiap 15 menit sekitar deadline pendaftaran  |
| `close_upload.php`          | Tiap 15 menit sekitar deadline upload       |
| `reminder_upload.php`       | Harian, 08:00                               |
| `cleanup_tokens.php`        | Tiap jam                                    |
| `notify_finalists.php`      | Tiap 15 menit pada hari pengumuman finalis  |
| `notify_winners.php`        | Tiap 15 menit pada hari pengumuman pemenang |
| `cleanup_temp_files.php`    | Harian, 02:00                               |
| `generate_daily_report.php` | Harian, 23:55                               |

## Windows Task Scheduler

1. Buka **Task Scheduler**.
2. Pilih **Create Task**.
3. Pada tab **Actions**:
   - Program/script: `d:\xampp\php\php.exe`
   - Add arguments: `d:\xampp\htdocs\pesoma\scripts\nama_script.php`
   - Start in: `d:\xampp\htdocs\pesoma`
4. Atur trigger sesuai jadwal script.
5. Aktifkan opsi **Run whether user is logged on or not** jika diperlukan.

Contoh command manual:

```bat
d:\xampp\php\php.exe d:\xampp\htdocs\pesoma\scripts\backup_database.php
```

## Linux Cron

Contoh crontab tersedia di `scripts/crontab.example`.

Edit crontab:

```bash
crontab -e
```

Tambahkan jadwal sesuai kebutuhan dan sesuaikan path project.

## Keamanan Storage

Folder `storage/` dilindungi `.htaccess` agar backup, log, marker, dan report tidak bisa diakses publik melalui Apache.

## Troubleshooting

### Script tidak berjalan
- Pastikan PHP CLI sudah terinstall dan dapat diakses
- Cek permission folder `storage/logs/` (harus writable)
- Lihat error log di `storage/logs/php-error.log`

### Email notifikasi tidak terkirim
- Pastikan mail server/SMTP sudah dikonfigurasi
- Cek `CRON_MAIL_DISABLED` tidak diset ke `1`
- Lihat log di `storage/logs/cron.log`

### Database backup gagal
- Pastikan folder `storage/` writable
- Cek koneksi database di `config/config.php`
- Pastikan user database memiliki privilege `SELECT, LOCK TABLES`

## Monitoring

Cek status script cron dengan melihat log:

```bash
# Linux
tail -f /var/log/pesoma/cron.log

# Windows (PowerShell)
Get-Content storage/logs/cron.log -Tail 20 -Wait
```

Setiap script akan mencatat waktu eksekusi, status, dan error (jika ada) ke log file.
