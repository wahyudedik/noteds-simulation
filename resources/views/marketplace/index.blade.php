<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" /></svg>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Marketplace</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Home', 'url' => route('home')],
                ['label' => 'Marketplace'],
            ]" />

            {{-- Hero Section --}}
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl shadow-lg -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-10 mb-8 text-white">
                <h1 class="text-3xl font-bold mb-2">Marketplace</h1>
                <p class="text-emerald-100 text-lg mb-6">Temukan simulasi premium dari kreator terbaik.</p>

                {{-- Search Bar --}}
                <form action="{{ route('marketplace.index') }}" method="GET" class="max-w-2xl">
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search ?? '' }}"
                            placeholder="Cari simulasi premium..."
                            class="w-full pl-12 pr-4 py-3 rounded-xl text-gray-900 bg-white/95 backdrop-blur border-0 shadow-lg focus:ring-2 focus:ring-emerald-300 text-sm"
                        />
                        <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        <button type="submit" class="absolute right-2 top-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition">
                            Cari
                        </button>
                    </div>
                    {{-- Preserve hidden filters --}}
                    @if($activeCategory ?? null)
                        <input type="hidden" name="category" value="{{ $activeCategory }}">
                    @endif
                    @if($activeLicense ?? null)
                        <input type="hidden" name="license" value="{{ $activeLicense }}">
                    @endif
                    @if($sort ?? null)
                        <input type="hidden" name="sort" value="{{ $sort }}">
                    @endif
                </form>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                {{-- Sidebar Filters --}}
                <aside class="w-full lg:w-64 flex-shrink-0">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 sticky top-24">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                            Filter
                        </h3>

                        {{-- Category Filter --}}
                        <div class="mb-5">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Kategori</h4>
                            <div class="space-y-1">
                                <a href="{{ route('marketplace.index', array_merge(request()->query(), ['category' => ''])) }}"
                                    class="block px-3 py-1.5 text-sm rounded-lg transition {{ !($activeCategory ?? null) ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                    Semua Kategori
                                </a>
                                @foreach($categories as $cat)
                                    <a href="{{ route('marketplace.index', array_merge(request()->query(), ['category' => $cat->category])) }}"
                                        class="block px-3 py-1.5 text-sm rounded-lg transition {{ ($activeCategory ?? null) === $cat->category ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                        {{ $cat->category }}
                                        <span class="text-xs opacity-60">({{ $cat->count }})</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- License Type Filter --}}
                        <div class="mb-5">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Tipe Lisensi</h4>
                            <div class="space-y-1">
                                <a href="{{ route('marketplace.index', array_merge(request()->query(), ['license' => ''])) }}"
                                    class="block px-3 py-1.5 text-sm rounded-lg transition {{ !($activeLicense ?? null) ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                    Semua Lisensi
                                </a>
                                @foreach($licenseTypes as $key => $label)
                                    <a href="{{ route('marketplace.index', array_merge(request()->query(), ['license' => $key])) }}"
                                        class="block px-3 py-1.5 text-sm rounded-lg transition {{ ($activeLicense ?? null) === $key ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                        {{ $label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Sort --}}
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Urutkan</h4>
                            <div class="space-y-1">
                                @php
                                    $sortOptions = [
                                        'newest' => 'Terbaru',
                                        'popular' => 'Terpopuler',
                                        'rating' => 'Rating Tertinggi',
                                        'price_low' => 'Harga Terendah',
                                        'price_high' => 'Harga Tertinggi',
                                        'sales' => 'Terlaris',
                                    ];
                                @endphp
                                @foreach($sortOptions as $key => $label)
                                    <a href="{{ route('marketplace.index', array_merge(request()->query(), ['sort' => $key])) }}"
                                        class="block px-3 py-1.5 text-sm rounded-lg transition {{ ($sort ?? 'newest') === $key ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                        {{ $label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </aside>

                {{-- Main Content --}}
                <main class="flex-1 min-w-0">
                    {{-- Results Header --}}
                    <div class="flex items-center justify-between mb-5">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Menampilkan <span class="font-medium text-gray-700 dark:text-gray-300">{{ $listings->total() }}</span> simulasi premium
                        </p>

                        {{-- Mobile sort --}}
                        <div class="lg:hidden">
                            <select onchange="window.location.href=this.value" class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                @foreach($sortOptions as $key => $label)
                                    <option value="{{ route('marketplace.index', array_merge(request()->query(), ['sort' => $key])) }}" {{ ($sort ?? 'newest') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Listings Grid --}}
                    @if($listings->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                            @foreach($listings as $listing)
                                @include('components.marketplace-card', ['listing' => $listing])
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-8">
                            {{ $listings->links() }}
                        </div>
                    @else
                        {{-- Empty State --}}
                        <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
                            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" /></svg>
                            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mt-4 mb-2">Tidak ada simulasi ditemukan</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">
                                @if($search ?? null)
                                    Tidak ada hasil untuk "{{ $search }}". Coba kata kunci lain.
                                @elseif($activeCategory ?? null)
                                    Belum ada simulasi premium di kategori "{{ $activeCategory }}". Coba kategori lain.
                                @else
                                    Belum ada simulasi premium yang tersedia di marketplace.
                                @endif
                            </p>
                            <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">
                                Lihat Semua
                            </a>
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </div>
</x-app-layout>
