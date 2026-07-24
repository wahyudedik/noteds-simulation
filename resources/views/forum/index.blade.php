<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Komunitas</h1>
                    <p class="text-sm text-gray-500 mt-1">Diskusi, berbagi, dan belajar bersama.</p>
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="lg:grid lg:grid-cols-4 lg:gap-8">

            {{-- Main Content --}}
            <div class="lg:col-span-3">
                {{-- Stats Bar --}}
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_threads']) }}</p>
                        <p class="text-xs text-gray-500">Thread</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_replies']) }}</p>
                        <p class="text-xs text-gray-500">Balasan</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                        <p class="text-xs text-gray-500">Kontributor</p>
                    </div>
                </div>

                {{-- Sort Tabs --}}
                <div class="flex items-center gap-1 bg-white rounded-xl border border-gray-100 p-1 mb-6">
                    <a href="{{ route('forum.index', ['sort' => 'latest']) }}"
                       class="flex-1 text-center px-4 py-2 text-sm font-medium rounded-lg transition {{ $sort === 'latest' ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                        Terbaru
                    </a>
                    <a href="{{ route('forum.index', ['sort' => 'popular']) }}"
                       class="flex-1 text-center px-4 py-2 text-sm font-medium rounded-lg transition {{ $sort === 'popular' ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                        Terpopuler
                    </a>
                    <a href="{{ route('forum.index', ['sort' => 'unanswered']) }}"
                       class="flex-1 text-center px-4 py-2 text-sm font-medium rounded-lg transition {{ $sort === 'unanswered' ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                        Belum Dijawab
                    </a>
                </div>

                {{-- Thread List --}}
                <div class="space-y-3">
                    @forelse($threads as $thread)
                        @include('forum._thread-card', ['thread' => $thread])
                    @empty
                        <div class="bg-white rounded-xl border border-gray-100 p-12 text-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <p class="text-gray-500 font-medium">Belum ada thread.</p>
                            <p class="text-gray-400 text-sm mt-1">Jadilah yang pertama memulai diskusi!</p>
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
                <div class="bg-white rounded-xl border border-gray-100 p-5 mb-4">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Kategori</h3>
                    <div class="space-y-1">
                        @foreach($categories as $cat)
                            <a href="{{ route('forum.category', $cat->slug) }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 transition group">
                                <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $cat->color ?? '#6B7280' }}"></span>
                                <span class="text-sm text-gray-700 group-hover:text-gray-900 flex-1">{{ $cat->name }}</span>
                                <span class="text-xs text-gray-400">{{ $cat->threads_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Guidelines --}}
                <div class="bg-white rounded-xl border border-gray-100 p-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Panduan Komunitas</h3>
                    <ul class="space-y-2 text-xs text-gray-500">
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
