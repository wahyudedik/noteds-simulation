<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil Saya - {{ config('app.name') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">

    @include('components.app-header')

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Profile Header --}}
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                {{-- Avatar --}}
                <div class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-3xl font-bold overflow-hidden flex-shrink-0">
                    @if($user->avatar)
                        <img src="{{ Storage::disk('public')->url($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover" />
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 text-center sm:text-left">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                    @if($user->bio)
                        <p class="text-gray-500 text-sm mt-2 max-w-lg">{{ $user->bio }}</p>
                    @endif

                    <div class="flex items-center gap-6 mt-4 justify-center sm:justify-start">
                        <div class="text-center">
                            <p class="text-gray-900 font-bold text-lg">{{ $stats['bookmarks'] }}</p>
                            <p class="text-gray-500 text-xs">Bookmark</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-900 font-bold text-lg">{{ $stats['simulations_played'] }}</p>
                            <p class="text-gray-500 text-xs">Dimainkan</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-900 font-bold text-lg">{{ $stats['following'] }}</p>
                            <p class="text-gray-500 text-xs">Mengikuti</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-900 font-bold text-lg">{{ $stats['followers'] }}</p>
                            <p class="text-gray-500 text-xs">Pengikut</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-900 font-bold text-lg">{{ $stats['comments'] }}</p>
                            <p class="text-gray-500 text-xs">Komentar</p>
                        </div>
                    </div>

                    <div class="mt-4 flex gap-3 justify-center sm:justify-start">
                        <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                            Edit Profil
                        </a>
                        <a href="{{ route('creators.show', $user->id) }}" class="px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 text-sm font-medium rounded-lg transition">
                            Lihat Profil Publik
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="mt-8">
            <div class="border-b border-gray-200">
                <nav class="flex gap-1 -mb-px">
                    <a href="{{ route('user-profile.index') }}"
                        class="px-5 py-3 text-sm font-medium border-b-2 transition {{ $activeTab === 'bookmarks' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="inline w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                        Bookmark
                    </a>
                    <a href="{{ route('user-profile.tab', 'history') }}"
                        class="px-5 py-3 text-sm font-medium border-b-2 transition {{ $activeTab === 'history' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="inline w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Riwayat
                    </a>
                    <a href="{{ route('user-profile.tab', 'following') }}"
                        class="px-5 py-3 text-sm font-medium border-b-2 transition {{ $activeTab === 'following' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="inline w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Mengikuti
                    </a>
                </nav>
            </div>

            {{-- Tab Content --}}
            <div class="mt-6">

                {{-- Bookmarks Tab --}}
                @if($activeTab === 'bookmarks')
                    @if(isset($data['bookmarks']) && $data['bookmarks']->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($data['bookmarks'] as $bookmark)
                                @if($bookmark->simulation)
                                    @include('components.simulation-card', ['simulation' => $bookmark->simulation])
                                @endif
                            @endforeach
                        </div>
                        <div class="mt-6">
                            {{ $data['bookmarks']->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-16">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                            <h3 class="text-gray-500 text-lg font-medium">Belum ada bookmark</h3>
                            <p class="text-gray-400 text-sm mt-2">Simulasi yang Anda bookmark akan muncul di sini.</p>
                            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                                Jelajahi Simulasi
                            </a>
                        </div>
                    @endif
                @endif

                {{-- History Tab --}}
                @if($activeTab === 'history')
                    @if(isset($data['history']) && $data['history']->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($data['history'] as $play)
                                @if($play->simulation)
                                    @include('components.simulation-card', ['simulation' => $play->simulation])
                                @endif
                            @endforeach
                        </div>
                        <div class="mt-6">
                            {{ $data['history']->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-16">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <h3 class="text-gray-500 text-lg font-medium">Belum ada riwayat</h3>
                            <p class="text-gray-400 text-sm mt-2">Riwayat bermain simulasi Anda akan muncul di sini.</p>
                            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                                Mulai Bermain
                            </a>
                        </div>
                    @endif
                @endif

                {{-- Following Tab --}}
                @if($activeTab === 'following')
                    @if(isset($data['following']) && $data['following']->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($data['following'] as $creator)
                                <a href="{{ route('creators.show', $creator->id) }}" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-lg font-bold overflow-hidden flex-shrink-0">
                                            @if($creator->avatar)
                                                <img src="{{ Storage::disk('public')->url($creator->avatar) }}" alt="{{ $creator->name }}" class="w-full h-full object-cover" />
                                            @else
                                                {{ strtoupper(substr($creator->name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-gray-900 font-medium text-sm truncate">{{ $creator->name }}</h4>
                                            <p class="text-gray-500 text-xs">{{ $creator->simulations_count }} simulasi</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="mt-6">
                            {{ $data['following']->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-16">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <h3 class="text-gray-500 text-lg font-medium">Belum mengikuti siapapun</h3>
                            <p class="text-gray-400 text-sm mt-2">Ikuti kreator untuk melihat simulasi terbaru mereka di sini.</p>
                            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                                Temukan Kreator
                            </a>
                        </div>
                    @endif
                @endif

            </div>
        </div>
    </main>

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
</body>
</html>
