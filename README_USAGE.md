# 📚 Panduan Penggunaan PESOMA 2026

Sistem manajemen **PESOMA 2026** (Pekan Seni & Olahraga Mahasiswa) untuk UIN Prof. K.H. Saifuddin Zuhri Purwokerto. Dokumentasi lengkap cara menggunakan sistem ini untuk setiap role pengguna.

---

## 📋 Daftar Isi

1. [Pengenalan Sistem](#pengenalan-sistem)
2. [Persyaratan Sistem](#persyaratan-sistem)
3. [Instalasi & Setup](#instalasi--setup)
4. [Akun Test](#akun-test)
5. [Panduan Penggunaan per Role](#panduan-penggunaan-per-role)
6. [Fitur Utama](#fitur-utama)
7. [FAQ & Troubleshooting](#faq--troubleshooting)

---

## 🎯 Pengenalan Sistem

PESOMA 2026 adalah platform digital untuk mengelola:
- **Pendaftaran peserta** dalam berbagai cabang lomba
- **Upload karya** peserta
- **Penilaian** oleh juri
- **Verifikasi peserta** oleh panitia
- **Pengumuman** finalis dan pemenang
- **Manajemen jadwal** dan data kompetisi

### Fitur Utama:
✅ Multi-role authentication (Admin, Panitia, Juri, Peserta)  
✅ Dashboard interaktif untuk setiap role  
✅ Sistem penilaian terstruktur  
✅ Upload dan manajemen karya  
✅ Notifikasi real-time  
✅ Laporan dan statistik  
✅ Responsive design (mobile-friendly)

---

## 💻 Persyaratan Sistem

### Server Requirements:
- PHP 8.1 atau lebih tinggi
- MySQL 5.7 atau MariaDB 10.3+
- Apache dengan mod_rewrite enabled
- Minimal 512MB RAM

### Browser Support:
- Chrome/Chromium (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

### Koneksi Internet:
- Stabil untuk upload file
- Minimal 1 Mbps untuk pengalaman optimal

---

## 🚀 Instalasi & Setup

### 1. Download & Extract
```bash
# Clone atau download project
git clone <repository-url>
cd pesoma

# Atau extract ZIP ke folder htdocs
# d:\xampp\htdocs\pesoma
```

### 2. Setup Database
```bash
# Import database
mysql -u root -p < sql/database.sql

# Atau gunakan phpMyAdmin:
# 1. Buka http://localhost/phpmyadmin
# 2. Buat database baru: pesoma_2026
# 3. Import file: sql/database.sql
```

### 3. Konfigurasi
Edit file `config/config.php`:
```php
// Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pesoma_2026');

// App
define('APP_NAME', 'PESOMA 2026');
define('APP_URL', 'http://localhost/pesoma');
```

### 4. Setup Folder Uploads
```bash
# Buat folder untuk uploads
mkdir -p public/uploads
chmod 755 public/uploads
```

### 5. Akses Sistem
```
URL: http://localhost/pesoma
```

---

## 🔐 Akun Test

Gunakan akun berikut untuk testing:

| Role | Email | Password | Akses |
|------|-------|----------|-------|
| **Admin** | admin@pesoma.local | admin | http://localhost/pesoma/src/admin/dashboard.php |
| **Panitia** | panitia1@pesoma.local | Pesoma2026 | http://localhost/pesoma/src/panitia/dashboard.php |
| **Juri** | juri.seni@pesoma.local | Pesoma2026 | http://localhost/pesoma/src/juri/dashboard.php |
| **Peserta** | ahmad@student.local | Pesoma2026 | http://localhost/pesoma/src/peserta/dashboard.php |

### Cara Login:
1. Buka http://localhost/pesoma
2. Klik tombol "Login" di navbar
3. Masukkan email dan password
4. Klik "Masuk"

---

## 👥 Panduan Penggunaan per Role

### 🔧 ADMIN

**Akses:** http://localhost/pesoma/src/admin/dashboard.php

#### Fungsi Utama:
- Kelola user (tambah, edit, hapus)
- Kelola cabang lomba
- Kelola aspek penilaian
- Kelola jadwal
- Backup database
- Lihat log aktivitas
- Pengaturan sistem

#### Cara Menggunakan:

**1. Kelola User**
```
Menu: Kelola User
- Klik "Tambah User" untuk menambah user baru
- Pilih role: Admin, Panitia, Juri, atau Peserta
- Isi email dan password
- Klik "Simpan"
```

**2. Kelola Cabang Lomba**
```
Menu: Kelola Cabang Lomba
- Lihat daftar cabang lomba aktif
- Klik "Edit" untuk mengubah detail
- Klik "Nonaktifkan" untuk menonaktifkan cabang
- Klik "Tambah" untuk menambah cabang baru
```

**3. Kelola Jadwal**
```
Menu: Kelola Jadwal
- Lihat jadwal semua kegiatan
- Klik "Edit" untuk mengubah tanggal/waktu
- Klik "Hapus" untuk menghapus jadwal
- Klik "Tambah" untuk menambah jadwal baru
```

**4. Backup Database**
```
Menu: Backup Database
- Klik "Backup Sekarang"
- File backup akan diunduh otomatis
- Simpan di tempat aman
```

---

### 📋 PANITIA

**Akses:** http://localhost/pesoma/src/panitia/dashboard.php

#### Fungsi Utama:
- Verifikasi peserta
- Lihat daftar karya
- Tentukan finalis
- Input pemenang
- Kelola jadwal
- Buat pengumuman
- Lihat laporan

#### Cara Menggunakan:

**1. Verifikasi Peserta**
```
Menu: Verifikasi Peserta
- Filter berdasarkan cabang lomba atau status
- Lihat daftar peserta yang perlu diverifikasi
- Klik "Detail" untuk melihat informasi lengkap
- Klik "Terima" atau "Tolak" untuk memverifikasi
- Klik "Hadir TM" atau "Hadir Final" untuk check-in
```

**2. Tentukan Finalis**
```
Menu: Tentukan Finalis
- Pilih cabang lomba
- Lihat daftar peserta yang lolos penyisihan
- Pilih peserta yang akan menjadi finalis
- Klik "Simpan" untuk menyimpan
```

**3. Input Pemenang**
```
Menu: Input Pemenang
- Pilih cabang lomba
- Pilih finalis untuk setiap posisi (Juara 1, 2, 3)
- Isi skor akhir
- Klik "Simpan"
```

**4. Buat Pengumuman**
```
Menu: Buat Pengumuman
- Klik "Buat Pengumuman Baru"
- Isi judul dan isi pengumuman
- Pilih tipe: Umum, Finalis, atau Pemenang
- Klik "Publikasikan"
```

---

### ⭐ JURI

**Akses:** http://localhost/pesoma/src/juri/dashboard.php

#### Fungsi Utama:
- Penilaian penyisihan
- Penilaian final
- Lihat riwayat penilaian
- Lihat hasil penilaian

#### Cara Menggunakan:

**1. Penilaian Penyisihan**
```
Menu: Penilaian Penyisihan
- Pilih cabang lomba yang ditugaskan
- Lihat daftar peserta penyisihan
- Klik "Nilai" untuk memberikan penilaian
- Isi skor untuk setiap aspek penilaian
- Klik "Simpan"
```

**2. Penilaian Final**
```
Menu: Penilaian Final
- Pilih cabang lomba
- Lihat daftar finalis
- Klik "Nilai" untuk memberikan penilaian
- Isi skor untuk setiap aspek
- Klik "Simpan"
```

**3. Riwayat Penilaian**
```
Menu: Riwayat Penilaian
- Lihat semua penilaian yang sudah diberikan
- Klik "Edit" untuk mengubah penilaian
- Lihat tanggal dan waktu penilaian
```

---

### 👨‍🎓 PESERTA

**Akses:** http://localhost/pesoma/src/peserta/dashboard.php

#### Fungsi Utama:
- Daftar lomba
- Upload karya
- Lihat status pendaftaran
- Lihat jadwal
- Lihat pengumuman
- Lihat hasil penilaian

#### Cara Menggunakan:

**1. Daftar Lomba**
```
Menu: Daftar Lomba
- Klik "Daftar Sekarang"
- Pilih cabang lomba yang ingin diikuti
- Isi data diri (NIM, Nama, Fakultas, dll)
- Tambahkan anggota tim (jika diperlukan)
- Tambahkan pendamping (jika diperlukan)
- Klik "Daftar"
```

**2. Upload Karya**
```
Menu: Upload Karya
- Lihat daftar lomba yang sudah didaftar
- Klik "Upload Karya"
- Pilih file karya (format sesuai ketentuan)
- Isi deskripsi karya
- Klik "Upload"
```

**3. Lihat Status Pendaftaran**
```
Menu: Status Pendaftaran
- Lihat status verifikasi (Pending, Diterima, Ditolak)
- Lihat status upload karya
- Lihat status penilaian
- Lihat hasil akhir (jika sudah diumumkan)
```

**4. Lihat Jadwal**
```
Menu: Jadwal
- Lihat jadwal penyisihan
- Lihat jadwal final
- Lihat lokasi dan waktu kegiatan
```

---

## 🎨 Fitur Utama

### 1. Dashboard
Setiap role memiliki dashboard yang menampilkan:
- Statistik penting
- Aktivitas terbaru
- Notifikasi
- Quick actions

### 2. Sistem Penilaian
- Penilaian penyisihan dan final
- Aspek penilaian yang terstruktur
- Skor otomatis dihitung
- Ranking otomatis

### 3. Manajemen Karya
- Upload karya dengan validasi format
- Preview karya
- Tracking status upload
- Riwayat upload

### 4. Notifikasi
- Email notifikasi untuk event penting
- In-app notifications
- Status real-time

### 5. Laporan
- Laporan peserta
- Laporan penilaian
- Laporan finalis dan pemenang
- Export ke Excel/PDF

### 6. Responsive Design
- Optimal di desktop, tablet, mobile
- Touch-friendly interface
- Fast loading

---

## ❓ FAQ & Troubleshooting

### Q: Lupa password, bagaimana?
**A:** 
1. Klik "Lupa Password" di halaman login
2. Masukkan email
3. Cek email untuk link reset password
4. Buat password baru

### Q: Tidak bisa upload karya, kenapa?
**A:** Kemungkinan penyebab:
- File terlalu besar (max 50MB)
- Format file tidak didukung
- Folder uploads tidak memiliki permission
- Solusi: Hubungi admin

### Q: Bagaimana cara menambah cabang lomba?
**A:** 
- Login sebagai Admin
- Menu: Kelola Cabang Lomba
- Klik "Tambah Cabang"
- Isi detail cabang
- Klik "Simpan"

### Q: Bagaimana cara mengubah jadwal?
**A:**
- Login sebagai Admin
- Menu: Kelola Jadwal
- Klik "Edit" pada jadwal yang ingin diubah
- Ubah tanggal/waktu
- Klik "Simpan"

### Q: Bagaimana cara backup database?
**A:**
- Login sebagai Admin
- Menu: Backup Database
- Klik "Backup Sekarang"
- File akan diunduh otomatis

### Q: Bagaimana cara reset password user?
**A:**
- Login sebagai Admin
- Menu: Kelola User
- Cari user yang ingin direset
- Klik "Reset Password"
- Password baru akan dikirim ke email user

### Q: Bagaimana cara melihat laporan?
**A:**
- Login sebagai Panitia
- Menu: Laporan
- Pilih jenis laporan
- Klik "Download" untuk export

### Q: Bagaimana cara menghubungi support?
**A:**
- Klik "Hubungi Kami" di footer
- Isi form kontak
- Tim support akan merespons dalam 24 jam

---

## 📞 Kontak & Support

**Email:** pesoma@uin-purwokerto.ac.id  
**Telepon:** +62 281-635624  
**Website:** https://uin-purwokerto.ac.id

---

## 📄 Lisensi

PESOMA 2026 © 2026 UIN Prof. K.H. Saifuddin Zuhri Purwokerto. All rights reserved.

---

## 🔄 Versi

**Versi:** 1.0.0  
**Tanggal Rilis:** 31 Mei 2026  
**Status:** Production Ready

---

**Terakhir diperbarui:** 31 Mei 2026
