<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Jelajahi Simulasi</h2>
        </div>
    </x-slot>

    <style>
        .simulation-card:hover .thumbnail-overlay { opacity: 1; }
        .simulation-card:hover img { transform: scale(1.05); }
        .category-chip:hover { background-color: #2563eb; color: white; }
        .category-chip.active { background-color: #2563eb; color: white; }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[['label' => 'Explore']]" />

            {{-- Page Header --}}
            <div class="bg-white border-b border-gray-200 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-8 mb-8 rounded-xl shadow-sm border border-gray-100">
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
            <section class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">
                        <svg class="inline w-5 h-5 text-orange-500 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M13 7.83l3.59 3.59L18 10l-6-6-6 6 1.41 1.41L11 7.83V20h2V7.83z"/></svg>
                        Trending
                    </h2>
                    <div class="flex items-center gap-1">
                        @foreach($trendingPeriods as $key => $label)
                            <a href="{{ route('simulations.explore', array_merge(request()->query(), ['trending' => $key])) }}"
                                class="px-3 py-1.5 text-xs font-medium rounded-lg transition {{ $trendingPeriod === $key ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
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
                    <div class="text-center py-8 bg-gray-50 rounded-xl border border-gray-100">
                        <p class="text-sm text-gray-500">Belum ada simulasi trending untuk periode ini.</p>
                    </div>
                @endif
            </section>

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
        </div>
    </div>
</x-app-layout>
