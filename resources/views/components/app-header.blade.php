{{-- Unified App Header — used across ALL pages --}}
@props(['showSearch' => false, 'searchTerm' => ''])

<nav x-data="{ mobileOpen: false }" class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
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
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">
                                Studio
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Center: Search Bar (optional) --}}
            @if($showSearch)
                <form action="{{ route('home') }}" method="GET" class="hidden md:flex flex-1 max-w-xl mx-8">
                    <div class="relative w-full">
                        <input
                            type="text"
                            name="search"
                            value="{{ $searchTerm }}"
                            placeholder="Cari simulasi..."
                            class="w-full pl-4 pr-12 py-2.5 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        />
                        <button type="submit" class="absolute right-1 top-1 bottom-1 px-4 bg-gray-100 hover:bg-gray-200 rounded-full transition">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </form>
            @endif

            {{-- Right: Bell + User --}}
            <div class="flex items-center gap-3">
                @auth
                    @include('components.notification-bell')
                    <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        {{ auth()->user()->name }}
                    </a>
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
        @if($showSearch)
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
        @endif
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="sm:hidden border-t border-gray-100" x-cloak>
        <div class="py-2 space-y-1 px-4">
            <a href="{{ route('home') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">Beranda</a>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">Studio</a>
                @endif
                <a href="{{ route('notifications.index') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">Notifikasi</a>
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition">{{ auth()->user()->name }}</a>
            @endauth
        </div>
    </div>
</nav>
