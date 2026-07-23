<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Review Iklan Creator
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Status Tabs --}}
            <div class="flex items-center gap-2 mb-6">
                @foreach([
                    'pending_review' => 'Menunggu Review',
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                    'flagged' => 'Mencurigakan',
                    'all' => 'Semua',
                ] as $key => $label)
                    <a href="{{ route('admin.creator-ads.index', ['status' => $key]) }}"
                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ $status === $key ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Creator Ads Table --}}
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($creatorAds->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Creator</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Simulasi</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Provider</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Publisher ID</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Status</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Tanggal</th>
                                        <th class="text-right py-3 px-2 text-gray-500 font-medium">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($creatorAds as $ad)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-3 px-2">
                                            <div class="font-medium text-gray-900">{{ $ad->user->name }}</div>
                                        </td>
                                        <td class="py-3 px-2 text-gray-500">{{ Str::limit($ad->simulation->title, 25) }}</td>
                                        <td class="py-3 px-2">
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">{{ ucfirst($ad->provider) }}</span>
                                        </td>
                                        <td class="py-3 px-2 text-gray-500 font-mono text-xs">{{ $ad->publisher_id ?? '-' }}</td>
                                        <td class="py-3 px-2 text-center">
                                            @switch($ad->review_status)
                                                @case('pending_review')
                                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Menunggu</span>
                                                    @break
                                                @case('approved')
                                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Disetujui</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700 rounded-full">Ditolak</span>
                                                    @break
                                                @case('flagged')
                                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-orange-100 text-orange-700 rounded-full">Mencurigakan</span>
                                                    @break
                                                @default
                                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-500 rounded-full">{{ $ad->review_status }}</span>
                                            @endswitch
                                        </td>
                                        <td class="py-3 px-2 text-gray-400 text-xs">{{ $ad->created_at->diffForHumans() }}</td>
                                        <td class="py-3 px-2 text-right">
                                            <a href="{{ route('admin.creator-ads.show', $ad) }}" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Detail</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $creatorAds->links() }}
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>Tidak ada iklan creator dengan status ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
