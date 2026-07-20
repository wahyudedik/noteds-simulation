<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Noteds') }} - {{ request()->routeIs('login') ? 'Masuk' : 'Daftar' }}</title>
        <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex">
            {{-- Left Panel: Branding --}}
            <div class="hidden lg:flex lg:w-1/2 xl:w-[55%] relative overflow-hidden"
                 style="background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 50%, #1e293b 100%);">
                {{-- Decorative elements --}}
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-20 left-20 w-72 h-72 bg-blue-400 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-32 right-16 w-96 h-96 bg-cyan-400 rounded-full blur-3xl"></div>
                    <div class="absolute top-1/2 left-1/3 w-48 h-48 bg-indigo-400 rounded-full blur-3xl"></div>
                </div>

                <div class="relative z-10 flex flex-col items-center justify-center w-full px-12">
                    {{-- Logo --}}
                    <div class="mb-8">
                        <img src="{{ asset('logo.jpeg') }}" alt="{{ config('app.name') }}" class="w-20 h-20 rounded-2xl object-cover shadow-2xl" />
                    </div>

                    {{-- Brand Name --}}
                    <h1 class="text-4xl font-bold text-white mb-4">Noteds</h1>

                    {{-- Tagline --}}
                    <p class="text-lg text-blue-200 text-center max-w-md leading-relaxed mb-12">
                        Simulasi Interaktif untuk Semua.
                        <span class="block text-blue-300">Belajar jadi lebih menyenangkan.</span>
                    </p>

                    {{-- Feature highlights --}}
                    <div class="space-y-4 max-w-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                            </div>
                            <span class="text-sm text-blue-100">Ratusan simulasi sains interaktif</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <span class="text-sm text-blue-100">Belajar mandiri kapan saja</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span class="text-sm text-blue-100">Komunitas pembelajar aktif</span>
                        </div>
                    </div>
                </div>

                {{-- Bottom copyright --}}
                <div class="absolute bottom-6 left-0 right-0 text-center">
                    <p class="text-xs text-blue-300/50">&copy; {{ date('Y') }} Noteds. All rights reserved.</p>
                </div>
            </div>

            {{-- Right Panel: Auth Form --}}
            <div class="w-full lg:w-1/2 xl:w-[45%] flex items-center justify-center p-6 sm:p-8 bg-gray-50">
                <div class="w-full max-w-md">
                    {{-- Mobile logo --}}
                    <div class="lg:hidden flex items-center justify-center mb-8">
                        <a href="/" class="flex items-center gap-3">
                            <img src="{{ asset('logo.jpeg') }}" alt="{{ config('app.name') }}" class="w-10 h-10 rounded-xl object-cover shadow-md" />
                            <span class="text-2xl font-bold text-gray-900">Noteds</span>
                        </a>
                    </div>

                    {{ $slot }}

                    {{-- Footer links --}}
                    <div class="mt-8 text-center">
                        <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
                            &larr; Kembali ke beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
