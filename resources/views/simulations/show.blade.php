<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ Str::limit(strip_tags($simulation->description ?? $simulation->title), 160) }}">
    <meta property="og:title" content="{{ $simulation->title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($simulation->description ?? $simulation->title), 200) }}">
    <meta property="og:type" content="website">
    @if($simulation->thumbnail)
        <meta property="og:image" content="{{ Storage::disk('public')->url($simulation->thumbnail) }}">
    @endif
    <title>{{ $simulation->title }} - {{ config('app.name') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .player-sticky-active {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            max-width: 900px;
            margin: 0 auto;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.6);
            border-radius: 0 0 0.75rem 0.75rem;
            transition: all 0.3s ease;
        }
        .player-sticky-active #player-iframe-container,
        .player-sticky-active #player-poster {
            border-radius: 0;
        }
        .player-fullscreen {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            max-width: none !important;
            margin: 0 !important;
            z-index: 9999 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }
        .player-fullscreen #player-iframe-container,
        .player-fullscreen #player-poster,
        .player-fullscreen #player-iframe-container iframe {
            border-radius: 0;
            width: 100%;
            height: 100%;
        }
        .player-fullscreen .player-controls {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 10000;
        }
        body.fullscreen-mode {
            overflow: hidden;
        }
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f3f4f6;
        }
        ::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        * {
            scrollbar-width: thin;
            scrollbar-color: #d1d5db #f3f4f6;
        }
        .reaction-btn.active { background-color: #2563eb; color: white; }
        .bookmark-btn.active { color: #facc15; }
        .rating-star { cursor: pointer; transition: color 0.15s; }
        .rating-star:hover, .rating-star.active { color: #facc15; }
        .comment-reply { display: none; }
        .comment-reply.show { display: block; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

    @include('components.app-header')

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Beranda</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('simulations.category', $simulation->category) }}" class="hover:text-blue-600 transition">{{ $simulation->category }}</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 font-medium">{{ $simulation->title }}</span>
        </nav>
        <div class="flex flex-col lg:flex-row gap-6">

            {{-- Left: Player & Info --}}
            <div class="flex-1 min-w-0">
                {{-- Player Wrapper --}}
                <div id="player-wrapper">
                    {{-- Thumbnail / Poster --}}
                    <div id="player-poster" class="bg-black rounded-xl overflow-hidden aspect-video relative">
                        @if($simulation->thumbnail)
                            <img
                                src="{{ Storage::disk('public')->url($simulation->thumbnail) }}"
                                alt="{{ $simulation->title }}"
                                class="w-full h-full object-cover"
                            />
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-600 to-purple-700">
                                <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                            </div>
                        @endif

                        <div id="play-overlay" class="absolute inset-0 flex items-center justify-center bg-black/30 cursor-pointer" onclick="playSimulation()">
                            <div class="w-20 h-20 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-2xl transition duration-200 hover:scale-110">
                                <svg class="w-8 h-8 text-blue-600 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Simulation Player --}}
                    <div id="player-iframe-container" class="hidden bg-black overflow-hidden aspect-video relative">
                        <iframe id="simulation-iframe" class="w-full h-full border-0" src="" allowfullscreen></iframe>
                    </div>

                    {{-- Player Control Bar --}}
                    <div id="player-controls" class="hidden bg-gray-800 border-t border-gray-700 px-3 py-2 flex items-center justify-between rounded-b-xl">
                        <div class="flex items-center gap-2">
                            <button onclick="closeSimulation()" class="p-1.5 text-gray-300 hover:text-white rounded-lg hover:bg-gray-700 transition" title="Tutup simulasi">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                            <button onclick="reloadSimulation()" class="p-1.5 text-gray-300 hover:text-white rounded-lg hover:bg-gray-700 transition" title="Muat ulang">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            </button>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="text-xs text-gray-300 mr-2 hidden sm:inline">{{ $simulation->title }}</span>
                            <button onclick="toggleFullscreen()" id="btn-fullscreen" class="p-1.5 text-gray-300 hover:text-white rounded-lg hover:bg-gray-700 transition" title="Layar penuh">
                                <svg id="icon-fullscreen-enter" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" /></svg>
                                <svg id="icon-fullscreen-exit" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25" /></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Simulation Info --}}
                <div class="mt-4">
                    <h1 class="text-xl font-bold text-gray-900">{{ $simulation->title }}</h1>

                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                        <span>{{ $simulation->formatted_view_count }} dilihat</span>
                        <span>&middot;</span>
                        <span>{{ $simulation->formatted_play_count }} dimainkan</span>
                        <span>&middot;</span>
                        <span>{{ $simulation->time_ago }}</span>
                    </div>

                    {{-- Rating Display --}}
                    <div class="flex items-center gap-2 mt-2">
                        @php $avgRating = $simulation->ratings()->avg('rating'); @endphp
                        @if($avgRating)
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= round($avgRating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                @endfor
                                <span class="text-sm text-gray-500 ml-1">{{ number_format($avgRating, 1) }}</span>
                                <span class="text-xs text-gray-400">({{ $simulation->ratings()->count() }})</span>
                            </div>
                        @endif
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex items-center gap-2 mt-4 flex-wrap">
                        {{-- Bookmark Button --}}
                        @auth
                            <button
                                id="bookmark-btn"
                                onclick="toggleBookmark()"
                                class="bookmark-btn flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-full transition {{ $isBookmarked ? 'active' : '' }}"
                            >
                                <svg class="w-4 h-4" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                                <span id="bookmark-text">{{ $isBookmarked ? 'Tersimpan' : 'Bookmark' }}</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-full transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                                Bookmark
                            </a>
                        @endauth

                        {{-- Favorite Button --}}
                        @auth
                            <button
                                id="favorite-btn"
                                onclick="toggleFavorite()"
                                class="flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-full transition {{ $isFavorited ? 'active text-red-500' : '' }}"
                            >
                                <svg class="w-4 h-4" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                <span id="favorite-text">{{ $isFavorited ? 'Favorit' : 'Favorit' }}</span>
                                <span id="favorite-count" class="text-gray-400">({{ $favoriteCount }})</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-full transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                Favorit
                            </a>
                        @endauth

                        {{-- Add to Collection Dropdown --}}
                        @auth
                            <div class="relative" x-data="{ collectionOpen: false }" @click.away="collectionOpen = false">
                                <button @click="collectionOpen = !collectionOpen" class="flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-full transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    <span>Collection</span>
                                </button>
                                <div x-show="collectionOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak
                                    class="absolute left-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                                    <div class="px-4 py-2.5 border-b border-gray-100">
                                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tambahkan ke Collection</p>
                                    </div>
                                    @forelse($userCollections as $collection)
                                        <button
                                            @click="addToCollection({{ $collection->id }})"
                                            class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition text-left"
                                        >
                                            <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                            <span class="truncate">{{ $collection->title }}</span>
                                            <span class="text-xs text-gray-400 ml-auto shrink-0">{{ $collection->simulations_count }}</span>
                                        </button>
                                    @empty
                                        <div class="px-4 py-3 text-sm text-gray-400 text-center">
                                            Belum ada collection
                                        </div>
                                    @endforelse
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <a href="{{ route('collections.create') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-blue-600 hover:bg-blue-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Buat Collection Baru
                                    </a>
                                </div>
                            </div>
                        @endauth

                        {{-- Share Dropdown --}}
                        <div class="relative" x-data="{ shareOpen: false }" @click.away="shareOpen = false">
                            <button @click="shareOpen = !shareOpen" class="flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-full transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                                Share
                            </button>
                            <div x-show="shareOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak
                                class="absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                                <a href="https://wa.me/?text={{ urlencode($simulation->title . ' - ' . route('simulations.show', $simulation->slug)) }}" target="_blank" rel="noopener noreferrer" onclick="trackShare('whatsapp')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    WhatsApp
                                </a>
                                <a href="https://twitter.com/intent/tweet?text={{ urlencode($simulation->title) }}&url={{ urlencode(route('simulations.show', $simulation->slug)) }}" target="_blank" rel="noopener noreferrer" onclick="trackShare('twitter')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                    Twitter / X
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('simulations.show', $simulation->slug)) }}" target="_blank" rel="noopener noreferrer" onclick="trackShare('facebook')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    Facebook
                                </a>
                                <a href="https://t.me/share/url?url={{ urlencode(route('simulations.show', $simulation->slug)) }}&text={{ urlencode($simulation->title) }}" target="_blank" rel="noopener noreferrer" onclick="trackShare('telegram')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                                    Telegram
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <button onclick="copyLink(); trackShare('copy_link'); shareOpen = false;" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition text-left">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                                    Salin Tautan
                                </button>
                                @auth
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="{{ route('embed.code', $simulation->slug) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                    Kode Embed
                                </a>
                                @endauth
                            </div>
                        </div>
                    </div>

                    {{-- Report Button --}}
                    @auth
                    <div class="mt-3" x-data="{ reportOpen: false }">
                        <button @click="reportOpen = !reportOpen" class="flex items-center gap-1.5 px-3 py-1.5 text-xs text-gray-400 hover:text-red-500 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                            Laporkan
                        </button>
                        <div x-show="reportOpen" x-transition x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="reportOpen = false">
                            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Laporkan Simulasi</h3>
                                <p class="text-sm text-gray-500 mb-4">Pilih alasan pelaporan untuk simulasi ini.</p>
                                <form method="POST" action="{{ route('reports.store', $simulation->slug) }}" id="report-form">
                                    @csrf
                                    <div class="space-y-2 mb-4">
                                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl hover:border-red-300 cursor-pointer transition">
                                            <input type="radio" name="reason" value="malware" class="text-red-500 focus:ring-red-500">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">Malware / Kode Berbahaya</div>
                                                <div class="text-xs text-gray-500">Mengandung virus, malware, atau kode berbahaya</div>
                                            </div>
                                        </label>
                                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl hover:border-red-300 cursor-pointer transition">
                                            <input type="radio" name="reason" value="spam_ads" class="text-red-500 focus:ring-red-500">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">Spam / Iklan Tidak Patut</div>
                                                <div class="text-xs text-gray-500">Mengandung spam atau iklan tanpa izin</div>
                                            </div>
                                        </label>
                                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl hover:border-red-300 cursor-pointer transition">
                                            <input type="radio" name="reason" value="inappropriate" class="text-red-500 focus:ring-red-500">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">Konten Tidak Pantas</div>
                                                <div class="text-xs text-gray-500">Mengandung konten yang tidak pantas atau menyinggung</div>
                                            </div>
                                        </label>
                                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl hover:border-red-300 cursor-pointer transition">
                                            <input type="radio" name="reason" value="other" class="text-red-500 focus:ring-red-500">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">Lainnya</div>
                                                <div class="text-xs text-gray-500">Alasan lain yang perlu ditinjau</div>
                                            </div>
                                        </label>
                                    </div>
                                    <textarea name="description" rows="2" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 mb-4" placeholder="Deskripsi tambahan (opsional)..."></textarea>
                                    <div class="flex gap-2">
                                        <button type="button" @click="reportOpen = false" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">Batal</button>
                                        <button type="submit" class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-xl transition">Kirim Laporan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endauth

                    {{-- Reactions Section --}}
                    @auth
                    <div class="mt-4 p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                        <h3 class="text-gray-900 font-semibold text-sm mb-3">Bagaimana simulasi ini?</h3>
                        <div class="flex items-center gap-2 flex-wrap" id="reactions-container">
                            @php
                                $reactionTypes = [
                                    'mudah_dipahami' => ['label' => 'Mudah Dipahami'],
                                    'membuka_wawasan' => ['label' => 'Membuka Wawasan'],
                                    'sangat_membantu' => ['label' => 'Sangat Membantu'],
                                    'interaktif' => ['label' => 'Interaktif'],
                                    'favorit' => ['label' => 'Favorit'],
                                ];
                            @endphp
                            @foreach($reactionTypes as $type => $info)
                                <button
                                    onclick="toggleReaction('{{ $type }}')"
                                    id="reaction-{{ $type }}"
                                    class="reaction-btn flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-medium rounded-full transition {{ in_array($type, $userReactions) ? 'active' : '' }}"
                                >
                                    @if($type === 'mudah_dipahami')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                    @elseif($type === 'membuka_wawasan')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    @elseif($type === 'sangat_membantu')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @elseif($type === 'interaktif')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @elseif($type === 'favorit')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    @endif
                                    <span>{{ $info['label'] }}</span>
                                    <span id="reaction-count-{{ $type }}" class="ml-1 text-gray-400">({{ $reactionCounts[$type] ?? 0 }})</span>
                                </button>
                            @endforeach
                        </div>

                        {{-- Reactions Distribution Pie Chart --}}
                        @php
                            $totalReactions = array_sum($reactionCounts);
                        @endphp
                        @if($totalReactions > 0)
                            @php
                                $reactionColors = [
                                    'mudah_dipahami' => '#3b82f6',
                                    'membuka_wawasan' => '#8b5cf6',
                                    'sangat_membantu' => '#10b981',
                                    'interaktif' => '#f59e0b',
                                    'favorit' => '#ef4444',
                                ];
                                $cumulativePercent = 0;
                                $gradientParts = [];
                                foreach ($reactionCounts as $type => $count) {
                                    $percent = ($count / $totalReactions) * 100;
                                    $color = $reactionColors[$type] ?? '#6b7280';
                                    $endPercent = $cumulativePercent + $percent;
                                    $gradientParts[] = "{$color} {$cumulativePercent}% {$endPercent}%";
                                    $cumulativePercent += $percent;
                                }
                                $gradientString = implode(', ', $gradientParts);
                            @endphp
                            <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-100">
                                <div class="w-20 h-20 rounded-full flex-shrink-0" style="background: conic-gradient({{ $gradientString }});" title="Distribusi Reaksi"></div>
                                <div class="flex-1 space-y-1.5">
                                    @foreach($reactionCounts as $type => $count)
                                        @php
                                            $percent = $totalReactions > 0 ? round(($count / $totalReactions) * 100, 1) : 0;
                                        @endphp
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="flex items-center gap-1.5 text-gray-600">
                                                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $reactionColors[$type] ?? '#6b7280' }}"></span>
                                                {{ str_replace('_', ' ', ucfirst($type)) }}
                                            </span>
                                            <span class="text-gray-500">{{ $percent }}%</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    @endauth

                    {{-- Rating Section --}}
                    @auth
                    <div class="mt-4 p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                        <h3 class="text-gray-900 font-semibold text-sm mb-3">Beri Rating</h3>
                        <div class="flex items-center gap-1" id="rating-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <svg
                                    class="rating-star w-7 h-7 {{ $i <= $userRating ? 'active text-yellow-400' : 'text-gray-300' }}"
                                    onclick="setRating({{ $i }})"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                ><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            @endfor
                            <span id="rating-text" class="text-sm text-gray-500 ml-2">
                                @if($userRating)
                                    {{ $userRating }}/5
                                @else
                                    Klik untuk memberi rating
                                @endif
                            </span>
                        </div>
                    </div>
                    @endauth

                    {{-- Category & Tags --}}
                    <div class="flex items-center gap-2 mt-4 flex-wrap">
                        <a href="{{ route('simulations.category', $simulation->category) }}" class="px-3 py-1 bg-blue-100 text-blue-600 text-xs font-medium rounded-full hover:bg-blue-200 transition">
                            {{ $simulation->category }}
                        </a>
                        @if($simulation->subcategory)
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">
                                {{ $simulation->subcategory }}
                            </span>
                        @endif
                        @foreach($simulation->tags_array as $tag)
                            <span class="px-3 py-1 bg-gray-100 text-gray-500 text-xs rounded-full">
                                #{{ $tag }}
                            </span>
                        @endforeach
                    </div>

                    {{-- Creator --}}
                    <div class="flex items-center gap-3 mt-5 p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                        <a href="{{ route('creators.show', $simulation->user->id) }}" class="flex items-center gap-3 flex-1">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-semibold overflow-hidden">
                                @if($simulation->user->avatar)
                                    <img src="{{ Storage::disk('public')->url($simulation->user->avatar) }}" alt="{{ $simulation->user->name }}" class="w-full h-full object-cover" />
                                @else
                                    {{ strtoupper(substr($simulation->user->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <p class="text-gray-900 font-medium text-sm">{{ $simulation->user->name }}</p>
                                <p class="text-gray-500 text-xs">{{ $simulation->user->simulations()->published()->count() }} simulasi &middot; {{ $simulation->user->followers()->count() }} pengikut</p>
                            </div>
                        </a>
                        @auth
                            @if(auth()->id() !== $simulation->user_id)
                                <button
                                    id="follow-btn"
                                    onclick="toggleFollow({{ $simulation->user->id }})"
                                    class="px-4 py-2 text-sm font-medium rounded-full transition {{ $isFollowing ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-blue-600 text-white hover:bg-blue-700' }}"
                                >
                                    <span id="follow-text">{{ $isFollowing ? 'Mengikuti' : 'Ikuti' }}</span>
                                </button>
                            @endif
                        @endauth
                    </div>

                    {{-- Description --}}
                    @if($simulation->description)
                    <div class="mt-5 p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                        <h3 class="text-gray-900 font-semibold text-sm mb-2">Deskripsi</h3>
                        <p class="text-gray-600 text-sm whitespace-pre-line">{{ $simulation->description }}</p>
                    </div>
                    @endif
                </div>

                {{-- Comments Section --}}
                <div class="mt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-900 font-semibold">Komentar ({{ $comments->count() }})</h3>
                    </div>

                    {{-- Comment Form --}}
                    @auth
                        <div class="flex gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs font-semibold flex-shrink-0 overflow-hidden">
                                @if(auth()->user()->avatar)
                                    <img src="{{ Storage::disk('public')->url(auth()->user()->avatar) }}" alt="" class="w-full h-full object-cover" />
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="flex-1">
                                <textarea
                                    id="comment-input"
                                    class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-gray-900 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-400"
                                    rows="2"
                                    placeholder="Tulis komentar..."
                                ></textarea>
                                <div class="flex justify-end mt-2">
                                    <button
                                        onclick="postComment()"
                                        class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"
                                    >
                                        Kirim
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mb-6 p-4 bg-gray-50 rounded-xl text-center border border-gray-100">
                            <p class="text-gray-500 text-sm">
                                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium">Masuk</a> untuk memberikan komentar.
                            </p>
                        </div>
                    @endauth

                    {{-- Comments List --}}
                    <div class="space-y-4" id="comments-container">
                        @forelse($comments as $comment)
                            @if(!$comment->parent_id)
                                @include('simulations._comment', ['comment' => $comment, 'depth' => 0])
                            @endif
                        @empty
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                <p class="text-gray-500 text-sm">Belum ada komentar. Jadilah yang pertama!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Right: Related Simulations --}}
            <div class="w-full lg:w-96 flex-shrink-0">
                <h3 class="text-gray-900 font-semibold mb-4">Simulasi Terkait</h3>
                <div class="space-y-3">
                    @forelse($related as $rel)
                        <a href="{{ route('simulations.show', $rel->slug) }}" class="flex gap-3 group">
                            <div class="w-40 aspect-video bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                @if($rel->thumbnail)
                                    <img src="{{ Storage::disk('public')->url($rel->thumbnail) }}" alt="{{ $rel->title }}" class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600">
                                        <svg class="w-6 h-6 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-gray-900 text-sm font-medium line-clamp-2 group-hover:text-blue-600 transition">{{ $rel->title }}</h4>
                                <p class="text-gray-500 text-xs mt-1">{{ $rel->user->name }}</p>
                                <p class="text-gray-400 text-xs">{{ $rel->formatted_play_count }} dimainkan</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-gray-500 text-sm">Belum ada simulasi terkait.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <script>
        // ========== Player Controls ==========
        var isPlaying = false;
        var isFullscreen = false;
        var isSticky = false;
        var playerWrapper = document.getElementById('player-wrapper');
        var poster = document.getElementById('player-poster');
        var container = document.getElementById('player-iframe-container');
        var controls = document.getElementById('player-controls');
        var iframe = document.getElementById('simulation-iframe');
        var stickyThreshold = 0;

        function playSimulation() {
            isPlaying = true;
            poster.classList.add('hidden');
            container.classList.remove('hidden');
            controls.classList.remove('hidden');
            var serveUrl = '{{ route("simulations.serve", ["slug" => $simulation->slug, "path" => $simulation->entry_point ?? "index.html"]) }}';
            iframe.src = serveUrl;
            updateStickyThreshold();
            fetch('{{ route("simulations.play", $simulation->slug) }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            }).then(function(r) {
                if (!r.ok || !(r.headers.get('content-type') || '').includes('application/json')) {
                    return null;
                }
                return r.json();
            }).catch(function() {});
        }

        function closeSimulation() {
            isPlaying = false;
            exitFullscreen();
            exitSticky();
            container.classList.add('hidden');
            controls.classList.add('hidden');
            poster.classList.remove('hidden');
            iframe.src = '';
        }

        function reloadSimulation() {
            if (iframe.src) { iframe.src = iframe.src; }
        }

        function toggleFullscreen() {
            isFullscreen ? exitFullscreen() : enterFullscreen();
        }

        function enterFullscreen() {
            isFullscreen = true;
            document.body.classList.add('fullscreen-mode');
            playerWrapper.classList.add('player-fullscreen');
            document.getElementById('icon-fullscreen-enter').classList.add('hidden');
            document.getElementById('icon-fullscreen-exit').classList.remove('hidden');
        }

        function exitFullscreen() {
            isFullscreen = false;
            document.body.classList.remove('fullscreen-mode');
            playerWrapper.classList.remove('player-fullscreen');
            document.getElementById('icon-fullscreen-enter').classList.remove('hidden');
            document.getElementById('icon-fullscreen-exit').classList.add('hidden');
        }

        function updateStickyThreshold() {
            var rect = playerWrapper.getBoundingClientRect();
            stickyThreshold = window.scrollY + rect.top + rect.height;
        }

        function enterSticky() {
            isSticky = true;
            playerWrapper.classList.add('player-sticky-active');
            playerWrapper.style.maxWidth = playerWrapper.parentElement.offsetWidth + 'px';
        }

        function exitSticky() {
            isSticky = false;
            playerWrapper.classList.remove('player-sticky-active');
            playerWrapper.style.maxWidth = '';
        }

        var scrollTicking = false;
        window.addEventListener('scroll', function () {
            if (!scrollTicking) {
                window.requestAnimationFrame(function () {
                    if (isPlaying && !isFullscreen) {
                        var scrollPos = window.scrollY;
                        if (scrollPos > stickyThreshold && !isSticky) { enterSticky(); }
                        else if (scrollPos <= stickyThreshold && isSticky) { exitSticky(); }
                    }
                    scrollTicking = false;
                });
                scrollTicking = true;
            }
        });

        window.addEventListener('resize', function () {
            if (isPlaying) {
                if (isSticky) { playerWrapper.style.maxWidth = ''; }
                updateStickyThreshold();
                if (isSticky) { playerWrapper.style.maxWidth = playerWrapper.parentElement.offsetWidth + 'px'; }
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && isFullscreen) { exitFullscreen(); }
        });

        function copyLink() {
            var url = window.location.href;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function() {
                    showToast('Link berhasil disalin!');
                });
            } else {
                var textarea = document.createElement('textarea');
                textarea.value = url;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                try {
                    document.execCommand('copy');
                    showToast('Link berhasil disalin!');
                } catch (err) {
                    showToast('Gagal menyalin link');
                }
                document.body.removeChild(textarea);
            }
        }

        // ========== Favorite ==========
        function toggleFavorite() {
            ajaxPost('{{ route("favorites.toggle", $simulation->id) }}', {}, function(result) {
                if (!result) return;
                var btn = document.getElementById('favorite-btn');
                var countEl = document.getElementById('favorite-count');
                var svg = btn.querySelector('svg');
                if (result.favorited) {
                    btn.classList.add('text-red-500');
                    svg.setAttribute('fill', 'currentColor');
                } else {
                    btn.classList.remove('text-red-500');
                    svg.setAttribute('fill', 'none');
                }
                countEl.textContent = '(' + (result.favorite_count || 0) + ')';
                showToast(result.message);
            });
        }

        // ========== Share Tracking ==========
        function trackShare(platform) {
            ajaxPost('{{ route("simulations.share", $simulation->id) }}', { platform: platform }, function(result) {
                if (!result) return;
                if (result.success) {
                    showToast(result.message);
                }
            });
        }

        // ========== Bookmark ==========
        function toggleBookmark() {
            ajaxPost('{{ route("bookmarks.toggle") }}', { simulation_id: {{ $simulation->id }} }, function(result) {
                if (!result) return;
                var btn = document.getElementById('bookmark-btn');
                var text = document.getElementById('bookmark-text');
                if (result.bookmarked) {
                    btn.classList.add('active');
                    btn.querySelector('svg').setAttribute('fill', 'currentColor');
                    text.textContent = 'Tersimpan';
                } else {
                    btn.classList.remove('active');
                    btn.querySelector('svg').setAttribute('fill', 'none');
                    text.textContent = 'Bookmark';
                }
                showToast(result.message);
            });
        }

        // ========== Add to Collection ==========
        function addToCollection(collectionId) {
            ajaxPost('{{ route("collections.add-simulation") }}', {
                collection_id: collectionId,
                simulation_id: {{ $simulation->id }}
            }, function(result) {
                if (!result) return;
                showToast(result.message);
                if (result.success) {
                    setTimeout(function() { window.location.reload(); }, 500);
                }
            });
        }

        // ========== Reactions ==========
        function toggleReaction(type) {
            ajaxPost('{{ route("reactions.toggle") }}', { simulation_id: {{ $simulation->id }}, type: type }, function(result) {
                if (!result) return;
                var btn = document.getElementById('reaction-' + type);
                var countEl = document.getElementById('reaction-count-' + type);
                if (result.active) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
                countEl.textContent = '(' + (result.count || 0) + ')';
            });
        }

        // ========== Rating ==========
        function setRating(value) {
            ajaxPost('{{ route("ratings.store") }}', { simulation_id: {{ $simulation->id }}, rating: value }, function(result) {
                if (!result) return;
                var stars = document.querySelectorAll('#rating-stars .rating-star');
                stars.forEach(function(star, index) {
                    if (index < value) {
                        star.classList.add('active', 'text-yellow-400');
                        star.classList.remove('text-gray-300');
                    } else {
                        star.classList.remove('active', 'text-yellow-400');
                        star.classList.add('text-gray-300');
                    }
                });
                document.getElementById('rating-text').textContent = value + '/5';
                showToast('Rating berhasil dikirim!');
            });
        }

        // ========== Follow ==========
        function toggleFollow(userId) {
            ajaxPost('/follows/' + userId + '/toggle', {}, function(result) {
                if (!result) return;
                var btn = document.getElementById('follow-btn');
                var text = document.getElementById('follow-text');
                if (result.following) {
                    btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    btn.classList.add('bg-gray-200', 'hover:bg-gray-300', 'text-gray-700');
                    text.textContent = 'Mengikuti';
                } else {
                    btn.classList.remove('bg-gray-200', 'hover:bg-gray-300', 'text-gray-700');
                    btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    text.textContent = 'Ikuti';
                }
                showToast(result.message);
            });
        }

        // ========== Follow Simulation ==========
        function toggleFollowSimulation() {
            ajaxPost('/follows/{{ $simulation->user->id }}/toggle', { followable_type: 'simulation', followable_id: {{ $simulation->id }} }, function(result) {
                if (!result) return;
                var btn = document.getElementById('sim-follow-btn');
                var text = document.getElementById('sim-follow-text');
                if (result.following) {
                    btn.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'text-white');
                    btn.classList.add('bg-gray-200', 'hover:bg-gray-300', 'text-gray-700');
                    text.textContent = 'Mengikuti Simulasi';
                } else {
                    btn.classList.remove('bg-gray-200', 'hover:bg-gray-300', 'text-gray-700');
                    btn.classList.add('bg-blue-600', 'hover:bg-blue-700', 'text-white');
                    text.textContent = 'Ikuti Simulasi';
                }
                showToast(result.message);
            });
        }

        // ========== Comments ==========
        function postComment(parentId) {
            var inputId = parentId ? 'reply-input-' + parentId : 'comment-input';
            var input = document.getElementById(inputId);
            var content = input.value.trim();
            if (!content) return;

            var data = { simulation_id: {{ $simulation->id }}, body: content };
            if (parentId) { data.parent_id = parentId; }

            ajaxPost('{{ route("comments.store", $simulation->slug) }}', data, function(result) {
                if (!result) return;
                if (result.success) {
                    input.value = '';
                    // Reload page to show new comment (simple approach)
                    window.location.reload();
                } else {
                    showToast(result.message || 'Gagal mengirim komentar');
                }
            });
        }

        function deleteComment(commentId) {
            showConfirm('Hapus komentar ini?').then(function(confirmed) {
                if (!confirmed) return;
                ajaxPost('{{ route("comments.destroy", ":id") }}'.replace(':id', commentId), { _method: 'DELETE' }, function(result) {
                    if (!result) return;
                    if (result.success) {
                        showToast('Komentar berhasil dihapus');
                        window.location.reload();
                    } else {
                        showToast(result.message || 'Gagal menghapus komentar');
                    }
                });
            });
        }

        function toggleReplyForm(commentId) {
            var form = document.getElementById('reply-form-' + commentId);
            form.classList.toggle('show');
        }

    </script>

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

    <x-toast />

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
