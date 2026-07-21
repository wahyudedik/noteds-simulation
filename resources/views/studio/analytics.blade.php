<x-studio-layout :pageTitle="'Analitik: ' . $simulation->title">
    <div class="max-w-5xl mx-auto">
        {{-- Back Link --}}
        <a href="{{ route('studio.simulations') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Kembali ke Simulasi
        </a>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                <p class="text-xs text-gray-500">Total Views</p>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($simulation->view_count) }}</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                <p class="text-xs text-gray-500">Total Plays</p>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($simulation->play_count) }}</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                <p class="text-xs text-gray-500">Komentar</p>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($simulation->comment_count ?? 0) }}</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                <p class="text-xs text-gray-500">Versi</p>
                <p class="text-xl font-bold text-gray-900 mt-1">v{{ $simulation->version ?? '1.0.0' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Views & Plays Chart (30 Hari) --}}
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Views & Plays (30 Hari)</h3>
                </div>
                <div class="p-6">
                    @if($dailyAnalytics->count() > 0)
                        @php
                            $maxVal = max($dailyAnalytics->pluck('views')->merge($dailyAnalytics->pluck('plays'))->max(), 1);
                        @endphp
                        <div class="flex items-end gap-1 h-48">
                            @foreach($dailyAnalytics as $day)
                                @php
                                    $viewH = ($day->views / $maxVal) * 100;
                                    $playH = ($day->plays / $maxVal) * 100;
                                @endphp
                                <div class="flex-1 flex flex-col items-center gap-0.5" title="{{ $day->date }}: {{ $day->views }} views, {{ $day->plays }} plays">
                                    <div class="flex gap-px items-end w-full" style="height: 140px;">
                                        <div class="flex-1 bg-blue-400 rounded-t hover:bg-blue-500 transition" style="height: {{ $viewH }}%"></div>
                                        <div class="flex-1 bg-emerald-400 rounded-t hover:bg-emerald-500 transition" style="height: {{ $playH }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
                            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-blue-400 rounded-sm"></span> Views</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-emerald-400 rounded-sm"></span> Plays</span>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-400 text-sm">
                            Belum ada data analitik untuk 30 hari terakhir.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Reaction Breakdown --}}
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Reaksi</h3>
                </div>
                <div class="p-6">
                    @if($reactions->count() > 0)
                        @php
                            $reactionColors = ['like' => 'bg-blue-500', 'love' => 'bg-red-500', 'insightful' => 'bg-yellow-500', 'mindblown' => 'bg-purple-500', 'fun' => 'bg-green-500'];
                            $totalReactions = $reactions->sum('count');
                        @endphp
                        <div class="space-y-3">
                            @foreach($reactions as $reaction)
                                @php
                                    $pct = $totalReactions > 0 ? ($reaction->count / $totalReactions) * 100 : 0;
                                    $color = $reactionColors[$reaction->type] ?? 'bg-gray-500';
                                @endphp
                                <div>
                                    <div class="flex items-center justify-between text-sm mb-1">
                                        <span class="capitalize font-medium text-gray-700">{{ $reaction->type }}</span>
                                        <span class="text-gray-500">{{ $reaction->count }} ({{ number_format($pct, 1) }}%)</span>
                                    </div>
                                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $color }} rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-400 text-sm">
                            Belum ada reaksi.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Rating Distribution --}}
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm lg:col-span-2">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Distribusi Rating</h3>
                </div>
                <div class="p-6">
                    @if($ratingDistribution->count() > 0)
                        @php
                            $totalRatings = $ratingDistribution->sum('count');
                        @endphp
                        <div class="flex items-end gap-6 justify-center">
                            @for($i = 5; $i >= 1; $i--)
                                @php
                                    $count = $ratingDistribution->where('rating', $i)->first()->count ?? 0;
                                    $pct = $totalRatings > 0 ? ($count / $totalRatings) * 100 : 0;
                                @endphp
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-xs text-gray-500">{{ $count }}</span>
                                    <div class="w-12 bg-gray-100 rounded-t" style="height: {{ max($pct, 2) }}px">
                                        <div class="w-full bg-yellow-400 rounded-t" style="height: 100%"></div>
                                    </div>
                                    <div class="flex items-center gap-0.5">
                                        <span class="text-xs font-medium text-gray-700">{{ $i }}</span>
                                        <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                    </div>
                                </div>
                            @endfor
                        </div>
                        <p class="text-center text-sm text-gray-500 mt-4">{{ $totalRatings }} total rating</p>
                    @else
                        <div class="text-center py-8 text-gray-400 text-sm">
                            Belum ada rating.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-studio-layout>
