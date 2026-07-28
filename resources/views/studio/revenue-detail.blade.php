<x-studio-layout :pageTitle="'Detail Revenue'">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Detail Revenue</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Breakdown pendapatan dari setiap simulasi Anda</p>
        </div>
        <a href="{{ route('studio.ads-revenue') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">← Kembali ke Revenue</a>
    </div>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">Rp {{ number_format($breakdown['total_revenue'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500 dark:text-gray-400">Revenue Share</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $breakdown['creator_share_percent'] }}%</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $breakdown['tier_label'] }} Tier</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Simulasi</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ count($breakdown['simulations']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500 dark:text-gray-400">Min Payout</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Rp 500.000</p>
            <p class="text-xs {{ $breakdown['total_revenue'] >= 500000 ? 'text-green-500' : 'text-gray-400 dark:text-gray-500' }} mt-1">
                {{ $breakdown['total_revenue'] >= 500000 ? 'Siap payout!' : 'Rp ' . number_format(500000 - $breakdown['total_revenue'], 0, ',', '.') . ' lagi' }}
            </p>
        </div>
    </div>

    {{-- Daily Revenue Chart --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Revenue 30 Hari Terakhir</h3>
        </div>
        <div class="p-6">
            @if(count($dailyRevenue) > 0)
                @php
                    $maxRevenue = collect($dailyRevenue)->max('creator_revenue') ?: 1;
                @endphp
                <div class="flex items-end gap-1 h-40">
                    @foreach($dailyRevenue as $day)
                        @php
                            $height = $maxRevenue > 0 ? ($day['creator_revenue'] / $maxRevenue) * 100 : 0;
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-1 group relative">
                            <div class="w-full bg-emerald-400 rounded-t transition-all duration-300 hover:bg-emerald-500 cursor-pointer"
                                 style="height: {{ max(2, $height) }}%"
                                 title="{{ $day['label'] }}: Rp {{ number_format($day['creator_revenue'], 0, ',', '.') }}">
                            </div>
                            {{-- Tooltip --}}
                            <div class="hidden group-hover:block absolute bottom-full mb-2 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-lg px-3 py-2 whitespace-nowrap z-10 shadow-lg">
                                <p class="font-medium">{{ $day['label'] }}</p>
                                <p>Rp {{ number_format($day['creator_revenue'], 0, ',', '.') }}</p>
                                <p class="text-gray-400">{{ number_format($day['impressions']) }} impressions</p>
                            </div>
                            @if($loop->index % 5 === 0 || $loop->last)
                                <span class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">{{ $day['label'] }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center gap-4 mt-4 text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-emerald-400 rounded-sm"></span> Revenue Harian</span>
                </div>
            @else
                <div class="text-center py-8 text-gray-400 dark:text-gray-500 text-sm">
                    <p>Belum ada data revenue harian.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Revenue per Simulation --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Revenue per Simulasi</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Detail pendapatan dari setiap simulasi</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50">
                        <th class="text-left py-3 px-4 text-gray-500 dark:text-gray-400 font-medium">#</th>
                        <th class="text-left py-3 px-4 text-gray-500 dark:text-gray-400 font-medium">Simulasi</th>
                        <th class="text-center py-3 px-4 text-gray-500 dark:text-gray-400 font-medium">Plays</th>
                        <th class="text-center py-3 px-4 text-gray-500 dark:text-gray-400 font-medium">Views</th>
                        <th class="text-center py-3 px-4 text-gray-500 dark:text-gray-400 font-medium">Impressions</th>
                        <th class="text-right py-3 px-4 text-gray-500 dark:text-gray-400 font-medium">Gross Revenue</th>
                        <th class="text-right py-3 px-4 text-gray-500 dark:text-gray-400 font-medium">Your Revenue</th>
                        <th class="text-right py-3 px-4 text-gray-500 dark:text-gray-400 font-medium">Platform Share</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($breakdown['simulations'] as $i => $sim)
                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="py-3 px-4 text-gray-500 dark:text-gray-400">{{ $i + 1 }}</td>
                            <td class="py-3 px-4">
                                <a href="{{ route('simulations.show', $sim['slug']) }}" class="font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition">
                                    {{ Str::limit($sim['title'], 40) }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-center text-gray-600 dark:text-gray-400">{{ number_format($sim['play_count']) }}</td>
                            <td class="py-3 px-4 text-center text-gray-600 dark:text-gray-400">{{ number_format($sim['view_count']) }}</td>
                            <td class="py-3 px-4 text-center text-gray-600 dark:text-gray-400">{{ number_format($sim['impressions']) }}</td>
                            <td class="py-3 px-4 text-right text-gray-600 dark:text-gray-400">Rp {{ number_format($sim['gross_revenue'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-medium text-green-600 dark:text-green-400">Rp {{ number_format($sim['creator_revenue'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-gray-500 dark:text-gray-400">Rp {{ number_format($sim['platform_revenue'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <p>Belum ada simulasi yang dipublikasikan.</p>
                                    <a href="{{ route('studio.simulations.create') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Upload simulasi pertama Anda</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($breakdown['simulations']) > 0)
                    <tfoot>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 font-semibold">
                            <td colspan="5" class="py-3 px-4 text-gray-900 dark:text-white">Total</td>
                            <td class="py-3 px-4 text-right text-gray-600 dark:text-gray-400">Rp {{ number_format(collect($breakdown['simulations'])->sum('gross_revenue'), 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-green-600 dark:text-green-400">Rp {{ number_format(collect($breakdown['simulations'])->sum('creator_revenue'), 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-gray-500 dark:text-gray-400">Rp {{ number_format(collect($breakdown['simulations'])->sum('platform_revenue'), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</x-studio-layout>
