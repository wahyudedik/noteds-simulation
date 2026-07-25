Menurutku roadmap sebaiknya tidak hanya berdasarkan versi, tetapi juga berdasarkan kematangan produk. Dengan begitu, setiap tahap memiliki tujuan yang jelas dan tidak terburu-buru.


---

# NOTEDS ROADMAP

## Visi & Positioning

Noteds bukan "Steam untuk edukasi" atau "YouTube simulasi". Noteds adalah **platform Interactive Experience** — kategori baru yang memungkinkan pengetahuan berubah menjadi pengalaman interaktif.

### Perbandingan Platform

| Platform | Konten Utama | Tujuan |
|----------|--------------|--------|
| YouTube | Video | Menonton |
| GitHub | Source Code | Kolaborasi kode |
| Steam | Game | Bermain game |
| Figma Community | Design | Berbagi desain |
| Canva | Template | Membuat desain |
| **Noteds** | **Interactive Experience** | **Belajar dengan berinteraksi** |

### Kenapa Bukan Video?

Video bersifat pasif. Noteds bersifat aktif.

**Contoh: Belajar Hukum Newton**
- **YouTube**: Menonton orang menjelaskan
- **Noteds**: Mengubah massa, gaya, gesekan, lalu melihat hasilnya

### Kenapa Bukan Game?

Game dibuat untuk hiburan. Experience dibuat untuk memahami konsep. Walaupun sama-sama interaktif, tujuannya berbeda.

### Tagline

> **Noteds: Rasakan ilmunya.**

---

## PHASE 1 — Foundation (V1)

**Status**: Production (MVP)

**Tujuan**

Membuktikan bahwa pengguna tertarik belajar melalui Experience interaktif.

Fokus

Website production.

Authentication.

Dashboard.

Experience Engine v1.

HTML Renderer.

Upload HTML.

Kategori.

Mata pelajaran.

Search.

Trending.

Bookmark.

Like.

Komentar.

Follow Creator.

Notification.

Analytics dasar (View & Play).

Creator Profile.

Creator Dashboard.

Revenue Dashboard.

Admin Dashboard.


Arsitektur

Experience Engine
└── HTML Renderer


---

PHASE 2 — Experience Package (V2)

Tujuan

Mengubah HTML menjadi paket Experience yang mudah didistribusikan.

Fitur Baru

Upload ZIP.

manifest.json.

Thumbnail.

Preview.

Asset Management.

Versioning.

Auto Publish.

Auto Validation.

Auto Metadata.

Auto Notification.

Draft.

Preview sebelum Publish.


Struktur

experience.zip

index.html
manifest.json
thumbnail.webp
preview.webp
assets/

Keuntungan

Upload lebih mudah.

Metadata standar.

Siap untuk banyak renderer.



---

PHASE 3 — Multi Renderer (V3)

Tujuan

Noteds tidak lagi bergantung pada HTML.

Renderer

HTML

Canvas

SVG

ThreeJS

BabylonJS

Unity WebGL

Godot Web

Flutter Web

WebAssembly


Experience Engine

Experience Engine

├── HTML Renderer
├── Canvas Renderer
├── SVG Renderer
├── ThreeJS Renderer
├── Unity Renderer
├── Flutter Renderer
├── Godot Renderer
├── WASM Renderer
└── BabylonJS Renderer

Hasil

Creator bebas memilih teknologi.

Pengguna tidak peduli teknologi.


---

PHASE 4 — Experience Studio (V4)

Tujuan

Menurunkan hambatan membuat Experience.

Fitur

Visual Builder.

Drag & Drop.

Komponen.

Components

Slider

Button

Switch

Text

Formula

Graph

Chart

Canvas

SVG

Timeline

Map

Quiz

Video

Audio

Image

Animation

Code Block

Markdown

Table

3D Viewer


Workflow

Create

↓

Drag Component

↓

Preview

↓

Publish

Targetnya, guru yang tidak bisa coding tetap dapat membuat Experience.


---

PHASE 5 — Marketplace (V5)

Tujuan

Membangun ekosistem.

Marketplace

Experience

Components

Templates

Themes

Icons

Physics Module

Chemistry Module

Biology Module

Circuit Module

Timeline Module

Graph Module


Monetisasi

Paid Experience

Premium Component

Premium Theme

Premium Template



---

PHASE 6 — Creator Economy (V6)

Tujuan

Menjadikan Experience sebagai profesi.

Fitur

Creator Dashboard.

Revenue Dashboard.

Followers.

Analytics.

Creator Verification.

Creator Badge.

Monetization.

Play Revenue.

Creator Ranking.

Experience Ranking.

Trending Creator.

Top Creator.

Sponsor.

Affiliate.


---

PHASE 7 — Experience SDK (V7)

Tujuan

Developer dapat membuat renderer sendiri.

SDK

Plugin.

Renderer.

Experience Component.

Experience API.

Contoh

VR Renderer.

AR Renderer.

XR Renderer.

3D Renderer.

Physics Renderer.

Chemical Renderer.

Biology Renderer.

Timeline Renderer.


---

PHASE 8 — Experience Package Format (V8)

Tujuan

Noteds memiliki standar paket sendiri.

Misalnya

gravity.nxp

atau

atom.nxp

atau

photosynthesis.nxp

Di dalamnya

manifest.json

renderer/

assets/

config/

media/

scripts/

Semua renderer menggunakan format yang sama.


---

PHASE 9 — Universal Experience Engine (V9)

Tujuan

Noteds menjadi Operating System untuk Experience.

Semua Experience.

Semua Renderer.

Semua Device.

Semua Platform.

Support

Browser

Android

iOS

Windows

Linux

macOS

Smart TV

VR

AR

XR



---

PHASE 10 — Global Experience Ecosystem (Vision)

Tujuan

Menjadi standar dunia untuk distribusi Experience.

Seperti:

YouTube → Video

GitHub → Source Code

Figma → Design

Steam → Game

Noteds → Experience


Di tahap ini, Experience menjadi media pembelajaran baru yang dapat dibuat, dibagikan, dimainkan, dimonetisasi, dan diintegrasikan ke berbagai platform.


---

Prinsip Arsitektur (Tidak Berubah)

1. Backward Compatibility

Semua Experience lama tetap dapat dijalankan.

HTML yang dibuat pada V1 tetap dapat dimainkan di V10.



2. Technology Agnostic

Noteds tidak bergantung pada satu framework atau renderer.

Renderer boleh berganti, Experience tetap berjalan.



3. Creator First

Membuat Experience harus semakin mudah di setiap versi.

Dari upload HTML → Experience Package → Drag & Drop Studio.



4. Experience First

Pengguna tidak melihat teknologi di baliknya.

Yang mereka lihat hanyalah Experience yang menarik, interaktif, dan mudah dipelajari.




Menurutku, roadmap ini menjaga keseimbangan antara target jangka pendek (mengembangkan produk yang sudah production), target menengah (membangun ekosistem kreator), dan visi jangka panjang (menjadikan Noteds sebagai standar distribusi Experience interaktif), tanpa mengorbankan kompatibilitas dengan konten yang sudah dibuat sejak versi pertama.
