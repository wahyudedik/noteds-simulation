# Plan V6: Lightweight Round 3 — Bug Fixes, UX & Footer

> **Tanggal:** 28 Juli 2026
> **Berdasarkan:** Audit menyeluruh terhadap seluruh codebase + review plan V1-V5

---

## Status Sebelumnya

- ✅ V1 Plan (14 task): Selesai
- ✅ V2 Plan (16 task): Selesai
- ✅ V3 Plan (10 task): Selesai
- ✅ V4 Plan (10 task): Selesai
- ✅ V5 Plan (25 task): Selesai

---

## Temuan Audit V6

Dari review menyeluruh terhadap seluruh view, component, dan layout, ditemukan task-task baru.

### Kategori 1: Dark Mode Bug (Ringan)

| # | Temuan | File | Fix |
|---|--------|------|-----|
| 1 | Missing `dark:text-white` pada h1 kategori | `simulations/category.blade.php:15` | ✅ Ditambahkan |
| 2 | Missing `dark:text-white` pada h2 explore | `simulations/explore.blade.php:5` | ✅ Ditambahkan |

### Kategori 2: Konsistensi UX (Ringan)

| # | Temuan | File | Fix |
|---|--------|------|-----|
| 3 | Thread delete pakai native `confirm()` | `forum/show.blade.php:130` | ✅ Diganti ke `confirmSubmit()` |
| 4 | Ad delete pakai native `confirm()` | `studio/ads.blade.php:172` | ✅ Diganti ke `confirmSubmit()` |

### Kategori 3: Fitur Baru (Ringan)

| # | Temuan | File | Fix |
|---|--------|------|-----|
| 5 | Tidak ada global footer component | Semua halaman | ✅ Dibuat `app-footer.blade.php` |
| 6 | Footer belum terintegrasi di layout | `layouts/app.blade.php` | ✅ Ditambahkan `@include` |

### Kategori 4: UX Improvement (Ringan)

| # | Temuan | File | Fix |
|---|--------|------|-----|
| 7 | Notification bell hanya fetch sekali | `notification-bell.blade.php` | ✅ Ditambah polling 30 detik |
| 8 | Notification bell tidak punya aria-label | `notification-bell.blade.php` | ✅ Ditambahkan `aria-label` |
| 9 | Keyboard shortcut `/` untuk search | `app-header.blade.php` | ✅ Sudah ada (dari V4) |

---

## File yang Diubah

| File | Perubahan |
|------|-----------|
| `resources/views/simulations/category.blade.php` | Tambah `dark:text-white` pada h1 |
| `resources/views/simulations/explore.blade.php` | Tambah `dark:text-white` pada h2 header |
| `resources/views/forum/show.blade.php` | Ganti native `confirm()` → `confirmSubmit()` |
| `resources/views/studio/ads.blade.php` | Ganti native `confirm()` → `confirmSubmit()` |
| `resources/views/components/app-footer.blade.php` | **BARU** — Global footer component |
| `resources/views/layouts/app.blade.php` | Tambah `<x-app-footer />` sebelum `</div>` |
| `resources/views/components/notification-bell.blade.php` | Tambah polling 30s + `aria-label` + `min-h-[44px]` |

---

## Verifikasi

- ✅ `php artisan test --compact` — 319 passed (793 assertions)
- ✅ `vendor/bin/pint --dirty --format agent` — passed

---

## Estimasi

- **7 file diubah** (6 existing + 1 baru)
- **Waktu:** ~10 menit
- **Risiko:** Sangat rendah — perubahan UI/CSS + komponen baru
