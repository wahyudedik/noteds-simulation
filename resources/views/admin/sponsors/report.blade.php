<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan: {{ $sponsor->company_name }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Summary Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_sponsorships'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total Sponsorship</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_spent'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total Belanja</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_impressions'] ?? 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total Impressions</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['ctr'] ?? 0, 2) }}%</p>
                    <p class="text-xs text-gray-500 mt-1">CTR Rata-rata</p>
                </div>
            </div>

            {{-- Per-Sponsorship Breakdown --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Detail per Sponsorship</h3>
                @if($sponsorships->count() > 0)
                    <div class="space-y-4">
                        @foreach($sponsorships as $sponsorship)
                            <div class="p-4 rounded-lg border border-gray-100 hover:bg-gray-50">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.sponsorships.show', $sponsorship) }}" class="font-medium text-gray-900 hover:text-blue-600 text-sm">{{ $sponsorship->title }}</a>
                                        <span class="inline-flex px-2 py-0.5 text-xs font-medium {{ $sponsorship->status_color }} rounded-full">{{ $sponsorship->status_label }}</span>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $sponsorship->start_date->format('d M Y') }} — {{ $sponsorship->end_date->format('d M Y') }}</span>
                                </div>
                                <div class="grid grid-cols-5 gap-4 text-xs">
                                    <div>
                                        <span class="text-gray-500">Budget</span>
                                        <p class="font-medium text-gray-900">Rp {{ number_format($sponsorship->budget, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Terkonsumsi</span>
                                        <p class="font-medium text-gray-900">Rp {{ number_format($sponsorship->spent, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Impressions</span>
                                        <p class="font-medium text-gray-900">{{ number_format($sponsorship->total_impressions) }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Clicks</span>
                                        <p class="font-medium text-gray-900">{{ number_format($sponsorship->total_clicks) }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">CTR</span>
                                        <p class="font-medium text-gray-900">{{ $sponsorship->ctr }}%</p>
                                    </div>
                                </div>
                                {{-- Budget Progress --}}
                                <div class="mt-2">
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $sponsorship->progress }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500 text-sm">Belum ada data sponsorship.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
