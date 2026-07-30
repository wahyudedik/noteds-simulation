<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Jelajahi Experience</h2>
        </div>
    </x-slot>

    <div x-data="{ loading: false }">
    <style>
        .simulation-card:hover .thumbnail-overlay { opacity: 1; }
        .simulation-card:hover img { transform: scale(1.05); }
        .category-chip:hover { background-color: #2563eb; color: white; }
        .category-chip.active { background-color: #2563eb; color: white; }
        /* Touch-friendly: ensure active state works on mobile tap */
        .simulation-card:active .thumbnail-overlay { opacity: 1; }
        @media (hover: none) {
            .simulation-card .thumbnail-overlay { opacity: 0.7; }
            .simulation-card:active img { transform: scale(1.05); }
        }
    </style>

    {{-- Loading Skeleton Overlay --}}
    <div x-show="loading" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak class="fixed inset-0 z-50 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pt-20">
            <div class="space-y-8">
                @foreach(['Paling Populer', 'Trending', 'Rating Tertinggi', 'Baru Ditambahkan'] as $section)
                    <div>
                        <div class="h-6 w-40 bg-gray-200 dark:bg-gray-700 rounded mb-4 animate-pulse"></div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                            @foreach(range(1, 4) as $i)
                                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700">
                                    <div class="aspect-video bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                                    <div class="p-4 space-y-3">
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-3/4"></div>
                                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-full"></div>
                                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-1/2"></div>
                                        <div class="flex items-center gap-2 pt-2">
                                            <div class="w-6 h-6 bg-gray-200 dark:bg-gray-700 rounded-full animate-pulse"></div>
                                            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-20"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[['label' => 'Explore']]" />

            {{-- Page Header --}}
            <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-8 mb-8 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-2">Temukan experience interaktif sesuai minat Anda.</p>

                {{-- Category Chips --}}
                <div class="mt-6 flex flex-wrap gap-2">
                    <a href="{{ route('simulations.explore') }}" @click="loading = true"
                        class="category-chip px-4 py-2 rounded-full text-sm font-medium transition duration-200 border {{ !$activeCategory ? 'bg-blue-600 text-white border-blue-600' : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:border-blue-600' }}">
                        Semua
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('simulations.explore', ['category' => $cat->category]) }}" @click="loading = true"
                            class="category-chip px-4 py-2 rounded-full text-sm font-medium transition duration-200 border {{ $activeCategory === $cat->category ? 'bg-blue-600 text-white border-blue-600' : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:border-blue-600' }}">
                            {{ $cat->category }}
                            <span class="text-xs opacity-70">({{ $cat->count }})</span>
                        </a>
                    @endforeach
                </div>

                {{-- Tag Chips --}}
                @if($tags->count() > 0)
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="text-xs font-medium text-gray-400 self-center mr-1">Tag:</span>
                    @foreach($tags as $tag)
                        <a href="{{ route('simulations.explore', array_merge(request()->query(), ['tag' => $tag->slug])) }}" @click="loading = true"
                            class="px-3 py-1 rounded-full text-xs font-medium transition duration-200 border {{ ($activeTag ?? '') === $tag->slug ? 'bg-purple-600 text-white border-purple-600' : 'bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-gray-600 hover:border-purple-400 hover:text-purple-600' }}">
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Featured Section --}}
            @if($featured->count() > 0)
            <section class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
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
            <section class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        <svg class="inline w-5 h-5 text-orange-500 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M13 7.83l3.59 3.59L18 10l-6-6-6 6 1.41 1.41L11 7.83V20h2V7.83z"/></svg>
                        Trending
                    </h2>
                    <div class="flex items-center gap-1">
                        @foreach($trendingPeriods as $key => $label)
                            <a href="{{ route('simulations.explore', array_merge(request()->query(), ['period' => $key])) }}" @click="loading = true"
                                class="px-3 py-1.5 text-xs font-medium rounded-lg transition {{ $trendingPeriod === $key ? 'bg-orange-500 text-white dark:bg-orange-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @if($trending->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                        @foreach($trending as $sim)
                            @include('components.simulation-card', ['simulation' => $sim])
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada experience trending untuk periode ini.</p>
                    </div>
                @endif
            </section>

            {{-- Top Rated Section --}}
            @if($topRated->count() > 0)
            <section class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
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

            {{-- For You Section --}}
            @if($forYou->count() > 0)
            <section class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        <svg class="inline w-5 h-5 text-purple-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                        Untuk Anda
                    </h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($forYou as $sim)
                        @include('components.simulation-card', ['simulation' => $sim])
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Recently Added Section --}}
            @if($recent->count() > 0)
            <section class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
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
                <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mt-4 mb-2">Tidak ada experience ditemukan</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    @if($activeCategory)
                        Belum ada experience di kategori "{{ $activeCategory }}". Coba kategori lain.
                    @else
                        Belum ada experience yang tersedia. Sabar ya!
                    @endif
                </p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    Kembali ke Beranda
                </a>
            </div>
            @endif
        </div>
    </div>
    </div>
</x-app-layout>
