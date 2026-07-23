<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Ad Analytics
            </h2>
            <div class="flex items-center gap-2">
                @foreach(['7' => '7 Hari', '30' => '30 Hari', '90' => '90 Hari'] as $p => $label)
                    <a href="{{ route('admin.ad-analytics.index', ['period' => $p]) }}"
                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ $period === $p ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                {{-- Platform Ads --}}
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Platform Impressions</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($platformImpressions) }}</p>
                    <p class="text-xs text-gray-400 mt-1">CTR: {{ $platformCtr }}%</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Platform Clicks</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($platformClicks) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Revenue: Rp {{ number_format($platformRevenue, 0, ',', '.') }}</p>
                </div>
                {{-- Creator Ads --}}
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Creator Impressions</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($creatorImpressions) }}</p>
                    <p class="text-xs text-gray-400 mt-1">CTR: {{ $creatorCtr }}%</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Creator Clicks</p>
                    <p class="text-2xl font-bold text-violet-600 mt-1">{{ number_format($creatorClicks) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Revenue: Rp {{ number_format($creatorRevenue, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Impression Trend Chart (CSS-based) --}}
            <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Impression Trend ({{ $period }} Hari)</h3>
                @if($chartLabels->count() > 0)
                    <div class="flex items-end gap-1 h-48">
                        @foreach($chartLabels as $i => $date)
                            @php
                                $maxImpression = max($chartPlatformImpressions->max(), $chartCreatorImpressions->max(), 1);
                                $platformHeight = ($chartPlatformImpressions[$i] / $maxImpression) * 180;
                                $creatorHeight = ($chartCreatorImpressions[$i] / $maxImpression) * 180;
                            @endphp
                            <div class="flex-1 flex flex-col items-center gap-0.5" title="{{ $date }}">
                                <div class="w-full flex gap-0.5 items-end" style="height: 180px">
                                    <div class="flex-1 bg-blue-400 rounded-t" style="height: {{ max(2, $platformHeight) }}px"></div>
                                    <div class="flex-1 bg-violet-400 rounded-t" style="height: {{ max(2, $creatorHeight) }}px"></div>
                                </div>
                                @if($loop->index % 7 === 0)
                                    <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($date)->format('d') }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-4 mt-3 justify-center">
                        <div class="flex items-center gap-1"><div class="w-3 h-3 bg-blue-400 rounded"></div><span class="text-xs text-gray-500">Platform Ads</span></div>
                        <div class="flex items-center gap-1"><div class="w-3 h-3 bg-violet-400 rounded"></div><span class="text-xs text-gray-500">Creator Ads</span></div>
                    </div>
                @else
                    <p class="text-gray-400 text-sm text-center py-8">Belum ada data impression.</p>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Top Platform Ads --}}
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Platform Ads</h3>
                    @if($topPlatformAds->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-2 text-gray-500 font-medium">Judul</th>
                                        <th class="text-center py-2 text-gray-500 font-medium">Imp</th>
                                        <th class="text-center py-2 text-gray-500 font-medium">Click</th>
                                        <th class="text-center py-2 text-gray-500 font-medium">CTR</th>
                                        <th class="text-right py-2 text-gray-500 font-medium">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topPlatformAds as $ad)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-2 text-gray-900">{{ Str::limit($ad['title'], 25) }}</td>
                                        <td class="py-2 text-center text-gray-500">{{ number_format($ad['impressions']) }}</td>
                                        <td class="py-2 text-center text-gray-500">{{ number_format($ad['clicks']) }}</td>
                                        <td class="py-2 text-center">
                                            <span class="text-xs font-medium {{ $ad['ctr'] >= 2 ? 'text-green-600' : 'text-gray-500' }}">{{ $ad['ctr'] }}%</span>
                                        </td>
                                        <td class="py-2 text-right text-gray-500">Rp {{ number_format($ad['revenue'], 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-400 text-sm text-center py-4">Belum ada data.</p>
                    @endif
                </div>

                {{-- Top Creator Ads --}}
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Creator Ads</h3>
                    @if($topCreatorAds->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-2 text-gray-500 font-medium">Judul</th>
                                        <th class="text-center py-2 text-gray-500 font-medium">Imp</th>
                                        <th class="text-center py-2 text-gray-500 font-medium">Click</th>
                                        <th class="text-center py-2 text-gray-500 font-medium">CTR</th>
                                        <th class="text-right py-2 text-gray-500 font-medium">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topCreatorAds as $ad)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-2">
                                            <div class="text-gray-900">{{ Str::limit($ad['title'], 25) }}</div>
                                            <div class="text-xs text-gray-400">{{ $ad['creator'] }}</div>
                                        </td>
                                        <td class="py-2 text-center text-gray-500">{{ number_format($ad['impressions']) }}</td>
                                        <td class="py-2 text-center text-gray-500">{{ number_format($ad['clicks']) }}</td>
                                        <td class="py-2 text-center">
                                            <span class="text-xs font-medium {{ $ad['ctr'] >= 2 ? 'text-green-600' : 'text-gray-500' }}">{{ $ad['ctr'] }}%</span>
                                        </td>
                                        <td class="py-2 text-right text-gray-500">Rp {{ number_format($ad['revenue'], 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-400 text-sm text-center py-4">Belum ada data.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
