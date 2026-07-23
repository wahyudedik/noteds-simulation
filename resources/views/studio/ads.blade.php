<x-studio-layout :pageTitle="'Iklan Simulasi'">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Iklan: {{ $simulation->title }}</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola iklan yang ditampilkan dalam simulasi ini</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('studio.ads-revenue') }}" class="text-sm text-blue-600 hover:underline">Lihat Revenue →</a>
            <a href="{{ route('studio.simulations') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
        </div>
    </div>

    {{-- Reputation Info --}}
    @if($reputation)
        <div class="bg-white border border-gray-100 rounded-xl p-4 mb-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Reputasi Creator</p>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 rounded-full" style="width: {{ $reputation->score }}%"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ $reputation->score }}/100</span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Tier Reputasi</p>
                    <p class="text-sm font-semibold text-gray-900 mt-1">{{ ucfirst($reputation->revenue_tier ?? 'basic') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Add New Ad Form --}}
    <div class="bg-white border border-gray-100 rounded-xl p-6 mb-6 shadow-sm">
        <h3 class="font-semibold text-gray-900 mb-4">Tambah Iklan Baru</h3>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3 mb-4">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('studio.simulations.ads.store', $simulation->slug) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Provider --}}
                <div>
                    <label for="provider" class="block text-sm font-medium text-gray-700 mb-1">Provider Iklan</label>
                    <select name="provider" id="provider" x-data x-model="$el.value" class="w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="adsense">Google AdSense</option>
                        <option value="mediavine">Mediavine</option>
                        <option value="adthrive">AdThrive</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>

                {{-- Publisher ID --}}
                <div>
                    <label for="publisher_id" class="block text-sm font-medium text-gray-700 mb-1">Publisher / Ad Slot ID</label>
                    <input type="text" name="publisher_id" id="publisher_id" value="{{ old('publisher_id') }}"
                           class="w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="ca-pub-1234567890" required />
                </div>

                {{-- Ad Type --}}
                <div>
                    <label for="ad_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Iklan</label>
                    <select name="ad_type" id="ad_type" x-data x-model="$el.value" class="w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="banner">Banner</option>
                        <option value="native">Native Ad</option>
                        <option value="code_snippet">Custom Code Snippet</option>
                    </select>
                </div>

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul (opsional)</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}"
                           class="w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Judul iklan" />
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (opsional)</label>
                    <input type="text" name="description" id="description" value="{{ old('description') }}"
                           class="w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Deskripsi singkat iklan" />
                </div>

                {{-- Image URL --}}
                <div>
                    <label for="image_url" class="block text-sm font-medium text-gray-700 mb-1">URL Gambar (opsional)</label>
                    <input type="url" name="image_url" id="image_url" value="{{ old('image_url') }}"
                           class="w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="https://example.com/banner.jpg" />
                </div>

                {{-- Target URL --}}
                <div>
                    <label for="target_url" class="block text-sm font-medium text-gray-700 mb-1">URL Tujuan (opsional)</label>
                    <input type="url" name="target_url" id="target_url" value="{{ old('target_url') }}"
                           class="w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="https://example.com" />
                </div>

                {{-- Code Snippet --}}
                <div class="md:col-span-2">
                    <label for="code_snippet" class="block text-sm font-medium text-gray-700 mb-1">Code Snippet (opsional)</label>
                    <textarea name="code_snippet" id="code_snippet" rows="4"
                              class="w-full border-gray-300 rounded-lg text-sm font-mono focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Kode HTML/JS iklan...">{{ old('code_snippet') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Kode akan di-scan oleh sistem keamanan sebelum disetujui.</p>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Ajukan Iklan
                </button>
            </div>
        </form>
    </div>

    {{-- Existing Creator Ads --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Iklan yang Sudah Diajukan</h3>
        </div>

        @if($creatorAds->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="text-left py-3 px-4 text-gray-500 font-medium">Provider</th>
                            <th class="text-left py-3 px-4 text-gray-500 font-medium">Publisher ID</th>
                            <th class="text-left py-3 px-4 text-gray-500 font-medium">Tipe</th>
                            <th class="text-center py-3 px-4 text-gray-500 font-medium">Status</th>
                            <th class="text-center py-3 px-4 text-gray-500 font-medium">Impressions</th>
                            <th class="text-right py-3 px-4 text-gray-500 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($creatorAds as $ad)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 text-gray-900">{{ ucfirst($ad->provider) }}</td>
                                <td class="py-3 px-4 text-gray-500 font-mono text-xs">{{ $ad->publisher_id }}</td>
                                <td class="py-3 px-4 text-gray-500">{{ ucfirst($ad->ad_config['type'] ?? '-') }}</td>
                                <td class="py-3 px-4 text-center">
                                    @if($ad->review_status === 'approved')
                                        <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Approved</span>
                                    @elseif($ad->review_status === 'pending_review')
                                        <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Review</span>
                                    @elseif($ad->review_status === 'rejected')
                                        <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700 rounded-full">Ditolak</span>
                                    @elseif($ad->review_status === 'flagged')
                                        <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-orange-100 text-orange-700 rounded-full">Flagged</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">{{ $ad->review_status }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center text-gray-500">{{ number_format($ad->impressions ?? 0) }}</td>
                                <td class="py-3 px-4 text-right">
                                    @if($ad->review_status !== 'approved')
                                        <form action="{{ route('studio.simulations.ads.destroy', [$simulation->slug, $ad->id]) }}" method="POST" class="inline"
                                              x-data x-on:submit.prevent="if(confirm('Hapus iklan ini?')) $el.submit()">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <p>Belum ada iklan untuk simulasi ini.</p>
            </div>
        @endif
    </div>

    {{-- Revenue Tiers Info --}}
    <div class="mt-6 bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <h3 class="font-semibold text-gray-900 mb-4">Revenue Sharing Tiers</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach($revenueTiers as $key => $tier)
                <div class="border rounded-lg p-3 {{ ($reputation->revenue_tier ?? 'basic') === $key ? 'border-blue-300 bg-blue-50' : 'border-gray-200' }}">
                    <p class="text-sm font-semibold text-gray-900">{{ $tier['name'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $tier['creator_share'] }}% Creator / {{ $tier['platform_share'] }}% Platform</p>
                    <p class="text-[10px] text-gray-400 mt-1">{{ $tier['requirements'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</x-studio-layout>
