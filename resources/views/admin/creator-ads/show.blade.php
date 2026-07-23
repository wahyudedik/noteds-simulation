<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Detail Iklan Creator
            </h2>
            <a href="{{ route('admin.creator-ads.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Info Card --}}
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $creatorAd->simulation->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">Oleh: {{ $creatorAd->user->name }} &middot; {{ $creatorAd->created_at->diffForHumans() }}</p>
                    </div>
                    @switch($creatorAd->review_status)
                        @case('pending_review')
                            <span class="inline-flex px-3 py-1 text-sm font-medium bg-yellow-100 text-yellow-700 rounded-full">Menunggu Review</span>
                            @break
                        @case('approved')
                            <span class="inline-flex px-3 py-1 text-sm font-medium bg-green-100 text-green-700 rounded-full">Disetujui</span>
                            @break
                        @case('rejected')
                            <span class="inline-flex px-3 py-1 text-sm font-medium bg-red-100 text-red-700 rounded-full">Ditolak</span>
                            @break
                        @case('flagged')
                            <span class="inline-flex px-3 py-1 text-sm font-medium bg-orange-100 text-orange-700 rounded-full">Mencurigakan</span>
                            @break
                    @endswitch
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Provider:</span>
                        <span class="ml-1 font-medium text-gray-900">{{ ucfirst($creatorAd->provider) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Publisher ID:</span>
                        <span class="ml-1 font-medium text-gray-900 font-mono">{{ $creatorAd->publisher_id ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Simulasi ID:</span>
                        <span class="ml-1 font-medium text-gray-900">#{{ $creatorAd->simulation_id }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Active:</span>
                        <span class="ml-1 font-medium {{ $creatorAd->is_active ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $creatorAd->is_active ? 'Ya' : 'Tidak' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Ad Config --}}
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Konfigurasi Iklan</h4>
                <pre class="bg-gray-50 rounded-lg p-4 text-xs text-gray-700 overflow-x-auto">{{ json_encode($creatorAd->ad_config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>

            {{-- Code Snippet --}}
            @if($creatorAd->code_snippet)
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Kode Iklan</h4>
                <pre class="bg-gray-900 rounded-lg p-4 text-xs text-green-400 overflow-x-auto max-h-64">{{ e($creatorAd->code_snippet) }}</pre>
            </div>
            @endif

            {{-- Scan Result --}}
            @if($creatorAd->scan_result)
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Hasil Auto-Scan</h4>
                <pre class="bg-gray-50 rounded-lg p-4 text-xs text-gray-700 overflow-x-auto">{{ json_encode($creatorAd->scan_result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif

            {{-- Review Notes --}}
            @if($creatorAd->review_notes)
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Catatan Review</h4>
                <p class="text-sm text-gray-600">{{ $creatorAd->review_notes }}</p>
                @if($creatorAd->reviewer)
                    <p class="text-xs text-gray-400 mt-2">Oleh: {{ $creatorAd->reviewer->name }} &middot; {{ $creatorAd->reviewed_at?->diffForHumans() }}</p>
                @endif
            </div>
            @endif

            {{-- Action Buttons --}}
            @if($creatorAd->review_status === 'pending_review' || $creatorAd->review_status === 'flagged')
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-4">Aksi Review</h4>
                <div class="flex flex-wrap gap-3">
                    {{-- Approve --}}
                    <form action="{{ route('admin.creator-ads.approve', $creatorAd) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('Setujui iklan ini?')"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                            ✓ Setujui
                        </button>
                    </form>

                    {{-- Reject --}}
                    <form action="{{ route('admin.creator-ads.reject', $creatorAd) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        <input type="text" name="review_notes" placeholder="Alasan penolakan (opsional)" class="text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                        <button type="submit" onclick="return confirm('Tolak iklan ini?')"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                            ✗ Tolak
                        </button>
                    </form>

                    {{-- Flag --}}
                    <form action="{{ route('admin.creator-ads.flag', $creatorAd) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        <input type="text" name="review_notes" placeholder="Alasan flag (opsional)" class="text-sm border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                        <button type="submit" onclick="return confirm('Tandai mencurigakan?')"
                            class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition">
                            ⚑ Flag
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
