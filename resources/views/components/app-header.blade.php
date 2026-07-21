{{-- Unified App Header — used across ALL pages --}}
@props(['showSearch' => false, 'searchTerm' => ''])

<nav x-data="{ mobileOpen: false, userMenuOpen: false, searchQuery: '{{ $searchTerm }}', searchResults: [], searchLoading: false, searchOpen: false, searchTimeout: null }" class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">

            {{-- Left: Logo + Nav Links --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                    <img src="{{ asset('logo.jpeg') }}" alt="NotEDs" class="w-8 h-8 rounded-lg object-cover" />
                    <span class="text-xl font-bold text-gray-900">Noteds</span>
                </a>

                {{-- Desktop Nav Links --}}
                <div class="hidden sm:flex items-center gap-1">
                    <a href="{{ route('home') }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">
                        Beranda
                    </a>
                    <a href="{{ route('simulations.explore') }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">
                        Jelajahi
                    </a>
                    <a href="{{ route('leaderboard.index') }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">
                        #1
                    </a>
                    @auth
                        <a href="{{ route('collections.index') }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">
                            Collection
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">
                                Admin
                            </a>
                        @endif
                        @if(auth()->user()->isCreator() || auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                            <a href="{{ route('studio.dashboard') }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">
                                Studio
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Center: Search Bar --}}
            <div class="hidden md:flex flex-1 max-w-xl mx-8" @click.away="searchOpen = false">
                <form action="{{ route('home') }}" method="GET" class="relative w-full" @submit="searchOpen = false">
                    <input
                        type="text"
                        name="search"
                        x-model="searchQuery"
                        @input.debounce.300ms="if(searchQuery.length >= 2) { searchLoading = true; searchOpen = true; fetch('/api/search?q=' + encodeURIComponent(searchQuery)).then(r => r.json()).then(data => { searchResults = data.results || []; searchLoading = false; }).catch(() => { searchLoading = false; }); } else { searchResults = []; searchOpen = false; }"
                        @focus="if(searchQuery.length >= 2 && searchResults.length > 0) searchOpen = true"
                        value="{{ $searchTerm }}"
                        placeholder="Cari simulasi..."
                        class="w-full pl-4 pr-12 py-2.5 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        autocomplete="off"
                    />
                    <button type="submit" class="absolute right-1 top-1 bottom-1 px-4 bg-gray-100 hover:bg-gray-200 rounded-full transition">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                    {{-- Live Search Dropdown --}}
                    <div x-show="searchOpen" x-cloak class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50 max-h-80 overflow-y-auto">
                        <template x-if="searchLoading">
                            <div class="px-4 py-3 text-sm text-gray-400 flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" class="opacity-75"></path></svg>
                                Mencari...
                            </div>
                        </template>
                        <template x-if="!searchLoading && searchResults.length > 0">
                            <div>
                                <template x-for="result in searchResults" :key="result.id">
                                    <a :href="'/sim/' + result.slug" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                                        <div class="w-16 aspect-video bg-gray-200 rounded overflow-hidden flex-shrink-0">
                                            <template x-if="result.thumbnail">
                                                <img :src="result.thumbnail" class="w-full h-full object-cover" />
                                            </template>
                                            <template x-if="!result.thumbnail">
                                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600">
                                                    <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate" x-text="result.title"></p>
                                            <p class="text-xs text-gray-500" x-text="result.category + ' · ' + result.formatted_play_count + ' dimainkan'"></p>
                                        </div>
                                    </a>
                                </template>
                                <div class="border-t border-gray-100 mt-1 pt-1">
                                    <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-blue-600 hover:bg-blue-50 transition font-medium" x-text="'Lihat semua hasil untuk "' + searchQuery + '"'"></button>
                                </div>
                            </div>
                        </template>
                        <template x-if="!searchLoading && searchResults.length === 0 && searchQuery.length >= 2">
                            <div class="px-4 py-3 text-sm text-gray-400">Tidak ada hasil ditemukan.</div>
                        </template>
                    </div>
                </form>
            </div>

            {{-- Right: Bell + User --}}
            <div class="flex items-center gap-3">
                @auth
                    @include('components.notification-bell')

                    {{-- User Dropdown --}}
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition">
                            @if(auth()->user()->avatar)
                                <img src="{{ Storage::disk('public')->url(auth()->user()->avatar) }}" alt="" class="w-7 h-7 rounded-full object-cover" />
                            @else
                                <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-semibold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak
                            class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Dashboard
                            </a>
                            <a href="{{ route('user-profile.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profil Saya
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Pengaturan
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 transition">Masuk</a>
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">Daftar</a>
                    @endif
                @endauth

                {{-- Mobile Hamburger --}}
                <button @click="mobileOpen = !mobileOpen" class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': mobileOpen, 'inline-flex': !mobileOpen}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !mobileOpen, 'inline-flex': mobileOpen}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Search --}}
        <div class="md:hidden pb-3">
            <form action="{{ route('home') }}" method="GET">
                <input
                    type="text"
                    name="search"
                    value="{{ $searchTerm }}"
                    placeholder="Cari simulasi..."
                    class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                />
            </form>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="sm:hidden border-t border-gray-100" x-cloak>
        <div class="py-2 space-y-1 px-4">
            <a href="{{ route('home') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">Beranda</a>
            <a href="{{ route('simulations.explore') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">Jelajahi</a>
            <a href="{{ route('leaderboard.index') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">#1 Leaderboard</a>
            @auth
                <a href="{{ route('collections.index') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">Collection</a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">Admin Panel</a>
                @endif
                @if(auth()->user()->isCreator() || auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                    <a href="{{ route('studio.dashboard') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">Studio</a>
                @endif
                <a href="{{ route('notifications.index') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">Notifikasi</a>
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">Dashboard</a>
                <a href="{{ route('user-profile.index') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">Profil Saya</a>
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">Pengaturan</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">Masuk</a>
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="block px-3 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition text-center">Daftar</a>
                @endif
            @endauth
        </div>
    </div>
</nav>
