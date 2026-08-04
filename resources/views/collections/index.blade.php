<x-app-layout x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 500)">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-lg sm:text-xl text-gray-800 dark:text-white leading-tight truncate">
                <svg class="inline w-5 h-5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Collection
            </h2>
            <a href="{{ route('collections.create') }}" class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="hidden sm:inline">Buat Collection</span>
                <span class="sm:hidden">Buat Baru</span>
            </a>
        </div>
    </x-slot>

    {{-- Loading Skeleton --}}
    <template x-if="loading">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-4"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32 animate-pulse"></div></div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @for($i = 0; $i < 6; $i++)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden animate-pulse">
                            <div class="aspect-video bg-gray-200 dark:bg-gray-700"></div>
                            <div class="p-4 space-y-3">
                                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </template>

    <div class="py-12" x-show="!loading" x-transition>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[['label' => 'Collection Saya']]" />

            @if($collections->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($collections as $collection)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition">
                            {{-- Thumbnail --}}
                            <div class="aspect-video bg-gradient-to-br from-blue-500 to-purple-600 relative">
                                @if($collection->thumbnail)
                                    <img src="{{ Storage::disk('public')->url($collection->thumbnail) }}" alt="{{ $collection->title }}" class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    </div>
                                @endif
                                <div class="absolute top-2 right-2">
                                    @if($collection->is_public)
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs font-medium rounded-full">Publik</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 text-xs font-medium rounded-full">Privat</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Info --}}
                            <div class="p-4">
                                <a href="{{ route('collections.show', $collection->slug) }}" class="text-gray-900 dark:text-white font-semibold text-sm hover:text-blue-600 transition line-clamp-1">
                                    {{ $collection->title }}
                                </a>
                                <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">{{ $collection->simulations_count }} experience &middot; {{ $collection->formatted_view_count }} dilihat</p>
                                @if($collection->description)
                                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-2 line-clamp-2">{{ $collection->description }}</p>
                                @endif

                                <div class="flex items-center gap-2 mt-3">
                                    <a href="{{ route('collections.edit', $collection) }}" class="flex-1 text-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-lg transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('collections.destroy', $collection) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmSubmit(this.closest('form'), 'Hapus collection ini?')" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 text-xs font-medium rounded-lg transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $collections->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <h3 class="text-gray-500 dark:text-gray-400 text-lg font-medium">Belum ada collection</h3>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">Buat collection untuk mengorganisasi experience favorit Anda.</p>
                    <a href="{{ route('collections.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        Buat Collection Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
