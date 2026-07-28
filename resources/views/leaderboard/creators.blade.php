<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                🏆 Top Creators
            </h2>
            <a href="{{ route('leaderboard.index') }}" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition">
                ← Leaderboard Poin
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- Period Tabs --}}
            <div class="flex flex-wrap items-center gap-2 mb-4">
                @foreach(['all' => 'Semua Waktu', 'month' => 'Bulan Ini', 'week' => 'Minggu Ini'] as $key => $label)
                    <a href="{{ route('leaderboard.creators', ['period' => $key, 'sort' => $sortBy]) }}"
                       class="px-4 py-2 text-sm font-medium rounded-lg transition {{ $period === $key ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:border-gray-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Sort Tabs --}}
            <div class="flex flex-wrap items-center gap-2 mb-8">
                <span class="text-xs text-gray-500 dark:text-gray-400 mr-1">Urutkan:</span>
                @foreach(['ranking' => '🏆 Ranking', 'followers' => '👥 Followers', 'simulations' => '📦 Simulasi', 'rating' => '⭐ Rating'] as $key => $label)
                    <a href="{{ route('leaderboard.creators', ['period' => $period, 'sort' => $key]) }}"
                       class="px-3 py-1.5 text-xs font-medium rounded-full transition {{ $sortBy === $key ? 'bg-blue-600 text-white dark:bg-blue-500' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Top 3 Podium --}}
            @if($topCreators->count() >= 3)
                <div class="flex items-end justify-center gap-4 mb-10">
                    @php
                        $podiumColors = [2 => 'from-gray-400 to-gray-500', 1 => 'from-yellow-400 to-yellow-500', 3 => 'from-amber-600 to-amber-700'];
                        $podiumHeights = [2 => 'h-28', 1 => 'h-36', 3 => 'h-24'];
                        $podiumOrder = [2, 1, 3];
                    @endphp
                    @foreach($podiumOrder as $rank)
                        @php $entry = $topCreators[$rank - 1] ?? null; @endphp
                        @if($entry)
                            <div class="flex flex-col items-center text-center">
                                @if($entry['user']->avatar)
                                    <img src="{{ Storage::disk('public')->url($entry['user']->avatar) }}"
                                         alt="{{ $entry['user']->name }}"
                                         class="w-16 h-16 rounded-full object-cover border-2 {{ $rank === 1 ? 'border-yellow-400' : 'border-gray-300 dark:border-gray-600' }} shadow-lg mb-2" />
                                @else
                                    <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl font-bold border-2 {{ $rank === 1 ? 'border-yellow-400' : 'border-gray-300 dark:border-gray-600' }} shadow-lg mb-2">
                                        {{ strtoupper(substr($entry['user']->name, 0, 1)) }}
                                    </div>
                                @endif
                                <a href="{{ route('creators.show', $entry['user']->username) }}" class="text-sm font-semibold text-gray-900 dark:text-white max-w-[100px] truncate hover:text-blue-600 transition">
                                    {{ $entry['user']->name }}
                                </a>
                                @if($entry['user']->isVerifiedCreator())
                                    <x-verified-badge :type="$entry['user']->verification_badge" size="sm" />
                                @endif
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($entry['ranking_score'], 0) }} pts</p>
                                <div class="mt-2 w-20 bg-gray-100 dark:bg-gray-700 rounded-t-lg overflow-hidden {{ $podiumHeights[$rank] }}">
                                    <div class="w-full h-full bg-gradient-to-t {{ $podiumColors[$rank] }} rounded-t-lg flex items-center justify-center">
                                        <span class="text-white text-2xl font-bold">{{ $rank }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            {{-- Trending Creators --}}
            @if($trendingCreators->count() > 0)
                <div class="mb-10">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="text-orange-500">🔥</span> Trending Creator
                        <span class="text-xs font-normal text-gray-500 dark:text-gray-400 ml-1">— Aktif minggu ini</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        @foreach($trendingCreators as $trending)
                            <a href="{{ route('creators.show', $trending['user']->username) }}" class="group">
                                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-4 hover:shadow-md hover:border-orange-200 dark:hover:border-orange-800 transition text-center">
                                    <div class="w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-900/50 text-orange-600 dark:text-orange-400 flex items-center justify-center text-lg font-bold mx-auto mb-2 overflow-hidden">
                                        @if($trending['user']->avatar)
                                            <img src="{{ Storage::disk('public')->url($trending['user']->avatar) }}" alt="{{ $trending['user']->name }}" class="w-full h-full object-cover" />
                                        @else
                                            {{ strtoupper(substr($trending['user']->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover:text-orange-600 transition">{{ $trending['user']->name }}</p>
                                    @if($trending['user']->isVerifiedCreator())
                                        <div class="mt-1"><x-verified-badge :type="$trending['user']->verification_badge" size="sm" /></div>
                                    @endif
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $trending['simulation_count'] }} simulasi · {{ number_format($trending['follower_count']) }} followers</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Creator Ranking Table --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
                @if($topCreators->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                                    <th class="text-left py-3 px-3 sm:px-4 text-gray-500 dark:text-gray-400 font-medium w-12">#</th>
                                    <th class="text-left py-3 px-3 sm:px-4 text-gray-500 dark:text-gray-400 font-medium">Creator</th>
                                    <th class="hidden sm:table-cell text-center py-3 px-3 sm:px-4 text-gray-500 dark:text-gray-400 font-medium">Simulasi</th>
                                    <th class="hidden md:table-cell text-center py-3 px-3 sm:px-4 text-gray-500 dark:text-gray-400 font-medium">Rating</th>
                                    <th class="text-center py-3 px-3 sm:px-4 text-gray-500 dark:text-gray-400 font-medium">Followers</th>
                                    <th class="text-right py-3 px-3 sm:px-4 text-gray-500 dark:text-gray-400 font-medium">Skor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                @foreach($topCreators as $rank => $entry)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition {{ $rank < 3 ? 'bg-yellow-50/30 dark:bg-yellow-900/10' : '' }}">
                                        <td class="py-3 px-3 sm:px-4">
                                            @if($rank < 3)
                                                @php
                                                    $rankLabels = [0 => '🥇 1', 1 => '🥈 2', 2 => '🥉 3'];
                                                @endphp
                                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $rankLabels[$rank] }}</span>
                                            @else
                                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $rank + 1 }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 sm:px-4">
                                            <a href="{{ route('creators.show', $entry['user']->username) }}" class="flex items-center gap-3 hover:opacity-80 transition">
                                                @if($entry['user']->avatar)
                                                    <img src="{{ Storage::disk('public')->url($entry['user']->avatar) }}"
                                                         alt="{{ $entry['user']->name }}"
                                                         class="w-9 h-9 rounded-full object-cover" />
                                                @else
                                                    <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm font-semibold">
                                                        {{ strtoupper(substr($entry['user']->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="font-medium text-gray-900 dark:text-white hover:text-blue-600 transition truncate">{{ $entry['user']->name }}</span>
                                                        @if($entry['user']->isVerifiedCreator())
                                                            <x-verified-badge :type="$entry['user']->verification_badge" size="sm" />
                                                        @endif
                                                    </div>
                                                    @if($entry['user']->reputation)
                                                        <span class="hidden sm:inline text-xs px-1.5 py-0.5 rounded-full font-medium
                                                            {{ match($entry['user']->reputation->revenue_tier) {
                                                                'platinum' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-400',
                                                                'expert' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400',
                                                                'verified' => 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400',
                                                                default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                                            } }}">
                                                            {{ $entry['user']->reputation->tier_label }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </a>
                                        </td>
                                        <td class="hidden sm:table-cell py-3 px-3 sm:px-4 text-center">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $entry['simulation_count'] }}</span>
                                        </td>
                                        <td class="hidden md:table-cell py-3 px-3 sm:px-4 text-center">
                                            @if($entry['avg_rating'] > 0)
                                                <span class="inline-flex items-center gap-1 text-sm">
                                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ number_format($entry['avg_rating'], 1) }}</span>
                                                </span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-600">—</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 sm:px-4 text-center">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ number_format($entry['follower_count']) }}</span>
                                        </td>
                                        <td class="py-3 px-3 sm:px-4 text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                                {{ $rank < 3 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                                {{ number_format($entry['ranking_score'], 0) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-16">
                        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <h3 class="text-gray-500 dark:text-gray-400 text-lg font-medium">Belum ada data creator</h3>
                        <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">Creator yang aktif membuat simulasi akan muncul di sini.</p>
                        <a href="{{ route('dashboard') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                                Mulai Menjadi Creator
                            </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
