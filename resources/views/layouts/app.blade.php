<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- SEO Meta Tags --}}
        <title>{{ $seo['title'] }}</title>
        <meta name="description" content="{{ $seo['description'] }}">
        @if($seo['meta_keywords'] ?? null)
            <meta name="keywords" content="{{ $seo['meta_keywords'] }}">
        @endif
        <meta name="robots" content="{{ $robots ?? 'index, follow' }}">
        <link rel="canonical" href="{{ $seo['url'] }}">

        {{-- Open Graph / Facebook --}}
        <meta property="og:type" content="{{ $seo['type'] }}">
        <meta property="og:url" content="{{ $seo['url'] }}">
        <meta property="og:title" content="{{ $seo['og_title'] ?? $seo['title'] }}">
        <meta property="og:description" content="{{ $seo['og_description'] ?? $seo['description'] }}">
        <meta property="og:image" content="{{ $seo['og_image'] ?? $seo['image'] }}">
        <meta property="og:site_name" content="{{ $seo['site_name'] }}">

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seo['og_title'] ?? $seo['title'] }}">
        <meta name="twitter:description" content="{{ $seo['og_description'] ?? $seo['description'] }}">
        <meta name="twitter:image" content="{{ $seo['og_image'] ?? $seo['image'] }}">

        {{-- Structured Data (Schema.org JSON-LD) --}}
        @if($seo['structured_data'] ?? null)
            <script type="application/ld+json">{!! json_encode($seo['structured_data'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endif

        <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Global Ad Network Scripts (only enabled networks) --}}
        @php
            $adNetworkScripts = \App\Models\AdNetworkSetting::getEnabledScriptTags();
        @endphp
        @foreach($adNetworkScripts as $network => $script)
            {!! $script !!}
        @endforeach
    </head>
    <body class="font-sans antialiased dark:bg-gray-900">
        <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:top-2 focus:left-2 focus:bg-blue-600 focus:text-white focus:px-4 focus:py-2 focus:rounded-lg focus:font-medium focus:shadow-lg">
            Lewati ke konten utama
        </a>
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 shadow-sm">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main id="main-content">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <x-app-footer />
        </div>

        <x-toast />

        <x-whatsapp-contact />

        {{-- Back to Top Button --}}
        <div x-data="{ show: false }" x-init="window.addEventListener('scroll', () => { show = window.scrollY > 300 })"
             x-show="show" x-transition
             class="fixed bottom-6 right-6 z-50">
            <button @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                    class="w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg flex items-center justify-center transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
            </button>
        </div>

        @stack('scripts')
    </body>
</html>
