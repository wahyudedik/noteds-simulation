<x-studio-layout :pageTitle="'Riwayat Versi: ' . $simulation->title">
    <div class="max-w-4xl mx-auto">
        {{-- Back Link --}}
        <a href="{{ route('studio.simulations') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Kembali ke Simulasi
        </a>

        {{-- Current Version --}}
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Versi Saat Ini</h3>
                    <p class="text-sm text-gray-500 mt-1">v{{ $simulation->version ?? '1.0.0' }} · Diperbarui {{ $simulation->updated_at->diffForHumans() }}</p>
                </div>
                <a href="{{ route('studio.simulations.edit', $simulation->slug) }}" class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                    Upload Versi Baru
                </a>
            </div>
        </div>

        {{-- Version History --}}
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Riwayat Versi</h3>
            </div>

            @if($versions->count() > 0)
                <div class="divide-y divide-gray-50">
                    @foreach($versions as $version)
                        <div class="px-6 py-4 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">v{{ $version->version }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $version->created_at->diffForHumans() }} · {{ $version->created_at->format('d M Y H:i') }}</p>
                                        @if($version->changelog)
                                            <p class="text-sm text-gray-600 mt-2">{{ $version->changelog }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if($version->zip_path)
                                    <span class="text-xs text-gray-400 shrink-0">
                                        {{ basename($version->zip_path) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-6 py-4">
                    {{ $versions->links() }}
                </div>
            @else
                <div class="text-center py-12 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm">Belum ada riwayat versi.</p>
                    <p class="text-xs text-gray-400 mt-1">Versi akan tercatat saat Anda mengupload ZIP baru.</p>
                </div>
            @endif
        </div>
    </div>
</x-studio-layout>
