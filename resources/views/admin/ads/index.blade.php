<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                Kelola Iklan Platform
            </h2>
            <a href="{{ route('admin.ads.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Iklan Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Stats Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Total Iklan</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ $stats['total_ads'] }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Aktif</p>
                    <p class="text-xl font-bold text-green-600 mt-1">{{ $stats['active_ads'] }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Impressions</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_impressions']) }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Klik</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_clicks']) }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <p class="text-xs text-gray-500">CTR</p>
                    <p class="text-xl font-bold text-blue-600 mt-1">{{ $stats['ctr'] }}%</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Revenue</p>
                    <p class="text-xl font-bold text-emerald-600 mt-1">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm mb-6">
                <form method="GET" action="{{ route('admin.ads.index') }}" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Posisi</label>
                        <select name="position" class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Posisi</option>
                            <option value="header" {{ request('position') === 'header' ? 'selected' : '' }}>Header</option>
                            <option value="sidebar" {{ request('position') === 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                            <option value="pre_roll" {{ request('position') === 'pre_roll' ? 'selected' : '' }}>Pre-Roll</option>
                            <option value="mid_roll" {{ request('position') === 'mid_roll' ? 'selected' : '' }}>Mid-Roll</option>
                            <option value="post_simulation" {{ request('position') === 'post_simulation' ? 'selected' : '' }}>Post-Simulation</option>
                            <option value="feed_sponsored" {{ request('position') === 'feed_sponsored' ? 'selected' : '' }}>Feed Sponsored</option>
                            <option value="search_sponsored" {{ request('position') === 'search_sponsored' ? 'selected' : '' }}>Search Sponsored</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Tipe</label>
                        <select name="type" class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Tipe</option>
                            <option value="banner" {{ request('type') === 'banner' ? 'selected' : '' }}>Banner</option>
                            <option value="interstitial" {{ request('type') === 'interstitial' ? 'selected' : '' }}>Interstitial</option>
                            <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Video</option>
                            <option value="native" {{ request('type') === 'native' ? 'selected' : '' }}>Native</option>
                            <option value="adsense" {{ request('type') === 'adsense' ? 'selected' : '' }}>AdSense</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="active_only" value="1" {{ request('active_only') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                        <label class="text-sm text-gray-600">Hanya Aktif</label>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">Filter</button>
                </form>
            </div>

            {{-- Ads Table --}}
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($ads->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Judul</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Tipe</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Posisi</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Impressions</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Klik</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">CTR</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Status</th>
                                        <th class="text-right py-3 px-2 text-gray-500 font-medium">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ads as $ad)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-3 px-2 font-medium text-gray-900">{{ Str::limit($ad->title, 30) }}</td>
                                        <td class="py-3 px-2 text-gray-500">
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">{{ ucfirst($ad->type) }}</span>
                                        </td>
                                        <td class="py-3 px-2 text-gray-500">{{ str_replace('_', ' ', ucfirst($ad->position)) }}</td>
                                        <td class="py-3 px-2 text-center text-gray-500">{{ number_format($ad->impressions) }}</td>
                                        <td class="py-3 px-2 text-center text-gray-500">{{ number_format($ad->clicks) }}</td>
                                        <td class="py-3 px-2 text-center text-gray-500">{{ $ad->ctr }}%</td>
                                        <td class="py-3 px-2 text-center">
                                            @if($ad->is_active)
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Aktif</span>
                                            @else
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-500 rounded-full">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-2 text-right space-x-2">
                                            <a href="{{ route('admin.ads.edit', $ad) }}" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Edit</a>
                                            <form action="{{ route('admin.ads.toggle', $ad) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-yellow-600 hover:text-yellow-700 text-xs font-medium">
                                                    {{ $ad->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.ads.destroy', $ad) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmSubmit(this.closest('form'), 'Yakin hapus iklan ini?')" class="text-red-600 hover:text-red-700 text-xs font-medium">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $ads->links() }}
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            <p>Belum ada iklan platform.</p>
                            <a href="{{ route('admin.ads.create') }}" class="mt-2 inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Buat Iklan Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- confirmSubmit() is globally available via app.js --}}
</x-app-layout>
