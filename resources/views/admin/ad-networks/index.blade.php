<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                Pengaturan Ad Network
            </h2>
            <a href="{{ route('admin.ad-networks.ads-txt') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Kelola ads.txt
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-sm text-blue-700 dark:text-blue-300">
                        <p class="font-medium">Tentang Ad Network Settings</p>
                        <p class="mt-1">Konfigurasi ad network global. Iklan hanya dalam format banner & native — pop-under dinonaktifkan demi kenyamanan pengguna. <strong>{{ $enabledCount }}</strong> dari {{ $networks->count() }} network aktif.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($networks as $network)
                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-6 shadow-sm {{ $network->is_enabled ? 'ring-2 ring-green-500/30' : '' }}">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $network->display_name }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $network->network }}</p>
                            </div>
                            @if($network->is_enabled)
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-full">Aktif</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 rounded-full">Nonaktif</span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Publisher ID</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 font-mono">{{ $network->publisher_id ?: '—' }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Site ID</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 font-mono">{{ $network->site_id ?: '—' }}</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Est. RPM</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">${{ number_format($network->estimated_rpm, 2) }}</p>
                        </div>

                        <div class="flex flex-wrap gap-1 mb-4">
                            @if($network->allow_banner)
                                <span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400 rounded">Banner ✓</span>
                            @endif
                            @if($network->allow_native)
                                <span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400 rounded">Native ✓</span>
                            @endif
                            @if($network->allow_interstitial)
                                <span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400 rounded">Interstitial ⚠</span>
                            @endif
                            @if($network->allow_popunder)
                                <span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 rounded">Pop-under ✗</span>
                            @endif
                        </div>

                        <div class="grid grid-cols-3 gap-2 mb-4 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <div>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400">Impressions</p>
                                <p class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ number_format($network->total_impressions) }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400">Klik</p>
                                <p class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ number_format($network->total_clicks) }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400">Revenue</p>
                                <p class="text-xs font-medium text-gray-700 dark:text-gray-300">$${{ number_format($network->total_revenue, 2) }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.ad-networks.edit', $network) }}" class="flex-1 text-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition">
                                Pengaturan
                            </a>
                            <form action="{{ route('admin.ad-networks.toggle', $network) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 {{ $network->is_enabled ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600' }} text-white text-xs font-medium rounded-lg transition">
                                    {{ $network->is_enabled ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </div>

                        @if($network->notes)
                            <p class="mt-3 text-[11px] text-gray-400 dark:text-gray-500 italic">{{ $network->notes }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
