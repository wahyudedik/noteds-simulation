<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jelajahi - {{ config('app.name', 'Noteds') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .simulation-card:hover .thumbnail-overlay { opacity: 1; }
        .simulation-card:hover img { transform: scale(1.05); }
        .category-chip:hover { background-color: #2563eb; color: white; }
        .category-chip.active { background-color: #2563eb; color: white; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

    @include('components.app-header')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Beranda</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 font-medium">Explore</span>
        </nav>
    </div>

    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold text-gray-900">
                <svg class="inline w-8 h-8 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                Jelajahi Simulasi
            </h1>
            <p class="text-gray-500 text-sm mt-2">Temukan simulasi interaktif sesuai minat Anda.</p>

            {{-- Category Chips --}}
            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('simulations.explore') }}"
                    class="category-chip px-4 py-2 rounded-full text-sm font-medium transition duration-200 border {{ !$activeCategory ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:border-blue-600' }}">
                    Semua
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('simulations.explore', ['category' => $cat->category]) }}"
                        class="category-chip px-4 py-2 rounded-full text-sm font-medium transition duration-200 border {{ $activeCategory === $cat->category ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:border-blue-600' }}">
                        {{ $cat->category }}
                        <span class="text-xs opacity-70">({{ $cat->count }})</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Featured Section --}}
        @if($featured->count() > 0)
        <section class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">
                    <svg class="inline w-5 h-5 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    Paling Populer
                </h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($featured as $sim)
                    @include('components.simulation-card', ['simulation' => $sim])
                @endforeach
            </div>
        </section>
        @endif

        {{-- Trending Section --}}
        @if($trending->count() > 0)
        <section class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">
                    <svg class="inline w-5 h-5 text-orange-500 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M13 7.83l3.59 3.59L18 10l-6-6-6 6 1.41 1.41L11 7.83V20h2V7.83z"/></svg>
                    Trending
                </h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($trending as $sim)
                    @include('components.simulation-card', ['simulation' => $sim])
                @endforeach
            </div>
        </section>
        @endif

        {{-- Top Rated Section --}}
        @if($topRated->count() > 0)
        <section class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">
                    <svg class="inline w-5 h-5 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Rating Tertinggi
                </h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($topRated as $sim)
                    @include('components.simulation-card', ['simulation' => $sim])
                @endforeach
            </div>
        </section>
        @endif

        {{-- Recently Added Section --}}
        @if($recent->count() > 0)
        <section class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">
                    <svg class="inline w-5 h-5 text-blue-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                    Baru Ditambahkan
                </h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($recent as $sim)
                    @include('components.simulation-card', ['simulation' => $sim])
                @endforeach
            </div>
        </section>
        @endif

        {{-- Empty State --}}
        @if($featured->count() === 0 && $trending->count() === 0)
        <div class="text-center py-20">
            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <h3 class="text-xl font-semibold text-gray-700 mt-4 mb-2">Tidak ada simulasi ditemukan</h3>
            <p class="text-gray-500 mb-4">
                @if($activeCategory)
                    Belum ada simulasi di kategori "{{ $activeCategory }}". Coba kategori lain.
                @else
                    Belum ada simulasi yang tersedia. Sabar ya!
                @endif
            </p>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                Kembali ke Beranda
            </a>
        </div>
        @endif
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('logo.jpeg') }}" alt="Noteds" class="w-6 h-6 rounded object-cover" />
                    <span class="font-semibold text-gray-900">Noteds</span>
                </div>
                <p class="text-sm text-gray-500">
                    Interactive Simulations &copy; {{ date('Y') }}
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
