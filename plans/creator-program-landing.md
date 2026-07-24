# Rencana: Halaman Landing Program Kreator Noteds

## Tujuan
Membuat halaman landing publik `/become-creator` yang mempresentasikan brief program kreator Noteds kepada pengunjung, dengan konten yang akurat sesuai sistem ad revenue yang sudah ada.

## Komponen yang Dibutuhkan

### 1. Route
- Tambahkan `GET /become-creator` sebagai route publik (tanpa auth middleware)
- Route POST `/become-creator` yang sudah ada tetap dipertahankan (untuk aksi become creator)

### 2. Controller
- Tambahkan method `becomeCreatorPage()` di `DashboardController` yang mengembalikan view landing
- Method ini tidak memerlukan autentikasi

### 3. View Blade
- File: `resources/views/creators/become-creator.blade.php`
- Menggunakan `<x-guest-layout>` (atau layout standalone seperti landing page)
- Sections:
  1. Hero section dengan tagline
  2. "Apa itu Noteds Creator?"
  3. "Bagaimana Cara Kerjanya?" (alur 6 langkah)
  4. "Program Monetisasi" (berbasis ad revenue, BUKAN play count langsung)
  5. "Revenue Sharing Tiers" (Basic 55% → Platinum 85%)
  6. "Siapa yang Bisa Menjadi Kreator?"
  7. "Dashboard Kreator" (fitur-fitur yang tersedia)
  8. "Mengapa Menjadi Kreator?" (termasuk badge & sertifikasi)
  9. CTA: "Mulai Sekarang" → POST /become-creator (jika login) atau /login (jika belum)
  10. Disclaimer tentang penghasilan

### 4. Konten Akurat
- Penghasilan berasal dari **ad revenue sharing**, bukan langsung dari play count
- Skema: kreator memasang iklan di simulasi → impression/click → revenue → revenue share berdasarkan tier
- Minimum payout: Rp 500.000
- Badge & sertifikasi sebagai penghargaan non-materi

## File yang Perlu Diubah/Dibuat
1. `resources/views/creators/become-creator.blade.php` — **BARU**
2. `app/Http/Controllers/DashboardController.php` — tambah method
3. `routes/web.php` — tambah route GET
