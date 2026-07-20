<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            background: #111827;
        }
        ::-webkit-scrollbar-thumb {
            background: #374151;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #4b5563;
        }
        * {
            scrollbar-width: thin;
            scrollbar-color: #374151 #111827;
        }
        .reaction-btn.active { background-color: #2563eb; color: white; }
        .bookmark-btn.active { color: #facc15; }
        .rating-star { cursor: pointer; transition: color 0.15s; }
        .rating-star:hover, .rating-star.active { color: #facc15; }
        .comment-reply { display: none; }
        .comment-reply.show { display: block; }
    </style>
</head>
<body class="bg-gray-900 font-sans antialiased">

    @include('components.app-header')

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
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
                    <div id="player-controls" class="hidden bg-gray-900 border-t border-gray-800 px-3 py-2 flex items-center justify-between rounded-b-xl">
                        <div class="flex items-center gap-2">
                            <button onclick="closeSimulation()" class="p-1.5 text-gray-400 hover:text-white rounded-lg hover:bg-gray-800 transition" title="Tutup simulasi">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                            <button onclick="reloadSimulation()" class="p-1.5 text-gray-400 hover:text-white rounded-lg hover:bg-gray-800 transition" title="Muat ulang">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            </button>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="text-xs text-gray-500 mr-2 hidden sm:inline">{{ $simulation->title }}</span>
                            <button onclick="toggleFullscreen()" id="btn-fullscreen" class="p-1.5 text-gray-400 hover:text-white rounded-lg hover:bg-gray-800 transition" title="Layar penuh">
                                <svg id="icon-fullscreen-enter" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" /></svg>
                                <svg id="icon-fullscreen-exit" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25" /></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Simulation Info --}}
                <div class="mt-4">
                    <h1 class="text-xl font-bold text-white">{{ $simulation->title }}</h1>

                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-400">
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
                                    <svg class="w-4 h-4 {{ $i <= round($avgRating) ? 'text-yellow-400' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                @endfor
                                <span class="text-sm text-gray-400 ml-1">{{ number_format($avgRating, 1) }}</span>
                                <span class="text-xs text-gray-500">({{ $simulation->ratings()->count() }})</span>
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
                                class="bookmark-btn flex items-center gap-1.5 px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium rounded-full transition {{ $isBookmarked ? 'active' : '' }}"
                            >
                                <svg class="w-4 h-4" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                                <span id="bookmark-text">{{ $isBookmarked ? 'Tersimpan' : 'Bookmark' }}</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="flex items-center gap-1.5 px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium rounded-full transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                                Bookmark
                            </a>
                        @endauth

                        <button onclick="copyLink()" class="flex items-center gap-1.5 px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium rounded-full transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                            Share
                        </button>
                    </div>

                    {{-- Reactions Section --}}
                    @auth
                    <div class="mt-4 p-4 bg-gray-800 rounded-xl">
                        <h3 class="text-white font-semibold text-sm mb-3">Bagaimana simulasi ini?</h3>
                        <div class="flex items-center gap-2 flex-wrap" id="reactions-container">
                            @php
                                $reactionTypes = [
                                    'mudah_dipahami' => ['icon' => '🧠', 'label' => 'Mudah Dipahami'],
                                    'membuka_wawasan' => ['icon' => '💡', 'label' => 'Membuka Wawasan'],
                                    'sangat_membantu' => ['icon' => '✅', 'label' => 'Sangat Membantu'],
                                    'interaktif' => ['icon' => '🎮', 'label' => 'Interaktif'],
                                    'favorit' => ['icon' => '⭐', 'label' => 'Favorit'],
                                ];
                            @endphp
                            @foreach($reactionTypes as $type => $info)
                                <button
                                    onclick="toggleReaction('{{ $type }}')"
                                    id="reaction-{{ $type }}"
                                    class="reaction-btn flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-gray-300 text-xs font-medium rounded-full transition {{ in_array($type, $userReactions) ? 'active' : '' }}"
                                >
                                    <span>{{ $info['icon'] }}</span>
                                    <span>{{ $info['label'] }}</span>
                                    <span id="reaction-count-{{ $type }}" class="ml-1 text-gray-400">({{ $reactionCounts[$type] ?? 0 }})</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @endauth

                    {{-- Rating Section --}}
                    @auth
                    <div class="mt-4 p-4 bg-gray-800 rounded-xl">
                        <h3 class="text-white font-semibold text-sm mb-3">Beri Rating</h3>
                        <div class="flex items-center gap-1" id="rating-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <svg
                                    class="rating-star w-7 h-7 {{ $i <= $userRating ? 'active text-yellow-400' : 'text-gray-600' }}"
                                    onclick="setRating({{ $i }})"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                ><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            @endfor
                            <span id="rating-text" class="text-sm text-gray-400 ml-2">
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
                        <a href="{{ route('simulations.category', $simulation->category) }}" class="px-3 py-1 bg-blue-600/20 text-blue-400 text-xs font-medium rounded-full hover:bg-blue-600/30 transition">
                            {{ $simulation->category }}
                        </a>
                        @if($simulation->subcategory)
                            <span class="px-3 py-1 bg-gray-700 text-gray-300 text-xs font-medium rounded-full">
                                {{ $simulation->subcategory }}
                            </span>
                        @endif
                        @foreach($simulation->tags_array as $tag)
                            <span class="px-3 py-1 bg-gray-800 text-gray-400 text-xs rounded-full">
                                #{{ $tag }}
                            </span>
                        @endforeach
                    </div>

                    {{-- Creator --}}
                    <div class="flex items-center gap-3 mt-5 p-4 bg-gray-800 rounded-xl">
                        <a href="{{ route('creators.show', $simulation->user->id) }}" class="flex items-center gap-3 flex-1">
                            <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-white font-semibold overflow-hidden">
                                @if($simulation->user->avatar)
                                    <img src="{{ Storage::disk('public')->url($simulation->user->avatar) }}" alt="{{ $simulation->user->name }}" class="w-full h-full object-cover" />
                                @else
                                    {{ strtoupper(substr($simulation->user->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <p class="text-white font-medium text-sm">{{ $simulation->user->name }}</p>
                                <p class="text-gray-400 text-xs">{{ $simulation->user->simulations()->published()->count() }} simulasi &middot; {{ $simulation->user->followers()->count() }} pengikut</p>
                            </div>
                        </a>
                        @auth
                            @if(auth()->id() !== $simulation->user_id)
                                <button
                                    id="follow-btn"
                                    onclick="toggleFollow({{ $simulation->user->id }})"
                                    class="px-4 py-2 text-sm font-medium rounded-full transition {{ $isFollowing ? 'bg-gray-600 text-white hover:bg-gray-500' : 'bg-blue-600 text-white hover:bg-blue-700' }}"
                                >
                                    <span id="follow-text">{{ $isFollowing ? 'Mengikuti' : 'Ikuti' }}</span>
                                </button>
                            @endif
                        @endauth
                    </div>

                    {{-- Description --}}
                    @if($simulation->description)
                    <div class="mt-5 p-4 bg-gray-800 rounded-xl">
                        <h3 class="text-white font-semibold text-sm mb-2">Deskripsi</h3>
                        <p class="text-gray-300 text-sm whitespace-pre-line">{{ $simulation->description }}</p>
                    </div>
                    @endif
                </div>

                {{-- Comments Section --}}
                <div class="mt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-white font-semibold">Komentar ({{ $comments->count() }})</h3>
                    </div>

                    {{-- Comment Form --}}
                    @auth
                        <div class="flex gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0 overflow-hidden">
                                @if(auth()->user()->avatar)
                                    <img src="{{ Storage::disk('public')->url(auth()->user()->avatar) }}" alt="" class="w-full h-full object-cover" />
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="flex-1">
                                <textarea
                                    id="comment-input"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-500"
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
                        <div class="mb-6 p-4 bg-gray-800 rounded-xl text-center">
                            <p class="text-gray-400 text-sm">
                                <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-medium">Masuk</a> untuk memberikan komentar.
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
                                <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                <p class="text-gray-500 text-sm">Belum ada komentar. Jadilah yang pertama!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Right: Related Simulations --}}
            <div class="w-full lg:w-96 flex-shrink-0">
                <h3 class="text-white font-semibold mb-4">Simulasi Terkait</h3>
                <div class="space-y-3">
                    @forelse($related as $rel)
                        <a href="{{ route('simulations.show', $rel->slug) }}" class="flex gap-3 group">
                            <div class="w-40 aspect-video bg-gray-800 rounded-lg overflow-hidden flex-shrink-0">
                                @if($rel->thumbnail)
                                    <img src="{{ Storage::disk('public')->url($rel->thumbnail) }}" alt="{{ $rel->title }}" class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600">
                                        <svg class="w-6 h-6 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-white text-sm font-medium line-clamp-2 group-hover:text-blue-400 transition">{{ $rel->title }}</h4>
                                <p class="text-gray-400 text-xs mt-1">{{ $rel->user->name }}</p>
                                <p class="text-gray-500 text-xs">{{ $rel->formatted_play_count }} dimainkan</p>
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
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
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
            navigator.clipboard.writeText(window.location.href).then(function() {
                showToast('Link berhasil disalin!');
            });
        }

        function showToast(message) {
            var toast = document.createElement('div');
            toast.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-sm px-4 py-2 rounded-lg shadow-lg z-[99999] transition-opacity duration-300';
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(function() { toast.style.opacity = '0'; }, 1500);
            setTimeout(function() { toast.remove(); }, 1800);
        }

        // ========== AJAX Helpers ==========
        function ajaxPost(url, data, callback) {
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(function(response) { return response.json(); })
            .then(function(result) { if (callback) callback(result); })
            .catch(function(err) { console.error('AJAX Error:', err); });
        }

        // ========== Bookmark ==========
        function toggleBookmark() {
            ajaxPost('{{ route("bookmarks.toggle") }}', { simulation_id: {{ $simulation->id }} }, function(result) {
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

        // ========== Reactions ==========
        function toggleReaction(type) {
            ajaxPost('{{ route("reactions.toggle") }}', { simulation_id: {{ $simulation->id }}, type: type }, function(result) {
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
                var stars = document.querySelectorAll('#rating-stars .rating-star');
                stars.forEach(function(star, index) {
                    if (index < value) {
                        star.classList.add('active', 'text-yellow-400');
                        star.classList.remove('text-gray-600');
                    } else {
                        star.classList.remove('active', 'text-yellow-400');
                        star.classList.add('text-gray-600');
                    }
                });
                document.getElementById('rating-text').textContent = value + '/5';
                showToast('Rating berhasil dikirim!');
            });
        }

        // ========== Follow ==========
        function toggleFollow(userId) {
            ajaxPost('/follows/' + userId + '/toggle', {}, function(result) {
                var btn = document.getElementById('follow-btn');
                var text = document.getElementById('follow-text');
                if (result.following) {
                    btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    btn.classList.add('bg-gray-600', 'hover:bg-gray-500');
                    text.textContent = 'Mengikuti';
                } else {
                    btn.classList.remove('bg-gray-600', 'hover:bg-gray-500');
                    btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    text.textContent = 'Ikuti';
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
            if (!confirm('Hapus komentar ini?')) return;
            ajaxPost('{{ route("comments.destroy", ":id") }}'.replace(':id', commentId), { _method: 'DELETE' }, function(result) {
                if (result.success) {
                    showToast('Komentar berhasil dihapus');
                    window.location.reload();
                } else {
                    showToast(result.message || 'Gagal menghapus komentar');
                }
            });
        }

        function toggleReplyForm(commentId) {
            var form = document.getElementById('reply-form-' + commentId);
            form.classList.toggle('show');
        }

    </script>
</body>
</html>
