<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $seo['url'] }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="{{ $seo['type'] }}">
    <meta property="og:url" content="{{ $seo['url'] }}">
    <meta property="og:title" content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:image" content="{{ $seo['image'] }}">
    <meta property="og:site_name" content="{{ $seo['site_name'] }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    <meta name="twitter:image" content="{{ $seo['image'] }}">

    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .simulation-card:hover .thumbnail-overlay { opacity: 1; }
        .simulation-card:hover img { transform: scale(1.05); }
        .category-pill:hover { background-color: #2563eb; color: white; }
        .hero-gradient {
            background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 50%, #1e293b 100%);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:top-2 focus:left-2 focus:bg-blue-600 focus:text-white focus:px-4 focus:py-2 focus:rounded-lg focus:font-medium focus:shadow-lg">
        Lewati ke konten utama
    </a>

    @include('components.app-header', ['showSearch' => true, 'searchTerm' => $search ?? ''])

    {{-- Hero Section (only when no search) --}}
    @if(! $search)
    <div class="hero-gradient text-white py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl md:text-4xl font-bold mb-3">
                <svg class="inline w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                <span class="text-blue-300">Simulasi Interaktif</span> untuk Semua
            </h1>
            <p class="text-gray-300 text-lg max-w-2xl mx-auto mb-6">
                Jelajahi ratusan simulasi sains interaktif. Belajar jadi lebih menyenangkan.
            </p>
            <div class="flex flex-wrap justify-center gap-2">
                @foreach($categories as $cat)
                    <a href="{{ route('simulations.category', $cat->category) }}" class="category-pill px-4 py-2 bg-white/10 hover:bg-blue-600 rounded-full text-sm font-medium transition duration-200 backdrop-blur-sm">
                        {{ $cat->category }} <span class="text-xs opacity-70">({{ $cat->count }})</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Main Content --}}
    <main id="main-content" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Search Results --}}
        @if($search && $searchResults)
            <section class="mb-10">
                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    Hasil pencarian untuk "<span class="text-blue-600">{{ $search }}</span>"
                </h2>
                @if($searchResults->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                        @foreach($searchResults as $sim)
                            @include('components.simulation-card', ['simulation' => $sim])
                        @endforeach
                    </div>
                    <div class="mt-6">
                        {{ $searchResults->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-16">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <h3 class="text-lg font-semibold text-gray-600 mb-1">Tidak ada hasil ditemukan</h3>
                        <p class="text-sm text-gray-400 mb-4">Tidak ada simulasi yang cocok dengan "<span class="font-medium text-gray-500">{{ $search }}</span>"</p>
                        <p class="text-xs text-gray-400">Coba kata kunci lain atau jelajahi kategori di bawah.</p>
                    </div>
                @endif
            </section>
        @else
            <div x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 500)">
                {{-- Skeleton Loading --}}
                <template x-if="loading">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-6 bg-gray-200 rounded w-48 animate-pulse"></div>
                            <div class="h-8 bg-gray-200 rounded w-64 animate-pulse"></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                            @for($i = 0; $i < 8; $i++)
                                <div class="bg-white rounded-xl overflow-hidden shadow-sm animate-pulse">
                                    <div class="aspect-video bg-gray-200"></div>
                                    <div class="p-3 space-y-2">
                                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </template>

                {{-- Actual Content --}}
                <template x-if="!loading">
                    <div>
                        {{-- Trending --}}
                        <section class="mb-10">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-gray-900">
                                    <svg class="inline w-5 h-5 text-orange-500 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M13 7.83l3.59 3.59L18 10l-6-6-6 6 1.41 1.41L11 7.83V20h2V7.83z"/></svg>
                                    Trending Simulations
                                </h2>
                                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                                    @php $currentPeriod = request('period', 'week'); @endphp
                                    <a href="?period=day" class="px-3 py-1 text-xs font-medium rounded-md transition {{ $currentPeriod === 'day' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Hari Ini</a>
                                    <a href="?period=week" class="px-3 py-1 text-xs font-medium rounded-md transition {{ $currentPeriod === 'week' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Minggu Ini</a>
                                    <a href="?period=month" class="px-3 py-1 text-xs font-medium rounded-md transition {{ $currentPeriod === 'month' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Bulan Ini</a>
                                    <a href="?period=year" class="px-3 py-1 text-xs font-medium rounded-md transition {{ $currentPeriod === 'year' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Tahun Ini</a>
                                </div>
                            </div>
                            @if($trending->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                                @foreach($trending as $sim)
                                    @include('components.simulation-card', ['simulation' => $sim])
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-8">
                                <p class="text-gray-400 text-sm">Tidak ada simulasi trending untuk periode ini.</p>
                            </div>
                            @endif
                        </section>

            {{-- Latest --}}
            @if($latest->count() > 0)
            <section class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">
                        <svg class="inline w-5 h-5 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        Simulasi Terbaru
                    </h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($latest as $sim)
                        @include('components.simulation-card', ['simulation' => $sim])
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Discovered for You --}}
            @if($discovered->count() > 0)
            <section class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">
                        <svg class="inline w-5 h-5 text-purple-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        Discovered for You
                    </h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($discovered as $sim)
                        @include('components.simulation-card', ['simulation' => $sim])
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Popular --}}
            @if($popular->count() > 0)
            <section class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">
                        <svg class="inline w-5 h-5 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        Paling Banyak Dimainkan
                    </h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($popular as $sim)
                        @include('components.simulation-card', ['simulation' => $sim])
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Empty State --}}
            @if($trending->count() === 0 && $latest->count() === 0)
            <div class="text-center py-20">
                <div class="mb-4">
                    <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum ada simulasi</h3>
                <p class="text-gray-500">Simulasi interaktif akan segera tersedia. Sabar ya!</p>
            </div>
            @endif
                    </div>
                </template>
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

    {{-- Back to Top Button --}}
    <div x-data="{ show: false }" x-init="window.addEventListener('scroll', () => { show = window.scrollY > 300 })"
         x-show="show" x-transition
         class="fixed bottom-6 right-6 z-50">
        <button @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                class="w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg flex items-center justify-center transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
        </button>
    </div>
</body>
</html>
