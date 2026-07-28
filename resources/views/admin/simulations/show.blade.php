<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                <svg class="w-5 h-5 inline-block mr-1 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ Str::limit($simulation->title, 50) }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.simulations.edit', $simulation) }}" class="px-3 py-1.5 bg-yellow-100 dark:bg-yellow-900/30 hover:bg-yellow-200 text-yellow-700 text-sm font-medium rounded-lg transition">Edit</a>
                <a href="{{ route('simulations.show', $simulation->slug) }}" target="_blank" class="px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 text-blue-700 text-sm font-medium rounded-lg transition">Lihat Publik ?</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Stats --}}
                <div class="md:col-span-1 space-y-4">
                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-5 shadow-sm">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Statistik</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Views</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($simulation->view_count) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Plays</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($simulation->play_count) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Likes</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($simulation->like_count) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Bookmarks</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($simulation->bookmark_count) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Shares</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($simulation->share_count) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-5 shadow-sm">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Info</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Status</span>
                                @if($simulation->is_published)
                                    <span class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-full">Published</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 rounded-full">Draft</span>
                                @endif
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Versi</span>
                                <span class="text-gray-900 dark:text-white">v{{ $simulation->version }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Kategori</span>
                                <span class="text-gray-900 dark:text-white">{{ $simulation->category }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Dibuat</span>
                                <span class="text-gray-900 dark:text-white">{{ $simulation->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Toggle Publish --}}
                    <form action="{{ route('admin.simulations.toggle-publish', $simulation) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 text-sm font-medium rounded-lg transition
                            {{ $simulation->is_published ? 'bg-yellow-100 dark:bg-yellow-900/30 hover:bg-yellow-200 text-yellow-700' : 'bg-green-100 dark:bg-green-900/30 hover:bg-green-200 text-green-700' }}">
                            {{ $simulation->is_published ? 'Unpublish' : 'Publish' }}
                        </button>
                    </form>
                </div>

                {{-- Details --}}
                <div class="md:col-span-2">
                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ $simulation->title }}</h3>

                        @if($simulation->description)
                            <div class="prose prose-sm max-w-none">
                                <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $simulation->description }}</p>
                            </div>
                        @else
                            <p class="text-gray-400 dark:text-gray-500 italic">Tidak ada deskripsi.</p>
                        @endif

                        @if($simulation->tags)
                            <div class="flex flex-wrap gap-2 mt-4">
                                @foreach($simulation->tags_array as $tag)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 dark:text-gray-400 text-xs rounded-full">#{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <strong>Slug:</strong> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">{{ $simulation->slug }}</code>
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                <strong>Entry Point:</strong> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">{{ $simulation->entry_point }}</code>
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                <strong>File:</strong> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">{{ basename($simulation->zip_path) }}</code>
                            </p>
                        </div>
                    </div>

                    {{-- Delete --}}
                    <div class="mt-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-5 shadow-sm">
                        <h4 class="text-sm font-medium text-red-600 dark:text-red-400 mb-2">Zona Berbahaya</h4>
                        <form action="{{ route('admin.simulations.destroy', $simulation) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmSubmit(this.closest('form'), 'Yakin ingin menghapus experience ini? Tindakan ini tidak dapat dibatalkan.', { title: 'Hapus Experience', confirmText: 'Ya, Hapus' })" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                Hapus Experience
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
