@echo off
REM PESOMA 2026 Windows Task Scheduler examples.
REM Jalankan sebagai Administrator jika ingin membuat task otomatis.

set PROJECT_DIR=d:\xampp\htdocs\pesoma
set PHP_BIN=d:\xampp\php\php.exe

REM Backup database harian pukul 01:00
schtasks /Create /TN "PESOMA Backup Database" /SC DAILY /ST 01:00 /TR "\"%PHP_BIN%\" \"%PROJECT_DIR%\scripts\backup_database.php\"" /F

REM Cleanup token tiap jam
schtasks /Create /TN "PESOMA Cleanup Tokens" /SC HOURLY /MO 1 /TR "\"%PHP_BIN%\" \"%PROJECT_DIR%\scripts\cleanup_tokens.php\"" /F

REM Reminder upload harian pukul 08:00
schtasks /Create /TN "PESOMA Reminder Upload" /SC DAILY /ST 08:00 /TR "\"%PHP_BIN%\" \"%PROJECT_DIR%\scripts\reminder_upload.php\"" /F

REM Cleanup file temp harian pukul 02:00
schtasks /Create /TN "PESOMA Cleanup Temp Files" /SC DAILY /ST 02:00 /TR "\"%PHP_BIN%\" \"%PROJECT_DIR%\scripts\cleanup_temp_files.php\"" /F

REM Laporan harian pukul 23:55
schtasks /Create /TN "PESOMA Daily Report" /SC DAILY /ST 23:55 /TR "\"%PHP_BIN%\" \"%PROJECT_DIR%\scripts\generate_daily_report.php\"" /F

REM Script deadline/notifikasi dapat dibuat manual dengan trigger sesuai tanggal event.
