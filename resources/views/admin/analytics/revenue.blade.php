<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Revenue Analytics
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.analytics.index') }}" class="px-3 py-1.5 text-sm font-medium rounded-lg transition bg-gray-100 text-gray-600 hover:bg-gray-200">
                    ← Overview
                </a>
                @foreach(['7' => '7 Hari', '30' => '30 Hari', '90' => '90 Hari'] as $p => $label)
                    <a href="{{ route('admin.analytics.revenue', ['period' => $p]) }}"
                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ $period === $p ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Revenue Summary --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Revenue ({{ $period }} hari)</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Creator Ad Revenue</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">Rp {{ number_format($creatorAdRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Platform Ad Revenue</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">Rp {{ number_format($platformAdRevenue, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Payout Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Sudah Dibayar ({{ $period }} hari)</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Menunggu Payout</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">Rp {{ number_format($pendingPayouts, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Revenue by Tier --}}
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue per Tier</h3>
                    @if($revenueByTier->count() > 0)
                        <div class="space-y-4">
                            @php
                                $tierColors = ['basic' => 'bg-gray-400', 'verified' => 'bg-blue-500', 'expert' => 'bg-purple-500', 'platinum' => 'bg-yellow-500'];
                                $tierLabels = ['basic' => 'Basic', 'verified' => 'Verified', 'expert' => 'Expert', 'platinum' => 'Platinum'];
                                $maxTierRevenue = max(1, (int) $revenueByTier->max('total'));
                            @endphp
                            @foreach($revenueByTier as $tier)
                                @php $percentage = $totalRevenue > 0 ? round(($tier->total / $totalRevenue) * 100, 1) : 0; @endphp
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700">{{ $tierLabels[$tier->revenue_tier] ?? ucfirst($tier->revenue_tier) }}</span>
                                        <span class="text-sm text-gray-500">{{ number_format($tier->creators) }} creator · Rp {{ number_format($tier->total, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                        <div class="{{ $tierColors[$tier->revenue_tier] ?? 'bg-gray-500' }} h-full rounded-full transition-all" style="width: {{ $maxTierRevenue > 0 ? ($tier->total / $maxTierRevenue) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Belum ada data revenue per tier.</p>
                    @endif
                </div>

                {{-- Revenue Split --}}
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Split</h3>
                    @if($totalRevenue > 0)
                        @php
                            $creatorPercent = round(($creatorAdRevenue / $totalRevenue) * 100, 1);
                            $platformPercent = round(($platformAdRevenue / $totalRevenue) * 100, 1);
                        @endphp
                        <div class="space-y-6">
                            {{-- Bar chart --}}
                            <div class="flex items-center gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700">Creator</span>
                                        <span class="text-sm text-gray-500">{{ $creatorPercent }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-6 overflow-hidden">
                                        <div class="bg-blue-500 h-full rounded-full flex items-center justify-end pr-2" style="width: {{ max($creatorPercent, 5) }}%">
                                            @if($creatorPercent > 10)
                                                <span class="text-white text-xs font-medium">Rp {{ number_format($creatorAdRevenue, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700">Platform</span>
                                        <span class="text-sm text-gray-500">{{ $platformPercent }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-6 overflow-hidden">
                                        <div class="bg-purple-500 h-full rounded-full flex items-center justify-end pr-2" style="width: {{ max($platformPercent, 5) }}%">
                                            @if($platformPercent > 10)
                                                <span class="text-white text-xs font-medium">Rp {{ number_format($platformAdRevenue, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Belum ada data revenue.</p>
                    @endif
                </div>
            </div>

            {{-- Top Earning Simulations --}}
            <div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Top Simulasi Berdasarkan Revenue</h3>
                </div>
                @if($topEarningSimulations->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="text-left py-3 px-4 text-gray-500 font-medium w-10">#</th>
                                    <th class="text-left py-3 px-4 text-gray-500 font-medium">Judul</th>
                                    <th class="text-center py-3 px-4 text-gray-500 font-medium">Plays</th>
                                    <th class="text-right py-3 px-4 text-gray-500 font-medium">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($topEarningSimulations as $index => $sim)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="py-3 px-4 text-gray-500">{{ $index + 1 }}</td>
                                        <td class="py-3 px-4">
                                            <a href="{{ route('simulations.show', $sim->slug) }}" class="font-medium text-gray-900 hover:text-blue-600 transition" target="_blank">
                                                {{ Str::limit($sim->title, 50) }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-4 text-center text-gray-500">{{ number_format($sim->play_count) }}</td>
                                        <td class="py-3 px-4 text-right font-medium text-green-600">
                                            Rp {{ number_format($sim->creator_ads_sum_revenue ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500 text-sm">Belum ada simulasi yang menghasilkan revenue.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
