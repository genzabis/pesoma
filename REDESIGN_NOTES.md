# PESOMA 2026 - Redesign Notes

## Overview
Redesign semua halaman publik PESOMA menggunakan design system dari `design/index.html` dan `design/style.css` sebagai acuan.

## Perubahan yang Dilakukan

### 1. Design System
- **File CSS**: `assets/css/pesoma-design.css` (copy dari `design/style.css`)
- **Design Tokens**:
  - Color: `--color-surface-muted: #0b2f9f` (PESOMA Blue)
  - Typography: Plus Jakarta Sans & Inter
  - Spacing: Scale 6px - 80px
  - Border Radius: 6px - 14px
  - Shadows: Premium card shadows

### 2. Header (includes/header.php)
- ✅ Dark header dengan background `#000000`
- ✅ Brand mark dengan gradient blue
- ✅ Navigation links dengan hover effects
- ✅ Sticky positioning
- ✅ Responsive design
- ✅ Menggunakan design tokens dari pesoma-design.css

### 3. Footer (includes/footer.php)
- ✅ Dark background (#000 → #050b1e) selaras hero & header
- ✅ 3-column layout (Brand, Navigasi, Kontak)
- ✅ Radial gradient overlay + grid mesh background
- ✅ Social icons + hover transition
- ✅ Responsive grid (collapse di < 900px)
- ✅ Menggunakan design tokens dari pesoma-design.css

### 4. Halaman yang Perlu Diupdate
Semua halaman di folder `pages/` perlu diupdate untuk menggunakan:
- `public_header()` dari `includes/header.php` (sudah updated)
- `public_footer()` dari `includes/footer.php` (sudah updated)
- Komponen UI dari design system (cards, buttons, badges, dll)

#### Daftar Halaman:
- [x] `pages/beranda.php` - Hero, stats, grid lomba, timeline, pengumuman, CTA
- [x] `pages/cabang-lomba.php` - Hero terpusat + grid card
- [x] `pages/detail-lomba.php` - Hero detail + info card + tabel aspek
- [x] `pages/jadwal.php` - Hero + tabel agenda + CTA
- [x] `pages/pengumuman.php` - Hero + list pengumuman + tabel finalis/pemenang
- [x] `pages/detail-pengumuman.php` - Hero + info card + artikel pengumuman
- [x] `pages/tentang.php` - Hero + visi-misi + fitur portal
- [x] `pages/kontak.php` - Hero + kontak card + FAQ
- [x] `pages/unduh-juknis.php` - Hero + tabel juknis

### 5. Auth Pages (src/auth/*)
Helper baru: `includes/auth-layout.php` untuk split-layout konsisten.

- [x] `src/auth/login.php` - Split layout dark + form login w/ icon input
- [x] `src/auth/register.php` - Split layout dark + form registrasi w/ grid
- [x] `src/auth/forgot-password.php` - Split layout dark + form reset

### 6. Dashboard Pages (src/{admin,panitia,juri,peserta}/*)
File CSS baru: `assets/css/dashboard-pesoma.css` (override theme).
Diload setelah `<style>` inline di setiap `_layout.php` lewat tag `<link>`,
sehingga override warna brand (hijau lama → biru `#0b2f9f`), sidebar putih
clean, card flat, badge pill, dan typography selaras design system.

- [x] `src/admin/_layout.php` - Override theme via dashboard-pesoma.css
- [x] `src/panitia/_layout.php` - Override theme via dashboard-pesoma.css
- [x] `src/juri/_layout.php` - Override theme via dashboard-pesoma.css
- [x] `src/peserta/_layout.php` - Override theme via dashboard-pesoma.css

## Design System Components

### Colors
```css
--color-surface-muted: #0b2f9f;    /* Primary Blue */
--color-accent: #10b981;            /* Success Green */
--color-surface-base: #000000;      /* Header Dark */
--color-bg-light: #f8fafc;          /* Body Background */
```

### Typography
```css
--font-family-primary: "Plus Jakarta Sans", "Inter", system-ui, sans-serif;
--font-size-xs: 9px;
--font-size-sm: 10.5px;
--font-size-md: 12px;
--font-size-lg: 15px;
--font-size-xl: 18px;
```

### Components Available
- `.btn.primary` - Primary button
- `.btn.secondary` - Secondary button
- `.card` - Card component
- `.badge` - Badge/tag component
- `.hero` - Hero section
- `.section` - Content section
- `.stats-grid` - Statistics grid
- `.timeline` - Timeline component

## Next Steps
1. Update konten halaman `beranda.php` dengan hero section baru
2. Update halaman lainnya satu per satu
3. Test responsive design di berbagai ukuran layar
4. Verifikasi semua link dan navigasi berfungsi

## Testing
- Browser: Chrome, Firefox, Safari, Edge
- Devices: Desktop, Tablet, Mobile
- Check: Navigation, Links, Responsive, Performance

---
**Redesign Date**: 1 Juni 2026
**Design System**: PESOMA 2026 Premium (Figma Canvas Style)
