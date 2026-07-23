<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                Embed Management
            </h2>
            <div class="flex items-center gap-2">
                @foreach(['7' => '7 Hari', '30' => '30 Hari', '90' => '90 Hari'] as $p => $label)
                    <a href="{{ route('admin.embeds.index', ['period' => $p]) }}"
                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ $period === $p ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Stats --}}
            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Embed (Semua Waktu)</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalEmbeds) }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Embed ({{ $period }} Hari Terakhir)</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($periodEmbeds) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Top Embedded Simulations --}}
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Simulasi Ter-embed</h3>
                    @if($topSimulations->count() > 0)
                        <div class="space-y-3">
                            @foreach($topSimulations as $sim)
                                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ Str::limit($sim->title, 35) }}</p>
                                        <p class="text-xs text-gray-400">{{ $sim->user->name ?? '-' }}</p>
                                    </div>
                                    <span class="inline-flex px-2.5 py-0.5 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full">
                                        {{ number_format($sim->embed_count) }} embed
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-400 text-sm text-center py-4">Belum ada data embed.</p>
                    @endif
                </div>

                {{-- Top Referrers --}}
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Referrer Domains</h3>
                    @if($topReferrers->count() > 0)
                        <div class="space-y-3">
                            @foreach($topReferrers as $ref)
                                @php
                                    $maxCount = $topReferrers->max('count') ?: 1;
                                    $percentage = ($ref->count / $maxCount) * 100;
                                @endphp
                                <div class="flex items-center gap-3">
                                    <div class="w-40 text-sm text-gray-700 font-mono truncate">{{ $ref->referrer_domain }}</div>
                                    <div class="flex-1 bg-gray-100 rounded-full h-4 overflow-hidden">
                                        <div class="bg-indigo-500 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <div class="w-12 text-right text-sm text-gray-500">{{ $ref->count }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-400 text-sm text-center py-4">Belum ada data referrer.</p>
                    @endif
                </div>
            </div>

            {{-- Recent Embed Tracks --}}
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Embed Terbaru</h3>
                    @if($recentTracks->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Simulasi</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Referrer</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">IP</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTracks as $track)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-3 px-2">
                                            <span class="text-gray-900">{{ $track->simulation?->title ?? '-' }}</span>
                                        </td>
                                        <td class="py-3 px-2">
                                            <span class="font-mono text-xs text-gray-500">{{ $track->referrer_domain ?? '-' }}</span>
                                        </td>
                                        <td class="py-3 px-2 text-gray-400 text-xs font-mono">{{ $track->ip_address ?? '-' }}</td>
                                        <td class="py-3 px-2 text-gray-400 text-xs">{{ $track->created_at->diffForHumans() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $recentTracks->links() }}
                        </div>
                    @else
                        <p class="text-gray-400 text-sm text-center py-4">Belum ada embed tracks.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
