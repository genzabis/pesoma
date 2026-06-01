# Security & Reporting Policy — PESOMA III

## Melaporkan Kerentanan

Jika kamu menemukan masalah keamanan (XSS, SQLi, IDOR, kebocoran data, dll), **jangan buka public Issue**. Laporkan langsung ke channel privat:

- Email: **pesoma@uinsaizu.ac.id**
- Subject: `[SECURITY] <ringkasan singkat>`

Sertakan:
- Deskripsi masalah dan dampaknya
- Langkah reproduksi (URL, payload, screenshot)
- Versi/commit hash yang dites

Kami berusaha membalas dalam 3 hari kerja. Sebelum public disclosure, beri kami waktu minimal 30 hari untuk patch.

## Pelanggaran Lisensi

Repo ini berlisensi proprietary (lihat [`LICENSE`](../LICENSE)). Kalau menemukan repo lain yang menyalin source code PESOMA III tanpa izin:

1. Kirim bukti ke email di atas (URL repo pelaku, perbandingan kode)
2. Kami akan submit DMCA Takedown ke GitHub: <https://github.com/contact/dmca>

## Versi yang Didukung

Hanya `main` branch yang aktif disupport. Branch lain di luar tag rilis resmi tidak menerima patch.

## Bug Bounty

Saat ini tidak ada program bug bounty berbayar. Apresiasi dapat berupa nama di section "Acknowledgments" di README setelah perbaikan dirilis.
