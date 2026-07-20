<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $creator->name }} - {{ config('app.name') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .simulation-card:hover .thumbnail-overlay { opacity: 1; }
        .simulation-card:hover img { transform: scale(1.05); }
    </style>
</head>
<body class="bg-gray-900 font-sans antialiased">

    @include('components.app-header')

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Creator Profile Header --}}
        <div class="bg-gray-800 rounded-2xl p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                {{-- Avatar --}}
                <div class="w-24 h-24 rounded-full bg-gray-700 flex items-center justify-center text-white text-3xl font-bold overflow-hidden flex-shrink-0">
                    @if($creator->avatar)
                        <img src="{{ Storage::disk('public')->url($creator->avatar) }}" alt="{{ $creator->name }}" class="w-full h-full object-cover" />
                    @else
                        {{ strtoupper(substr($creator->name, 0, 1)) }}
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 text-center sm:text-left">
                    <h1 class="text-2xl font-bold text-white">{{ $creator->name }}</h1>
                    @if($creator->bio)
                        <p class="text-gray-400 text-sm mt-2 max-w-lg">{{ $creator->bio }}</p>
                    @endif

                    <div class="flex items-center gap-6 mt-4 justify-center sm:justify-start">
                        <div class="text-center">
                            <p class="text-white font-bold text-lg">{{ $creator->simulations()->published()->count() }}</p>
                            <p class="text-gray-500 text-xs">Simulasi</p>
                        </div>
                        <div class="text-center">
                            <p class="text-white font-bold text-lg">{{ $creator->followers()->count() }}</p>
                            <p class="text-gray-500 text-xs">Pengikut</p>
                        </div>
                        <div class="text-center">
                            <p class="text-white font-bold text-lg">{{ $creator->following()->count() }}</p>
                            <p class="text-gray-500 text-xs">Mengikuti</p>
                        </div>
                    </div>

                    {{-- Follow Button --}}
                    @auth
                        @if(auth()->id() !== $creator->id)
                            <div class="mt-4">
                                <button
                                    id="follow-btn"
                                    onclick="toggleFollow({{ $creator->id }})"
                                    class="px-6 py-2 text-sm font-medium rounded-full transition {{ $isFollowing ? 'bg-gray-600 text-white hover:bg-gray-500' : 'bg-blue-600 text-white hover:bg-blue-700' }}"
                                >
                                    <span id="follow-text">{{ $isFollowing ? 'Mengikuti' : 'Ikuti' }}</span>
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="mt-4">
                            <a href="{{ route('login') }}" class="inline-block px-6 py-2 text-sm font-medium rounded-full bg-blue-600 text-white hover:bg-blue-700 transition">
                                Ikuti
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Simulations Grid --}}
        <div class="mt-8">
            <h2 class="text-xl font-bold text-white mb-4">Simulasi ({{ $simulations->count() }})</h2>

            @if($simulations->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($simulations as $sim)
                        <a href="{{ route('simulations.show', $sim->slug) }}" class="simulation-card group">
                            <div class="bg-gray-800 rounded-xl overflow-hidden">
                                <div class="aspect-video bg-gray-700 overflow-hidden relative">
                                    @if($sim->thumbnail)
                                        <img
                                            src="{{ Storage::disk('public')->url($sim->thumbnail) }}"
                                            alt="{{ $sim->title }}"
                                            class="w-full h-full object-cover transition duration-300"
                                        />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600">
                                            <svg class="w-10 h-10 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                                        </div>
                                    @endif
                                    {{-- Hover overlay --}}
                                    <div class="thumbnail-overlay absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 transition duration-300">
                                        <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <h3 class="text-white text-sm font-medium line-clamp-2 group-hover:text-blue-400 transition">{{ $sim->title }}</h3>
                                    <div class="flex items-center gap-2 mt-2 text-xs text-gray-500">
                                        <span>{{ $sim->formatted_play_count }} dimainkan</span>
                                        <span>&middot;</span>
                                        <span>{{ $sim->time_ago }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <svg class="w-16 h-16 text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                    <h3 class="text-gray-400 text-lg font-medium">Belum ada simulasi</h3>
                    <p class="text-gray-600 text-sm mt-2">Creator ini belum mengunggah simulasi apapun.</p>
                </div>
            @endif
        </div>
    </main>

    <script>
        function toggleFollow(userId) {
            fetch('/follows/' + userId + '/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(r) { return r.json(); })
            .then(function(result) {
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
                // Show toast
                var toast = document.createElement('div');
                toast.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-sm px-4 py-2 rounded-lg shadow-lg z-[99999] transition-opacity duration-300';
                toast.textContent = result.message;
                document.body.appendChild(toast);
                setTimeout(function() { toast.style.opacity = '0'; }, 1500);
                setTimeout(function() { toast.remove(); }, 1800);
            });
        }
    </script>
</body>
</html>
