<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $pageTitle ?? 'Studio' }} — {{ config('app.name', 'Noteds') }}</title>
        <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900" x-data="{ mobileSidebar: false }">
        <div class="min-h-screen flex">

            {{-- Sidebar --}}
            <aside class="hidden lg:flex lg:flex-col w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 fixed inset-y-0 left-0 z-40">
                {{-- Logo --}}
                <div class="flex items-center gap-2 px-6 h-16 border-b border-gray-100 dark:border-gray-700 shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <img src="{{ asset('logo.jpeg') }}" alt="NotEDs" class="w-8 h-8 rounded-lg object-cover" />
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Noteds</span>
                    </a>
                    <span class="ml-1 px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400 rounded-full">Studio</span>
                </div>

                {{-- Nav Links --}}
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    @php
                        $navItems = [
                            ['route' => 'studio.dashboard', 'label' => 'Dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />'],
                            ['route' => 'studio.simulations', 'label' => 'Experience', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />'],
                            ['route' => 'studio.comments', 'label' => 'Komentar', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />'],
                            ['route' => 'studio.followers', 'label' => 'Followers', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />'],
                            ['route' => 'studio.ads-revenue', 'label' => 'Revenue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'],
                            ['route' => 'studio.affiliate', 'label' => 'Afiliasi', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />'],
                            ['route' => 'studio.payouts', 'label' => 'Payouts', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />'],
                            ['route' => 'studio.payment-settings', 'label' => 'Pembayaran', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />'],
                            ['route' => 'studio.settings', 'label' => 'Pengaturan', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />'],
                        ];
                    @endphp

                    @foreach ($navItems as $item)
                        @php
                            $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*');
                        @endphp
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $isActive ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $item['icon'] !!}
                            </svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                {{-- Back to Site --}}
                <div class="px-3 py-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Situs
                    </a>
                </div>
            </aside>

            {{-- Mobile Header --}}
            <div class="lg:hidden fixed top-0 inset-x-0 z-50 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between h-14 px-4">
                    <div class="flex items-center gap-3">
                        <button @click="mobileSidebar = !mobileSidebar" class="p-1.5 text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 rounded-lg" aria-label="Toggle menu">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <a href="{{ route('home') }}" class="flex items-center gap-2">
                            <img src="{{ asset('logo.jpeg') }}" alt="NotEDs" class="w-7 h-7 rounded-lg object-cover" />
                            <span class="font-bold text-gray-900 dark:text-white">Studio</span>
                        </a>
                    </div>
                    <a href="{{ route('home') }}" class="text-sm text-blue-600 font-medium">Situs</a>
                </div>
            </div>

            {{-- Mobile Sidebar Overlay --}}
            <div x-show="mobileSidebar" x-transition:enter="transition-opacity duration-200" x-transition:leave="transition-opacity duration-200"
                 @click="mobileSidebar = false"
                 class="lg:hidden fixed inset-0 z-50 bg-black/50" x-cloak></div>

            {{-- Mobile Sidebar Drawer --}}
            <aside x-show="mobileSidebar" x-transition:enter="transition-transform duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                   x-transition:leave="transition-transform duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                   class="lg:hidden fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 shadow-xl overflow-y-auto" x-cloak>
                <div class="flex items-center gap-2 px-6 h-14 border-b border-gray-100 dark:border-gray-700 shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <img src="{{ asset('logo.jpeg') }}" alt="NotEDs" class="w-7 h-7 rounded-lg object-cover" />
                        <span class="font-bold text-gray-900 dark:text-white">Noteds</span>
                    </a>
                    <span class="px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400 rounded-full">Studio</span>
                </div>
                <nav class="px-3 py-4 space-y-1">
                    @foreach ($navItems as $item)
                        @php
                            $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*');
                        @endphp
                        <a href="{{ route($item['route']) }}" @click="mobileSidebar = false"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $isActive ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $item['icon'] !!}
                            </svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
                <div class="px-3 py-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Situs
                    </a>
                </div>
            </aside>

            {{-- Main Content --}}
            <div class="flex-1 lg:ml-64 pt-14 lg:pt-0">
                {{-- Desktop Header --}}
                <header class="hidden lg:block bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 sticky top-0 z-30">
                    <div class="flex items-center justify-between h-16 px-8">
                        <h1 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $pageTitle ?? 'Studio' }}</h1>
                        <div class="flex items-center gap-4">
                            @if(Auth::user()->isCreator() || Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
                                <a href="{{ route('studio.simulations.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Upload Baru
                                </a>
                            @endif
                            <div class="flex items-center gap-2">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Storage::disk('public')->url(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover" />
                                @else
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400 flex items-center justify-center text-xs font-semibold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                                @endif
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span>
                            </div>
                        </div>
                    </div>
                </header>

                {{-- Page Content --}}
                <main class="p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <x-toast />

        @stack('scripts')
    </body>
</html>
