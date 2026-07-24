<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Program Kreator — Noteds</title>
    <meta name="description" content="Bergabung sebagai kreator Noteds. Buat simulasi interaktif, bagikan ilmu, dan berpeluang memperoleh penghasilan.">

    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .hero-gradient-creator {
            background: linear-gradient(135deg, #2d1b69 0%, #111827 50%, #1e293b 100%);
        }
        .tier-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }
        .step-number {
            background: linear-gradient(135deg, #6366f1, #3b82f6);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

    @include('components.app-header')

    {{-- Hero Section --}}
    <div class="hero-gradient-creator text-white py-16 md:py-24">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-1.5 text-sm text-purple-200 mb-6">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                Program Kreator
            </div>
            <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">
                Buat Simulasi. Bagikan Ilmu.<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-300 to-blue-300">Bangun Masa Depan.</span>
            </h1>
            <p class="text-gray-300 text-lg max-w-2xl mx-auto mb-8">
                Noteds bukan hanya tempat belajar — tempat para guru, dosen, mahasiswa, pelajar, dan kreator edukasi berbagi simulasi interaktif kepada dunia.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                @auth
                    @if(auth()->user()->isCreator())
                        <a href="{{ route('studio.dashboard') }}" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 rounded-xl font-semibold text-white transition shadow-lg shadow-purple-500/25">
                            Buka Studio →
                        </a>
                    @else
                        <form action="{{ route('become-creator') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 rounded-xl font-semibold text-white transition shadow-lg shadow-purple-500/25">
                                Mulai Menjadi Kreator →
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 rounded-xl font-semibold text-white transition shadow-lg shadow-purple-500/25">
                        Daftar & Mulai Berkreasi →
                    </a>
                    <a href="{{ route('login') }}" class="px-8 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl font-semibold text-white transition">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        {{-- Apa itu Noteds Creator? --}}
        <section class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Apa itu Noteds Creator?</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Noteds Creator adalah program bagi siapa saja yang ingin membuat simulasi edukasi interaktif menggunakan HTML dan membagikannya kepada jutaan pengguna melalui Noteds.
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Buat Simulasi</h3>
                    <p class="text-sm text-gray-500">Kembangkan simulasi interaktif menggunakan HTML, CSS, dan JavaScript.</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
                    <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Bagikan ke Dunia</h3>
                    <p class="text-sm text-gray-500">Publikasikan simulasi dan jangkau jutaan pengguna di seluruh Indonesia.</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
                    <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Pantau Performa</h3>
                    <p class="text-sm text-gray-500">Dashboard analitik lengkap untuk memantau views, plays, likes, dan penghasilan.</p>
                </div>
            </div>
        </section>

        {{-- Bagaimana Cara Kerjanya? --}}
        <section class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Bagaimana Cara Kerjanya?</h2>
                <p class="text-gray-600 text-lg">Enam langkah sederhana untuk memulai perjalananmu sebagai kreator.</p>
            </div>
            <div class="grid md:grid-cols-2 gap-6">
                @foreach([
                    ['title' => 'Buat Simulasi Interaktif', 'desc' => 'Kembangkan simulasi edukasi menggunakan HTML, CSS, dan JavaScript. Unggah file ZIP melalui Simulation Studio.'],
                    ['title' => 'Upload ke Simulation Studio', 'desc' => 'Upload paket simulasi, tambahkan judul, deskripsi, kategori, dan thumbnail yang menarik.'],
                    ['title' => 'Review oleh Tim Noteds', 'desc' => 'Simulasi akan melalui auto-scan keamanan dan review oleh tim kami untuk memastikan kualitas.'],
                    ['title' => 'Publikasikan ke Dunia', 'desc' => 'Setelah disetujui, simulasi dipublikasikan dan dapat ditemukan oleh jutaan pengguna.'],
                    ['title' => 'Pengguna Memainkan Simulasi', 'desc' => 'Simulasimu dimainkan, disukai, dibagikan, dan diikuti oleh komunitas belajar.'],
                    ['title' => 'Pantau & Kembangkan', 'desc' => 'Gunakan dashboard analitik untuk memantau performa dan terus tingkatkan kualitas karyamu.'],
                ] as $i => $step)
                    <div class="flex gap-4 items-start bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                        <div class="step-number w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm shrink-0">
                            {{ $i + 1 }}
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">{{ $step['title'] }}</h3>
                            <p class="text-sm text-gray-500">{{ $step['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Program Monetisasi --}}
        <section class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Program Monetisasi</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Noteds menyediakan program apresiasi bagi kreator berdasarkan performa iklan yang ditampilkan dalam simulasi.
                    Semakin banyak pengguna memainkan simulasi yang menyertakan iklan, semakin besar potensi penghasilanmu.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-blue-50">
                    <h3 class="font-semibold text-gray-900 text-lg">Revenue Sharing Tiers</h3>
                    <p class="text-sm text-gray-500 mt-1">Persentase penghasilan yang kamu terima meningkat seiring reputasi dan kualitas karyamu.</p>
                </div>
                <div class="grid md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                    @foreach([
                        ['tier' => 'Basic', 'share' => '55%', 'desc' => 'Creator baru', 'color' => 'gray', 'bg' => 'bg-gray-50'],
                        ['tier' => 'Verified', 'share' => '65%', 'desc' => '10+ simulasi, rating ≥ 4.0', 'color' => 'blue', 'bg' => 'bg-blue-50'],
                        ['tier' => 'Expert', 'share' => '75%', 'desc' => '50+ simulasi, rating ≥ 4.5', 'color' => 'purple', 'bg' => 'bg-purple-50'],
                        ['tier' => 'Platinum', 'share' => '85%', 'desc' => '100+ simulasi, rating ≥ 4.7', 'color' => 'yellow', 'bg' => 'bg-amber-50'],
                    ] as $t)
                        <div class="p-6 text-center {{ $t['bg'] }}">
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">{{ $t['tier'] }}</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $t['share'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">kreator share</p>
                            <p class="text-xs text-gray-400 mt-2">{{ $t['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 bg-amber-50 border border-amber-200 rounded-xl p-4 flex gap-3">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-sm text-amber-800">
                    <strong>Catatan:</strong> Penghasilan kreator berasal dari bagi hasil iklan (ad revenue sharing), bukan langsung dari jumlah play. Besaran penghasilan bergantung pada jumlah impression iklan, CPM, kualitas simulasi, kategori, wilayah, serta kebijakan program yang berlaku. Minimum payout adalah Rp 500.000.
                </div>
            </div>
        </section>

        {{-- Siapa yang Bisa Menjadi Kreator? --}}
        <section class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Siapa yang Bisa Menjadi Kreator?</h2>
                <p class="text-gray-600 text-lg">Siapa pun yang ingin berbagi ilmu melalui simulasi interaktif.</p>
            </div>
            <div class="flex flex-wrap justify-center gap-3">
                @foreach(['Guru', 'Dosen', 'Mahasiswa', 'Pelajar', 'Programmer', 'Desainer', 'Peneliti', 'Komunitas STEM', 'Siapa pun yang ingin berbagi ilmu'] as $persona)
                    <span class="px-5 py-2.5 bg-white border border-gray-200 rounded-full text-sm font-medium text-gray-700 shadow-sm hover:shadow-md transition">
                        {{ $persona }}
                    </span>
                @endforeach
            </div>
        </section>

        {{-- Dashboard Kreator --}}
        <section class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Dashboard Kreator</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Setiap kreator memiliki dashboard lengkap untuk memantau dan mengelola simulasi mereka.</p>
            </div>
            @php
                $dashboardIcons = [
                    ['svg' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>', 'label' => 'Total Views'],
                    ['svg' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>', 'label' => 'Total Plays'],
                    ['svg' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>', 'label' => 'Total Likes'],
                    ['svg' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>', 'label' => 'Total Followers'],
                    ['svg' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>', 'label' => 'Total Bookmarks'],
                    ['svg' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>', 'label' => 'Total Shares'],
                    ['svg' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>', 'label' => 'Total Penghasilan'],
                    ['svg' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>', 'label' => 'Grafik Performa'],
                    ['svg' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>', 'label' => 'Simulasi Terpopuler'],
                    ['svg' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>', 'label' => 'Komentar Pengguna'],
                    ['svg' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>', 'label' => 'Revenue Analytics'],
                    ['svg' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>', 'label' => 'Creator Reputation'],
                ];
            @endphp
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($dashboardIcons as $item)
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
                        <span class="text-blue-500 shrink-0">{!! $item['svg'] !!}</span>
                        <span class="text-sm font-medium text-gray-700">{{ $item['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Mengapa Menjadi Kreator? --}}
        <section class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Mengapa Menjadi Kreator di Noteds?</h2>
            </div>
            @php
                $whyIcons = [
                    ['svg' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>', 'title' => 'Karyamu Dipelajari Banyak Orang', 'desc' => 'Simulasimu dapat diakses oleh jutaan pengguna dari seluruh Indonesia.'],
                    ['svg' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>', 'title' => 'Bangun Portofolio Edukasi', 'desc' => 'Kumpulkan karya simulasi terbaikmu dalam satu profil kreator yang profesional.'],
                    ['svg' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>', 'title' => 'Dapatkan Pengikut', 'desc' => 'Bangun komunitas pengikut yang setia mengikuti karya-karyamu.'],
                    ['svg' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>', 'title' => 'Berpeluang Memperoleh Penghasilan', 'desc' => 'Melalui program ad revenue sharing, karya terbaikmu dapat menghasilkan pendapatan.'],
                    ['svg' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>', 'title' => 'Berkontribusi untuk Pendidikan', 'desc' => 'Setiap simulasi yang kamu buat membantu siswa memahami konsep secara interaktif.'],
                    ['svg' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>', 'title' => 'Dapatkan Badge & Sertifikasi', 'desc' => 'Raih badge penghargaan dan sertifikasi kreator untuk meningkatkan reputasi dan tier revenue-mu.'],
                ];
            @endphp
            <div class="grid md:grid-cols-2 gap-6">
                @foreach($whyIcons as $item)
                    <div class="flex gap-4 items-start bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                        <span class="text-blue-500 shrink-0">{!! $item['svg'] !!}</span>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">{{ $item['title'] }}</h3>
                            <p class="text-sm text-gray-500">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Visi Jangka Panjang --}}
        <section class="mb-20">
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-8 md:p-12 text-white text-center">
                <h2 class="text-2xl md:text-3xl font-bold mb-6">Visi Jangka Panjang</h2>
                <p class="text-gray-300 text-lg max-w-3xl mx-auto mb-8 leading-relaxed">
                    Kami ingin membangun ekosistem simulasi edukasi terbesar, tempat jutaan pengguna belajar melalui simulasi interaktif dan ribuan kreator berbagi karya terbaik mereka.
                </p>
                <div class="grid md:grid-cols-3 gap-6 max-w-3xl mx-auto text-left">
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-5 border border-white/10">
                        <p class="text-gray-400 text-sm line-through mb-2">Bukan hanya menonton materi.</p>
                        <p class="text-gray-400 text-sm line-through mb-3">Bukan hanya membaca teori.</p>
                        <p class="text-white font-medium">Belajar dengan mencoba, mengeksplorasi, dan memahami konsep secara langsung melalui simulasi interaktif.</p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-5 border border-white/10">
                        <p class="text-purple-300 font-medium mb-2">Rumah bagi Kreator Simulasi</p>
                        <p class="text-gray-400 text-sm">Seperti YouTube menjadi rumah bagi kreator video, Noteds menjadi rumah bagi kreator simulasi edukasi.</p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-5 border border-white/10">
                        <p class="text-blue-300 font-medium mb-2">Pengalaman Belajar yang Berbeda</p>
                        <p class="text-gray-400 text-sm">Konten utama Noteds adalah pengalaman belajar yang interaktif, bukan sekadar tontonan.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- CTA Section --}}
        <section class="text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Siap Berkreasi?</h2>
            <p class="text-gray-600 text-lg mb-8">Mulai perjalananmu sebagai kreator simulasi edukasi hari ini.</p>
            <div class="flex flex-wrap justify-center gap-4">
                @auth
                    @if(auth()->user()->isCreator())
                        <a href="{{ route('studio.dashboard') }}" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 rounded-xl font-semibold text-white transition shadow-lg shadow-purple-500/25">
                            Buka Studio →
                        </a>
                    @else
                        <form action="{{ route('become-creator') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 rounded-xl font-semibold text-white transition shadow-lg shadow-purple-500/25">
                                Mulai Menjadi Kreator →
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 rounded-xl font-semibold text-white transition shadow-lg shadow-purple-500/25">
                        Daftar Gratis Sekarang →
                    </a>
                @endauth
            </div>
            <p class="text-gray-400 text-xs mt-6">
                <em>Buat Simulasi. Bagikan Ilmu. Bangun Masa Depan.</em>
            </p>
        </section>

    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-100 mt-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center text-sm text-gray-400">
            <p>© {{ date('Y') }} {{ config('app.name', 'Noteds') }}. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
