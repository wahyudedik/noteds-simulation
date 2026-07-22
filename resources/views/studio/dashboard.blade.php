<x-studio-layout :pageTitle="'Dashboard'">
    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-8">
        <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total Simulasi</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalSimulations }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $publishedCount }} published · {{ $draftCount }} draft</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total Views</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalViews) }}</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total Plays</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalPlays) }}</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500">Followers</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalFollowers) }}</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500">Likes & Reaksi</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalLikes) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Trend Chart (7 Hari) --}}
        <div class="lg:col-span-2 bg-white border border-gray-100 rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Tren 7 Hari Terakhir</h3>
            </div>
            <div class="p-6">
                {{-- Simple Bar Chart --}}
                <div class="flex items-end gap-2 h-48">
                    @foreach($trendDays as $day)
                        @php
                            $maxVal = max($trendDays->pluck('views')->merge($trendDays->pluck('plays'))->max(), 1);
                            $viewHeight = ($day['views'] / $maxVal) * 100;
                            $playHeight = ($day['plays'] / $maxVal) * 100;
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <div class="flex gap-0.5 items-end w-full" style="height: 160px;">
                                <div class="flex-1 bg-blue-400 rounded-t transition-all duration-500 hover:bg-blue-500"
                                     style="height: {{ $viewHeight }}%"
                                     title="Views: {{ number_format($day['views']) }}"></div>
                                <div class="flex-1 bg-emerald-400 rounded-t transition-all duration-500 hover:bg-emerald-500"
                                     style="height: {{ $playHeight }}%"
                                     title="Plays: {{ number_format($day['plays']) }}"></div>
                            </div>
                            <span class="text-xs text-gray-400">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center gap-4 mt-4 text-xs text-gray-500">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-blue-400 rounded-sm"></span> Views</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-emerald-400 rounded-sm"></span> Plays</span>
                </div>
            </div>
        </div>

        {{-- Top Simulations --}}
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Simulasi Teratas</h3>
            </div>
            <div class="p-4">
                @if($topSimulations->count() > 0)
                    <div class="space-y-3">
                        @foreach($topSimulations as $i => $sim)
                            <a href="{{ route('studio.simulations.analytics', $sim->slug) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                                <span class="text-lg font-bold text-gray-300 w-6 text-center">{{ $i + 1 }}</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $sim->title }}</p>
                                    <p class="text-xs text-gray-500">{{ number_format($sim->play_count) }} plays · {{ number_format($sim->view_count) }} views</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400 text-sm">
                        <p>Belum ada simulasi.</p>
                        <a href="{{ route('studio.simulations.create') }}" class="text-blue-600 hover:underline mt-1 inline-block">Upload yang pertama!</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Quick Stats Row --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Bookmarks</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($totalBookmarks) }}</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Shares</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($totalShares) }}</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Komentar</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($totalComments) }}</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Likes</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($totalLikes) }}</p>
            </div>
        </div>
    </div>

    {{-- Recent Comments --}}
    @if($recentComments->count() > 0)
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm mt-6">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">Komentar Terbaru</h3>
                <a href="{{ route('studio.comments') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat Semua →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($recentComments as $comment)
                    <div class="px-6 py-4 hover:bg-gray-50 transition">
                        <div class="flex items-start gap-3">
                            @if($comment->user->avatar)
                                <img src="{{ Storage::url($comment->user->avatar) }}" alt="{{ $comment->user->name }}" class="w-8 h-8 rounded-full object-cover shrink-0 mt-0.5" />
                            @else
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-semibold shrink-0 mt-0.5">{{ strtoupper(substr($comment->user->name, 0, 1)) }}</div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm">
                                    <span class="font-medium text-gray-900">{{ $comment->user->name }}</span>
                                    <span class="text-gray-400"> di </span>
                                    <a href="{{ route('simulations.show', $comment->simulation->slug) }}" class="text-blue-600 hover:underline">{{ Str::limit($comment->simulation->title, 30) }}</a>
                                </p>
                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($comment->body, 120) }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $comment->created_at->diffForHumans() }}</p>
                            </div>
                            <form method="POST" action="{{ route('studio.comments.destroy', $comment->id) }}" class="shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmSubmit(this.closest('form'), 'Hapus komentar ini?')" class="text-gray-400 hover:text-red-500 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-studio-layout>
