<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Profile Card --}}
            <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                <div class="flex items-center gap-5">
                    @if(auth()->user()->avatar)
                        <img src="{{ Storage::disk('public')->url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-16 h-16 rounded-full object-cover" />
                    @else
                        <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ auth()->user()->name }}</h3>
                        <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                        @if(auth()->user()->bio)
                            <p class="text-sm text-gray-600 mt-1">{{ auth()->user()->bio }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">Bergabung {{ auth()->user()->created_at->translatedFormat('d M Y') }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                        Edit Profil
                    </a>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-white border border-gray-100 shadow-sm rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['bookmarks'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Bookmark</div>
                </div>
                <div class="bg-white border border-gray-100 shadow-sm rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['simulations_played'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Dimainkan</div>
                </div>
                <div class="bg-white border border-gray-100 shadow-sm rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['following'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Mengikuti</div>
                </div>
                <div class="bg-white border border-gray-100 shadow-sm rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['followers'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Pengikut</div>
                </div>
                <div class="bg-white border border-gray-100 shadow-sm rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['comments'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Komentar</div>
                </div>
                <div class="bg-white border border-gray-100 shadow-sm rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['unread_notifications'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Notifikasi Baru</div>
                </div>
            </div>

            {{-- Level & Points Card --}}
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-5 text-white shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold">
                            {{ $levelProgress['level'] }}
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">{{ $levelProgress['level_title'] }}</h3>
                            <p class="text-sm text-blue-100">{{ number_format($levelProgress['total_points']) }} poin total</p>
                        </div>
                    </div>
                    <a href="{{ route('leaderboard.index') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-medium rounded-lg transition">
                        #1 Leaderboard
                    </a>
                </div>
                {{-- Progress Bar --}}
                <div class="mt-4">
                    <div class="flex items-center justify-between text-xs text-blue-100 mb-1">
                        <span>Level {{ $levelProgress['level'] }}</span>
                        <span>{{ number_format($levelProgress['current_points']) }} / {{ number_format($levelProgress['points_to_next']) }} poin ke Level {{ $levelProgress['level'] + 1 }}</span>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-2">
                        <div class="bg-white rounded-full h-2 transition-all duration-500" style="width: {{ $levelProgress['progress_percent'] }}%"></div>
                    </div>
                </div>
                @if($levelProgress['streak'] > 0)
                    <div class="mt-2 text-xs text-blue-100">
                        (fire) Streak: {{ $levelProgress['streak'] }} hari berturut-turut
                    </div>
                @endif
            </div>

            {{-- Quick Actions --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 bg-white border border-gray-100 shadow-sm rounded-xl p-4 hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">Edit Profil</div>
                        <div class="text-xs text-gray-500">Ubah foto, nama, bio</div>
                    </div>
                </a>
                <a href="{{ route('notifications.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 shadow-sm rounded-xl p-4 hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">Notifikasi</div>
                        <div class="text-xs text-gray-500">{{ $stats['unread_notifications'] }} belum dibaca</div>
                    </div>
                </a>
                <a href="{{ route('creators.show', auth()->id()) }}" class="flex items-center gap-3 bg-white border border-gray-100 shadow-sm rounded-xl p-4 hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">Profil Publik</div>
                        <div class="text-xs text-gray-500">Lihat profil Anda</div>
                    </div>
                </a>
            </div>

            {{-- Recent Bookmarks --}}
            @if($recent_bookmarks->count() > 0)
            <div class="bg-white border border-gray-100 shadow-sm rounded-xl p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Bookmark Terbaru</h3>
                <div class="space-y-3">
                    @foreach($recent_bookmarks as $sim)
                        @if($sim)
                        <a href="{{ route('simulations.show', $sim->slug) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                            <div class="w-16 aspect-video bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                @if($sim->thumbnail)
                                    <img src="{{ Storage::disk('public')->url($sim->thumbnail) }}" alt="{{ $sim->title }}" class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600">
                                        <svg class="w-5 h-5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 truncate">{{ $sim->title }}</h4>
                                <p class="text-xs text-gray-500">{{ $sim->user->name }} &middot; {{ $sim->formatted_play_count }} dimainkan</p>
                            </div>
                        </a>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Recent History --}}
            @if($recent_history->count() > 0)
            <div class="bg-white border border-gray-100 shadow-sm rounded-xl p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Riwayat Terbaru</h3>
                <div class="space-y-3">
                    @foreach($recent_history as $sim)
                        @if($sim)
                        <a href="{{ route('simulations.show', $sim->slug) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                            <div class="w-16 aspect-video bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                @if($sim->thumbnail)
                                    <img src="{{ Storage::disk('public')->url($sim->thumbnail) }}" alt="{{ $sim->title }}" class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600">
                                        <svg class="w-5 h-5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 truncate">{{ $sim->title }}</h4>
                                <p class="text-xs text-gray-500">{{ $sim->user->name }} &middot; {{ $sim->formatted_play_count }} dimainkan</p>
                            </div>
                        </a>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Empty State --}}
            @if($stats['bookmarks'] === 0 && $stats['simulations_played'] === 0)
            <div class="bg-white border border-gray-100 shadow-sm rounded-xl p-8 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                <h3 class="text-base font-semibold text-gray-700 mb-1">Mulai Menjelajahi</h3>
                <p class="text-sm text-gray-500 mb-4">Jelajahi simulasi interaktif dan mulai belajar!</p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cari Simulasi
                </a>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
