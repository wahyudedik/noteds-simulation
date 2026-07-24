<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Detail Creator: {{ $creator->name }}
            </h2>
            <a href="{{ route('admin.creators.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Creator Info --}}
            <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    @if($creator->avatar)
                        <img src="{{ Storage::disk('public')->url($creator->avatar) }}" alt="" class="w-16 h-16 rounded-full object-cover" />
                    @else
                        <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl font-bold">{{ strtoupper(substr($creator->name, 0, 1)) }}</div>
                    @endif
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $creator->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $creator->email }}</p>
                        <p class="text-xs text-gray-400 mt-1">Bergabung: {{ $creator->created_at->format('d M Y') }}</p>
                        @if($creator->bio)
                            <p class="text-sm text-gray-600 mt-2">{{ $creator->bio }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        @if($creator->reputation && $creator->reputation->score < 20)
                            <span class="inline-flex px-3 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full mb-2">Suspended</span>
                        @else
                            <span class="inline-flex px-3 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full mb-2">Aktif</span>
                        @endif
                        <div class="flex gap-2 mt-2 justify-end">
                            <form action="{{ route('admin.creators.toggle-suspend', $creator) }}" method="POST"
                                  x-data x-on:submit.prevent="if(confirm('Yakin ingin {{ $creator->reputation && $creator->reputation->score < 20 ? 'mengaktifkan' : 'menangguhkan' }} creator ini?')) $el.submit()">
                                @csrf
                                <button type="submit" class="text-xs font-medium px-3 py-1.5 rounded-lg transition
                                    {{ $creator->reputation && $creator->reputation->score < 20 ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                    {{ $creator->reputation && $creator->reputation->score < 20 ? 'Aktifkan' : 'Tangguhkan' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Simulasi</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ $stats['simulations'] }}</p>
                    <p class="text-[10px] text-gray-400">{{ $stats['published'] }} published</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Total Views</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_views']) }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Total Plays</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_plays']) }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Avg Rating</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($stats['avg_rating'], 1) }}/5</p>
                </div>
            </div>

            {{-- Reputation & Revenue --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Reputation --}}
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                    <h4 class="font-semibold text-gray-900 mb-4">Reputasi</h4>
                    @if($creator->reputation)
                        <div class="space-y-3">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm text-gray-500">Skor</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $creator->reputation->score }}/100</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all {{ $creator->reputation->score >= 80 ? 'bg-green-500' : ($creator->reputation->score >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                         style="width: {{ $creator->reputation->score }}%"></div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div><span class="text-gray-500">Tier:</span> <span class="font-medium">{{ ucfirst($creator->reputation->revenue_tier) }}</span></div>
                                <div><span class="text-gray-500">Uploads:</span> <span class="font-medium">{{ $creator->reputation->total_uploads }}</span></div>
                                <div><span class="text-gray-500">Approved:</span> <span class="font-medium text-green-600">{{ $creator->reputation->approved_count }}</span></div>
                                <div><span class="text-gray-500">Rejected:</span> <span class="font-medium text-red-600">{{ $creator->reputation->rejected_count }}</span></div>
                                <div><span class="text-gray-500">Flagged:</span> <span class="font-medium text-orange-600">{{ $creator->reputation->flagged_count }}</span></div>
                                <div><span class="text-gray-500">Reports:</span> <span class="font-medium">{{ $creator->reputation->reports_received }}</span></div>
                            </div>

                            {{-- Update Reputasi --}}
                            <form action="{{ route('admin.creators.update-reputation', $creator) }}" method="POST" class="mt-4 pt-4 border-t border-gray-100">
                                @csrf @method('PUT')
                                <label class="block text-sm font-medium text-gray-700 mb-1">Update Skor Reputasi</label>
                                <div class="flex gap-2">
                                    <input type="number" name="score" value="{{ $creator->reputation->score }}" min="0" max="100"
                                           class="flex-1 border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" />
                                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">Update</button>
                                </div>
                            </form>
                        </div>
                    @else
                        <p class="text-sm text-gray-400">Belum ada data reputasi.</p>
                    @endif
                </div>

                {{-- Revenue --}}
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                    <h4 class="font-semibold text-gray-900 mb-4">Revenue & Iklan</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Total Ad Revenue</span><span class="font-medium">Rp {{ number_format($stats['total_ad_revenue'], 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Total Iklan</span><span class="font-medium">{{ $stats['total_ads'] }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Approved Ads</span><span class="font-medium text-green-600">{{ $stats['approved_ads'] }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Pending Ads</span><span class="font-medium text-yellow-600">{{ $stats['pending_ads'] }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Followers</span><span class="font-medium">{{ $stats['followers'] }}</span></div>
                    </div>

                    {{-- Revenue Tiers --}}
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Revenue Tiers</h5>
                        <div class="space-y-1">
                            @foreach($revenueTiers as $key => $tier)
                                <div class="flex justify-between text-xs {{ ($creator->reputation->revenue_tier ?? 'basic') === $key ? 'font-semibold text-blue-600' : 'text-gray-500' }}">
                                    <span>{{ $tier['name'] }} {{ ($creator->reputation->revenue_tier ?? 'basic') === $key ? '(Aktif)' : '' }}</span>
                                    <span>{{ $tier['creator_share'] }}% / {{ $tier['platform_share'] }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Simulations --}}
            <div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm">
                <div class="p-6 border-b border-gray-100">
                    <h4 class="font-semibold text-gray-900">Simulasi Terbaru</h4>
                </div>
                @if($recentSimulations->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="text-left py-3 px-4 text-gray-500 font-medium">Judul</th>
                                    <th class="text-center py-3 px-4 text-gray-500 font-medium">Views</th>
                                    <th class="text-center py-3 px-4 text-gray-500 font-medium">Plays</th>
                                    <th class="text-center py-3 px-4 text-gray-500 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSimulations as $sim)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-3 px-4">
                                            <a href="{{ route('admin.simulations.show', $sim) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ Str::limit($sim->title, 40) }}</a>
                                        </td>
                                        <td class="py-3 px-4 text-center text-gray-500">{{ number_format($sim->view_count) }}</td>
                                        <td class="py-3 px-4 text-center text-gray-500">{{ number_format($sim->play_count) }}</td>
                                        <td class="py-3 px-4 text-center">
                                            @if($sim->is_published)
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Published</span>
                                            @else
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500 text-sm">Belum ada simulasi.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
