<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            #1 Leaderboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Period Tabs --}}
            <div class="flex items-center gap-2 mb-6">
                @foreach(['all' => 'Semua Waktu', 'month' => 'Bulan Ini', 'week' => 'Minggu Ini'] as $key => $label)
                    <a href="{{ route('leaderboard.index', ['period' => $key]) }}"
                       class="px-4 py-2 text-sm font-medium rounded-lg transition {{ $period === $key ? 'bg-gray-900 text-white' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Top 3 Podium --}}
            @if($leaderboard->count() >= 3)
                <div class="flex items-end justify-center gap-4 mb-8">
                    @php
                        $podiumColors = [2 => 'from-gray-400 to-gray-500', 1 => 'from-yellow-400 to-yellow-500', 3 => 'from-amber-600 to-amber-700'];
                        $podiumHeights = [2 => 'h-28', 1 => 'h-36', 3 => 'h-24'];
                        $podiumOrder = [2, 1, 3];
                    @endphp
                    @foreach($podiumOrder as $rank)
                        @php $entry = $leaderboard[$rank - 1] ?? null; @endphp
                        @if($entry)
                            <div class="flex flex-col items-center text-center">
                                @if($entry['user']->avatar)
                                    <img src="{{ Storage::url($entry['user']->avatar) }}"
                                         alt="{{ $entry['user']->name }}"
                                         class="w-16 h-16 rounded-full object-cover border-2 {{ $rank === 1 ? 'border-yellow-400' : 'border-gray-300' }} shadow-lg mb-2" />
                                @else
                                    <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl font-bold border-2 {{ $rank === 1 ? 'border-yellow-400' : 'border-gray-300' }} shadow-lg mb-2">
                                        {{ strtoupper(substr($entry['user']->name, 0, 1)) }}
                                    </div>
                                @endif
                                <p class="text-sm font-semibold text-gray-900 max-w-[100px] truncate">{{ $entry['user']->name }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($entry['points']) }} pts</p>
                                <div class="mt-2 w-20 bg-gray-100 rounded-t-lg overflow-hidden {{ $podiumHeights[$rank] }}">
                                    <div class="w-full h-full bg-gradient-to-t {{ $podiumColors[$rank] }} rounded-t-lg flex items-center justify-center">
                                        <span class="text-white text-2xl font-bold">{{ $rank }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            {{-- Full Leaderboard Table --}}
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
                @if($leaderboard->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 bg-gray-50">
                                    <th class="text-left py-3 px-3 sm:px-4 text-gray-500 font-medium w-12">#</th>
                                    <th class="text-left py-3 px-3 sm:px-4 text-gray-500 font-medium">Pengguna</th>
                                    <th class="hidden sm:table-cell text-center py-3 px-3 sm:px-4 text-gray-500 font-medium">Level</th>
                                    <th class="text-center py-3 px-3 sm:px-4 text-gray-500 font-medium">Streak</th>
                                    <th class="text-right py-3 px-3 sm:px-4 text-gray-500 font-medium">Poin</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($leaderboard as $rank => $entry)
                                    <tr class="hover:bg-gray-50 transition {{ $rank < 3 ? 'bg-yellow-50/30' : '' }}">
                                        <td class="py-3 px-3 sm:px-4">
                                            @if($rank === 0)
                                                <span class="text-lg">(1st)</span>
                                            @elseif($rank === 1)
                                                <span class="text-lg">(2nd)</span>
                                            @elseif($rank === 2)
                                                <span class="text-lg">(3rd)</span>
                                            @else
                                                <span class="text-sm font-medium text-gray-500">{{ $rank + 1 }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 sm:px-4">
                                            <a href="{{ route('creators.show', $entry['user']->id) }}" class="flex items-center gap-3 hover:opacity-80 transition">
                                                @if($entry['user']->avatar)
                                                    <img src="{{ Storage::url($entry['user']->avatar) }}"
                                                         alt="{{ $entry['user']->name }}"
                                                         class="w-8 h-8 rounded-full object-cover" />
                                                @else
                                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-semibold">
                                                        {{ strtoupper(substr($entry['user']->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div class="min-w-0">
                                                    <span class="font-medium text-gray-900 hover:text-blue-600 transition block truncate">{{ $entry['user']->name }}</span>
                                                    <span class="hidden sm:block text-xs text-gray-400">Lv.{{ $entry['level'] }} {{ $entry['level_title'] }}</span>
                                                </div>
                                            </a>
                                        </td>
                                        <td class="hidden sm:table-cell py-3 px-3 sm:px-4 text-center">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">
                                                Lv.{{ $entry['level'] }} {{ $entry['level_title'] }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 sm:px-4 text-center">
                                            @if($entry['streak'] > 0)
                                                <span class="inline-flex items-center gap-1 text-sm">
                                                    <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 23c-3.6 0-8-3.127-8-8.5C4 10.5 12 1 12 1s8 9.5 8 13.5c0 5.373-4.4 8.5-8 8.5zm0-18.5C8.6 7.2 6 12.1 6 14.5 6 18.649 9.134 21 12 21s6-2.351 6-6.5c0-2.4-2.6-7.3-6-10z"/><path d="M12 21c-1.657 0-4-1.343-4-4.5 0-2 2.5-6 4-8 1.5 2 4 6 4 8 0 3.157-2.343 4.5-4 4.5z" opacity="0.6"/></svg>
                                                    <span class="font-medium text-orange-600">{{ $entry['streak'] }}</span>
                                                </span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 sm:px-4 text-right font-semibold text-gray-900">{{ number_format($entry['points']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-16">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <h3 class="text-gray-500 text-lg font-medium">Belum ada data leaderboard</h3>
                        <p class="text-gray-400 text-sm mt-2">Mulai bermain simulasi untuk mendapatkan poin dan naik level!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
