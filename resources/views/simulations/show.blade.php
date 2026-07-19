<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $simulation->title }} - {{ config('app.name') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 font-sans antialiased">

    {{-- Top Navigation --}}
    <nav class="bg-white shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('logo.jpeg') }}" alt="NotEDs" class="w-7 h-7 rounded-lg object-cover" />
                    <span class="text-lg font-bold text-gray-900">Noteds</span>
                </a>
                <div class="flex items-center gap-3">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Studio</a>
                        @endif
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 hover:text-blue-600">{{ auth()->user()->name }}</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-blue-600">Masuk</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col lg:flex-row gap-6">

            {{-- Left: Player & Info --}}
            <div class="flex-1">
                {{-- Simulation Player --}}
                <div class="bg-black rounded-xl overflow-hidden aspect-video relative">
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

                    {{-- Play button overlay --}}
                    <div class="absolute inset-0 flex items-center justify-center">
                        <button
                            id="play-btn"
                            onclick="playSimulation()"
                            class="w-20 h-20 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-2xl transition duration-200 hover:scale-110"
                        >
                            <svg class="w-8 h-8 text-blue-600 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Simulation Info --}}
                <div class="mt-4">
                    <h1 class="text-xl font-bold text-white">{{ $simulation->title }}</h1>

                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-400">
                        <span>{{ number_format($simulation->view_count) }} dilihat</span>
                        <span>&middot;</span>
                        <span>{{ number_format($simulation->play_count) }} dimainkan</span>
                        <span>&middot;</span>
                        <span>{{ $simulation->time_ago }}</span>
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex items-center gap-2 mt-4 flex-wrap">
                        <button class="flex items-center gap-1.5 px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium rounded-full transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                            Suka
                        </button>
                        <button class="flex items-center gap-1.5 px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium rounded-full transition">
                            🔖 Bookmark
                        </button>
                        <button onclick="copyLink()" class="flex items-center gap-1.5 px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium rounded-full transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                            Share
                        </button>
                    </div>

                    {{-- Category & Tags --}}
                    <div class="flex items-center gap-2 mt-4">
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
                        <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr($simulation->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-white font-medium text-sm">{{ $simulation->user->name }}</p>
                            <p class="text-gray-400 text-xs">{{ $simulation->user->simulations()->published()->count() }} simulasi</p>
                        </div>
                    </div>

                    {{-- Description --}}
                    @if($simulation->description)
                    <div class="mt-5 p-4 bg-gray-800 rounded-xl">
                        <h3 class="text-white font-semibold text-sm mb-2">Deskripsi</h3>
                        <p class="text-gray-300 text-sm whitespace-pre-line">{{ $simulation->description }}</p>
                    </div>
                    @endif
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

    {{-- Hidden iframe for simulation --}}
    <div id="simulation-container" class="hidden fixed inset-0 z-50 bg-black">
        <div class="flex items-center justify-between p-2 bg-gray-900">
            <span class="text-white text-sm font-medium">{{ $simulation->title }}</span>
            <button onclick="closeSimulation()" class="text-white hover:text-red-400 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <iframe id="simulation-iframe" class="w-full" style="height: calc(100vh - 48px);" src="" frameborder="0"></iframe>
    </div>

    <script>
        function playSimulation() {
            // In MVP, we show an alert that simulation will be available soon
            // In production, this would load the simulation zip in an iframe
            alert('🚀 Simulasi akan segera dimainkan!\n\nFitur player simulasi sedang dalam pengembangan.');
        }

        function closeSimulation() {
            document.getElementById('simulation-container').classList.add('hidden');
            document.getElementById('simulation-iframe').src = '';
        }

        function copyLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Link berhasil disalin!');
            });
        }
    </script>
</body>
</html>
