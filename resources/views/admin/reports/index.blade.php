<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Laporan Pengguna</h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6">
        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <div class="text-2xl font-bold text-orange-600">{{ $counts['pending'] }}</div>
                <div class="text-sm text-gray-500">Menunggu</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <div class="text-2xl font-bold text-blue-600">{{ $counts['reviewed'] }}</div>
                <div class="text-sm text-gray-500">Direview</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <div class="text-2xl font-bold text-green-600">{{ $counts['resolved'] }}</div>
                <div class="text-sm text-gray-500">Diselesaikan</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <div class="text-2xl font-bold text-gray-600">{{ $counts['dismissed'] }}</div>
                <div class="text-sm text-gray-500">Ditolak</div>
            </div>
        </div>

        {{-- Filter Tabs --}}
        <div class="flex gap-2 mb-6 flex-wrap">
            @foreach(['pending' => 'Menunggu', 'reviewed' => 'Direview', 'resolved' => 'Selesai', 'dismissed' => 'Ditolak', 'all' => 'Semua'] as $key => $label)
                <a href="{{ route('admin.reports.index', ['status' => $key]) }}"
                   class="px-4 py-2 text-sm font-medium rounded-full transition {{ $activeStatus === $key ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Reports List --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @forelse($reports as $report)
                <a href="{{ route('admin.reports.show', $report) }}" class="flex items-start gap-4 p-5 border-b border-gray-50 hover:bg-gray-50 transition {{ $loop->last ? 'border-b-0' : '' }}">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-medium text-sm text-gray-900">{{ $report->user->name }}</span>
                            <span class="text-xs text-gray-400">melaporkan</span>
                            <span class="font-medium text-sm text-gray-900">{{ Str::limit($report->simulation->title, 40) }}</span>
                        </div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ match($report->reason) { 'malware' => 'bg-red-100 text-red-700', 'spam_ads' => 'bg-orange-100 text-orange-700', 'inappropriate' => 'bg-yellow-100 text-yellow-700', default => 'bg-gray-100 text-gray-700' } }}">
                                {{ match($report->reason) { 'malware' => 'Malware', 'spam_ads' => 'Spam/Iklan', 'inappropriate' => 'Tidak Pantas', default => 'Lainnya' } }}
                            </span>
                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ match($report->status) { 'pending' => 'bg-orange-100 text-orange-700', 'reviewed' => 'bg-blue-100 text-blue-700', 'resolved' => 'bg-green-100 text-green-700', 'dismissed' => 'bg-gray-100 text-gray-500', default => 'bg-gray-100 text-gray-500' } }}">
                                {{ match($report->status) { 'pending' => 'Menunggu', 'reviewed' => 'Direview', 'resolved' => 'Selesai', 'dismissed' => 'Ditolak', default => $report->status } }}
                            </span>
                        </div>
                        @if($report->description)
                            <p class="text-xs text-gray-500 mt-1">{{ Str::limit($report->description, 100) }}</p>
                        @endif
                    </div>
                    <div class="text-xs text-gray-400 whitespace-nowrap">
                        {{ $report->created_at->diffForHumans() }}
                    </div>
                </a>
            @empty
                <div class="p-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-gray-500">Tidak ada laporan ditemukan.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $reports->links() }}</div>
    </div>
</x-app-layout>
