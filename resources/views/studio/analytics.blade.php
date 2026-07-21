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
                <p class="text-xs text-gray-500">Completion Rate</p>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ $completionRate }}%</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ number_format($totalCompletions) }} / {{ number_format($totalPlays) }} plays</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                <p class="text-xs text-gray-500">Avg. Session</p>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ $avgSessionDuration }}d</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ floor($avgSessionDuration / 60) }}m {{ $avgSessionDuration % 60 }}s</p>
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

            {{-- Traffic Sources --}}
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Sumber Lalu Lintas</h3>
                </div>
                <div class="p-6">
                    @if($trafficSources->count() > 0)
                        @php
                            $sourceLabels = ['direct' => 'Direct', 'search' => 'Search Engine', 'social' => 'Social Media', 'embed' => 'Embed', 'referral' => 'Referral'];
                            $sourceColors = ['direct' => 'bg-blue-500', 'search' => 'bg-green-500', 'social' => 'bg-purple-500', 'embed' => 'bg-yellow-500', 'referral' => 'bg-red-500'];
                            $totalTraffic = $trafficSources->sum('total');
                        @endphp
                        <div class="space-y-3">
                            @foreach($trafficSources as $source)
                                @php
                                    $pct = $totalTraffic > 0 ? ($source->total / $totalTraffic) * 100 : 0;
                                    $color = $sourceColors[$source->source] ?? 'bg-gray-500';
                                    $label = $sourceLabels[$source->source] ?? $source->source;
                                @endphp
                                <div>
                                    <div class="flex items-center justify-between text-sm mb-1">
                                        <span class="font-medium text-gray-700">{{ $label }}</span>
                                        <span class="text-gray-500">{{ number_format($source->total) }} ({{ number_format($pct, 1) }}%)</span>
                                    </div>
                                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $color }} rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-400 text-sm">
                            Belum ada data sumber lalu lintas.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Completion & Duration Chart --}}
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Completion & Durasi (30 Hari)</h3>
                </div>
                <div class="p-6">
                    @if($dailyAnalytics->where('plays', '>', 0)->count() > 0)
                        @php
                            $maxPlays = max($dailyAnalytics->pluck('plays')->max(), 1);
                        @endphp
                        <div class="flex items-end gap-1 h-48">
                            @foreach($dailyAnalytics->where('plays', '>', 0) as $day)
                                @php
                                    $playH = ($day->plays / $maxPlays) * 100;
                                    $compH = $day->plays > 0 ? ($day->completions / $day->plays) * 100 : 0;
                                @endphp
                                <div class="flex-1 flex flex-col items-center gap-0.5" title="{{ $day->date }}: {{ $day->completions }}/{{ $day->plays }} completions, {{ $day->avg_duration_seconds }}s avg">
                                    <div class="w-full flex gap-px items-end" style="height: 140px;">
                                        <div class="flex-1 bg-emerald-400 rounded-t hover:bg-emerald-500 transition" style="height: {{ $playH }}%"></div>
                                        <div class="flex-1 bg-orange-400 rounded-t hover:bg-orange-500 transition" style="height: {{ $compH }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
                            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-emerald-400 rounded-sm"></span> Plays</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-orange-400 rounded-sm"></span> Completion %</span>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-400 text-sm">
                            Belum ada data play untuk 30 hari terakhir.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Rating Distribution --}}
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Distribusi Rating</h3>
                </div>
                <div class="p-6">
                    @for($i = 5; $i >= 1; $i--)
                        @php
                            $count = $ratingDistribution[$i] ?? 0;
                            $percentage = $ratingTotal > 0 ? ($count / $ratingTotal) * 100 : 0;
                        @endphp
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-sm text-gray-600 w-8">{{ $i }}★</span>
                            <div class="flex-1 bg-gray-100 rounded-full h-3 overflow-hidden">
                                <div class="bg-yellow-400 h-full rounded-full transition-all duration-500"
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="text-sm text-gray-500 w-16 text-right">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</x-studio-layout>
