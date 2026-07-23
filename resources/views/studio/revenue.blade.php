<x-studio-layout :pageTitle="'Revenue Iklan'">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Revenue Iklan</h2>
            <p class="text-sm text-gray-500 mt-1">Pendapatan dari iklan creator yang Anda ajukan</p>
        </div>
        <a href="{{ route('studio.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
    </div>

    {{-- Revenue Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500">Tier Saat Ini</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $tier['tier_name'] ?? $tier['name'] ?? ucfirst($reputation->revenue_tier ?? 'basic') }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $tier['creator'] ?? $tier['creator_share'] ?? 55 }}% creator / {{ $tier['platform'] ?? $tier['platform_share'] ?? 45 }}% platform</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total Impressions</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalImpressions) }}</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500">Estimasi Pendapatan</p>
            <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Min payout: Rp 500.000</p>
        </div>
    </div>

    {{-- Reputation Card --}}
    @if($reputation)
        <div class="bg-white border border-gray-100 rounded-xl p-6 mb-6 shadow-sm">
            <h3 class="font-semibold text-gray-900 mb-4">Reputasi Creator</h3>
            <div class="flex items-center gap-6">
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-500">Skor Reputasi</span>
                        <span class="text-sm font-medium text-gray-900">{{ $reputation->score }}/100</span>
                    </div>
                    <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500
                            {{ $reputation->score >= 80 ? 'bg-green-500' : ($reputation->score >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                             style="width: {{ $reputation->score }}%"></div>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Tier</p>
                    <p class="text-lg font-bold text-gray-900">{{ ucfirst($reputation->revenue_tier ?? 'basic') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Revenue Tiers Table --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Semua Revenue Tiers</h3>
            <p class="text-xs text-gray-500 mt-1">Peningkatan tier berdasarkan jumlah simulasi dan rating rata-rata</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">Tier</th>
                        <th class="text-center py-3 px-4 text-gray-500 font-medium">Creator Share</th>
                        <th class="text-center py-3 px-4 text-gray-500 font-medium">Platform Share</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">Persyaratan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tiers as $key => $tierData)
                        <tr class="border-b border-gray-100 {{ ($reputation->revenue_tier ?? 'basic') === $key ? 'bg-blue-50' : '' }}">
                            <td class="py-3 px-4">
                                <span class="font-medium text-gray-900">{{ $tierData['name'] }}</span>
                                @if(($reputation->revenue_tier ?? 'basic') === $key)
                                    <span class="ml-2 text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-full">Aktif</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center font-medium text-green-600">{{ $tierData['creator_share'] }}%</td>
                            <td class="py-3 px-4 text-center text-gray-500">{{ $tierData['platform_share'] }}%</td>
                            <td class="py-3 px-4 text-xs text-gray-500">{{ $tierData['requirements'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-studio-layout>
