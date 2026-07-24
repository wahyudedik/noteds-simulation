<x-studio-layout :pageTitle="'Simulasi Saya'">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-2">
            <a href="{{ route('studio.simulations') }}?status=all"
               class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ $status === 'all' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                Semua
            </a>
            <a href="{{ route('studio.simulations') }}?status=published"
               class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ $status === 'published' ? 'bg-green-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                Published
            </a>
            <a href="{{ route('studio.simulations') }}?status=draft"
               class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ $status === 'draft' ? 'bg-yellow-500 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                Draft
            </a>
        </div>
        <a href="{{ route('studio.simulations.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Upload Baru
        </a>
    </div>

    {{-- Simulations Grid --}}
    @if($simulations->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($simulations as $sim)
                <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                    {{-- Thumbnail --}}
                    <div class="aspect-video bg-gray-100 relative">
                        @if($sim->thumbnail)
                            <img src="{{ Storage::disk('public')->url($sim->thumbnail) }}" alt="{{ $sim->title }}" class="w-full h-full object-cover" />
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600">
                                <svg class="w-12 h-12 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                            </div>
                        @endif
                        {{-- Status Badge --}}
                        <div class="absolute top-2 left-2">
                            @if($sim->is_published)
                                <span class="px-2 py-0.5 text-xs font-medium bg-green-500 text-white rounded-full shadow">Published</span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-medium bg-yellow-500 text-white rounded-full shadow">Draft</span>
                            @endif
                        </div>
                        {{-- Version Badge --}}
                        @if($sim->version)
                            <div class="absolute top-2 right-2">
                                <span class="px-2 py-0.5 text-xs font-medium bg-black/50 text-white rounded-full backdrop-blur-sm">v{{ $sim->version }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 mb-2">{{ $sim->title }}</h3>
                        <div class="flex items-center gap-3 text-xs text-gray-500 mb-3">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                {{ number_format($sim->view_count) }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /></svg>
                                {{ number_format($sim->play_count) }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                {{ $sim->comment_count ?? 0 }}
                            </span>
                        </div>

                        {{-- Tags --}}
                        @if($sim->tagModels->count() > 0)
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach($sim->tagModels->take(3) as $tag)
                                    <span class="px-1.5 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">{{ $tag->name }}</span>
                                @endforeach
                                @if($sim->tagModels->count() > 3)
                                    <span class="px-1.5 py-0.5 text-xs bg-gray-100 text-gray-500 rounded">+{{ $sim->tagModels->count() - 3 }}</span>
                                @endif
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                            <a href="{{ route('studio.simulations.edit', $sim->slug) }}" class="flex-1 text-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Edit</a>
                            <a href="{{ route('studio.simulations.analytics', $sim->slug) }}" class="flex-1 text-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition">Analytics</a>
                            <a href="{{ route('studio.simulations.versions', $sim->slug) }}" class="flex-1 text-center px-3 py-1.5 text-xs font-medium text-purple-600 bg-purple-50 hover:bg-purple-100 rounded-lg transition">Versi</a>
                            @if($sim->is_published)
                                <a href="{{ route('simulations.show', $sim->slug) }}" target="_blank" class="px-3 py-1.5 text-xs font-medium text-gray-400 hover:text-blue-600 transition" title="Lihat di situs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                </a>
                            @endif
                            <form method="POST" action="{{ route('studio.simulations.destroy', $sim->slug) }}" id="delete-sim-{{ $sim->slug }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmSubmit(this.closest('form'), 'Yakin ingin menghapus simulasi ini? Tindakan ini tidak dapat dibatalkan.', { title: 'Hapus Simulasi', confirmText: 'Ya, Hapus' })" class="px-2 py-1.5 text-xs text-gray-400 hover:text-red-500 transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $simulations->links() }}
        </div>
    @else
        <div class="text-center py-16 bg-white border border-gray-100 rounded-xl shadow-sm">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada simulasi</h3>
            <p class="text-sm text-gray-500 mb-4">Mulai upload simulasi pertama Anda.</p>
            <a href="{{ route('studio.simulations.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Upload Simulasi
            </a>
        </div>
    @endif
</x-studio-layout>
