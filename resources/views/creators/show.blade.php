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
<body class="bg-gray-50 font-sans antialiased">

    @include('components.app-header')

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Creator Profile Header --}}
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                {{-- Avatar --}}
                <div class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-3xl font-bold overflow-hidden flex-shrink-0">
                    @if($creator->avatar)
                        <img src="{{ Storage::disk('public')->url($creator->avatar) }}" alt="{{ $creator->name }}" class="w-full h-full object-cover" />
                    @else
                        {{ strtoupper(substr($creator->name, 0, 1)) }}
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 text-center sm:text-left">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $creator->name }}
                        @if($creator->role === 'superadmin')
                            <span class="inline-flex items-center ml-2 px-2 py-0.5 text-xs font-medium rounded-full bg-purple-100 text-purple-700">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                Superadmin
                            </span>
                        @elseif($creator->role === 'admin')
                            <span class="inline-flex items-center ml-2 px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Admin
                            </span>
                        @elseif($creator->role === 'creator')
                            <span class="inline-flex items-center ml-2 px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                Creator
                            </span>
                        @endif
                    </h1>
                    @if($creator->bio)
                        <p class="text-gray-500 text-sm mt-2 max-w-lg">{{ $creator->bio }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-2">Bergabung {{ $creator->created_at->translatedFormat('d M Y') }}</p>

                    <div class="flex items-center gap-6 mt-4 justify-center sm:justify-start">
                        <div class="text-center">
                            <p class="text-gray-900 font-bold text-lg">{{ $creator->simulations()->published()->count() }}</p>
                            <p class="text-gray-500 text-xs">Simulasi</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-900 font-bold text-lg">{{ $creator->followers()->count() }}</p>
                            <p class="text-gray-500 text-xs">Pengikut</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-900 font-bold text-lg">{{ $creator->following()->count() }}</p>
                            <p class="text-gray-500 text-xs">Mengikuti</p>
                        </div>
                    </div>

                    {{-- Follow + Share Buttons --}}
                    <div class="mt-4 flex gap-3">
                        @auth
                            @if(auth()->id() !== $creator->id)
                                <button
                                    id="follow-btn"
                                    onclick="toggleFollow({{ $creator->id }})"
                                    class="px-6 py-2 text-sm font-medium rounded-full transition {{ $isFollowing ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-blue-600 text-white hover:bg-blue-700' }}"
                                >
                                    <span id="follow-text">{{ $isFollowing ? 'Mengikuti' : 'Ikuti' }}</span>
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="inline-block px-6 py-2 text-sm font-medium rounded-full bg-blue-600 text-white hover:bg-blue-700 transition">
                                Ikuti
                            </a>
                        @endauth
                        <button onclick="copyProfileLink()" class="px-4 py-2 text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                            Bagikan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Simulations Grid --}}
        <div class="mt-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Simulasi ({{ $simulations->count() }})</h2>

            @if($simulations->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($simulations as $sim)
                        <a href="{{ route('simulations.show', $sim->slug) }}" class="simulation-card group">
                            <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100">
                                <div class="aspect-video bg-gray-200 overflow-hidden relative">
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
                                    <h3 class="text-gray-900 text-sm font-medium line-clamp-2 group-hover:text-blue-600 transition">{{ $sim->title }}</h3>
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
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                    <h3 class="text-gray-500 text-lg font-medium">Belum ada simulasi</h3>
                    <p class="text-gray-400 text-sm mt-2">Creator ini belum mengunggah simulasi apapun.</p>
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
                    btn.classList.add('bg-gray-200', 'hover:bg-gray-300');
                    btn.classList.remove('text-white');
                    btn.classList.add('text-gray-700');
                    text.textContent = 'Mengikuti';
                } else {
                    btn.classList.remove('bg-gray-200', 'hover:bg-gray-300');
                    btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    btn.classList.remove('text-gray-700');
                    btn.classList.add('text-white');
                    text.textContent = 'Ikuti';
                }
                // Show toast
                var toast = document.createElement('div');
                toast.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-sm px-4 py-2 rounded-lg shadow-lg z-[99999] transition-opacity duration-300';
                toast.textContent = result.message;
                document.body.appendChild(toast);
                setTimeout(function() { toast.style.opacity = '0'; }, 1500);
                setTimeout(function() { toast.remove(); }, 1800);
            });
        }

        function copyProfileLink() {
            var url = window.location.href;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function() {
                    showToast('Link profil berhasil disalin!');
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
                    showToast('Link profil berhasil disalin!');
                } catch (err) {
                    showToast('Gagal menyalin link');
                }
                document.body.removeChild(textarea);
            }
        }

        function showToast(message) {
            var toast = document.createElement('div');
            toast.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-sm px-4 py-2 rounded-lg shadow-lg z-[99999] transition-opacity duration-300';
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(function() { toast.style.opacity = '0'; }, 1500);
            setTimeout(function() { toast.remove(); }, 1800);
        }
    </script>
</body>
</html>
