<x-app-layout x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 500)">
    <x-slot name="header">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Komunitas</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Diskusi, berbagi, dan belajar bersama.</p>
                </div>
                @auth
                    <a href="{{ route('forum.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Buat Thread
                    </a>
                @endauth
            </div>
        </div>
    </x-slot>

    {{-- Loading Skeleton --}}
    <template x-if="loading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="mb-6"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24 animate-pulse"></div></div>
            <div class="lg:grid lg:grid-cols-4 lg:gap-8">
                <div class="lg:col-span-3">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
                        @foreach(range(1, 3) as $i)
                            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 text-center">
                                <div class="h-7 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-12 mx-auto mb-2"></div>
                                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-16 mx-auto"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-1 mb-6">
                        <div class="flex gap-1">
                            @foreach(range(1, 3) as $i)
                                <div class="flex-1 h-9 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                            @endforeach
                        </div>
                    </div>
                    <div class="space-y-3">
                        @foreach(range(1, 4) as $i)
                            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 animate-pulse flex-shrink-0"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-3/4"></div>
                                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-full"></div>
                                        <div class="flex gap-4 mt-2">
                                            <div class="h-2.5 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-16"></div>
                                            <div class="h-2.5 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-12"></div>
                                            <div class="h-2.5 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-14"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mt-8 lg:mt-0 space-y-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-20 mb-3"></div>
                        <div class="space-y-2">
                            @foreach(range(1, 4) as $i)
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 rounded-full bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded animate-pulse flex-1"></div>
                                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-6"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-show="!loading" x-transition>
        <x-breadcrumb :items="[['label' => 'Komunitas']]" />

        <div class="lg:grid lg:grid-cols-4 lg:gap-8">

            {{-- Main Content --}}
            <div class="lg:col-span-3">
                {{-- Stats Bar --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_threads']) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Thread</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_replies']) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Balasan</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_users']) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Kontributor</p>
                    </div>
                </div>

                {{-- Sort Tabs --}}
                <div class="flex items-center gap-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-1 mb-6">
                    <a href="{{ route('forum.index', ['sort' => 'latest']) }}"
                       class="flex-1 text-center px-4 py-2 text-sm font-medium rounded-lg transition {{ $sort === 'latest' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        Terbaru
                    </a>
                    <a href="{{ route('forum.index', ['sort' => 'popular']) }}"
                       class="flex-1 text-center px-4 py-2 text-sm font-medium rounded-lg transition {{ $sort === 'popular' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        Terpopuler
                    </a>
                    <a href="{{ route('forum.index', ['sort' => 'unanswered']) }}"
                       class="flex-1 text-center px-4 py-2 text-sm font-medium rounded-lg transition {{ $sort === 'unanswered' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        Belum Dijawab
                    </a>
                </div>

                {{-- Thread List --}}
                <div class="space-y-3">
                    @forelse($threads as $thread)
                        @include('forum._thread-card', ['thread' => $thread])
                    @empty
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-12 text-center">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada thread.</p>
                            <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Jadilah yang pertama memulai diskusi!</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $threads->links() }}
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="mt-8 lg:mt-0">
                {{-- Categories --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5 mb-4">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Kategori</h3>
                    <div class="space-y-1">
                        @foreach($categories as $cat)
                            <a href="{{ route('forum.category', $cat->slug) }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 transition group">
                                <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $cat->color ?? '#6B7280' }}"></span>
                                <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white flex-1">{{ $cat->name }}</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $cat->threads_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Guidelines --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Panduan Komunitas</h3>
                    <ul class="space-y-2 text-xs text-gray-500 dark:text-gray-400">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Hormati semua anggota komunitas
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Gunakan kategori yang sesuai
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Cari dulu sebelum membuat thread baru
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Dilarang spam atau konten tidak pantas
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
