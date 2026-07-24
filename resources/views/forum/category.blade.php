<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <a href="{{ route('forum.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background-color: {{ $category->color ?? '#6366F1' }}"></span>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h1>
                    </div>
                    @if($category->description)
                        <p class="text-sm text-gray-500 mt-1">{{ $category->description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="lg:grid lg:grid-cols-4 lg:gap-8">
            {{-- Main Content --}}
            <div class="lg:col-span-3">
                {{-- Sort Tabs --}}
                <div class="flex items-center gap-1 bg-white rounded-xl border border-gray-100 p-1 mb-6">
                    <a href="{{ route('forum.category', ['category' => $category->slug, 'sort' => 'latest']) }}"
                       class="flex-1 text-center px-4 py-2 text-sm font-medium rounded-lg transition {{ $sort === 'latest' ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                        Terbaru
                    </a>
                    <a href="{{ route('forum.category', ['category' => $category->slug, 'sort' => 'popular']) }}"
                       class="flex-1 text-center px-4 py-2 text-sm font-medium rounded-lg transition {{ $sort === 'popular' ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                        Terpopuler
                    </a>
                    <a href="{{ route('forum.category', ['category' => $category->slug, 'sort' => 'unanswered']) }}"
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
                            <p class="text-gray-500 font-medium">Belum ada thread di kategori ini.</p>
                            <a href="{{ route('forum.create') }}" class="mt-3 inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700 font-medium">
                                Buat Thread Baru →
                            </a>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $threads->links() }}
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="mt-8 lg:mt-0">
                <div class="bg-white rounded-xl border border-gray-100 p-5 mb-4">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Kategori</h3>
                    <div class="space-y-1">
                        @foreach($categories as $cat)
                            <a href="{{ route('forum.category', $cat->slug) }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition group {{ $cat->id === $category->id ? 'bg-blue-50' : 'hover:bg-gray-50' }}">
                                <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $cat->color ?? '#6B7280' }}"></span>
                                <span class="text-sm {{ $cat->id === $category->id ? 'text-blue-700 font-medium' : 'text-gray-700 group-hover:text-gray-900' }} flex-1">{{ $cat->name }}</span>
                                <span class="text-xs text-gray-400">{{ $cat->threads_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                @auth
                    <a href="{{ route('forum.create', ['category' => $category->slug]) }}"
                       class="block w-full text-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                        + Buat Thread di {{ $category->name }}
                    </a>
                @endauth
            </div>
        </div>
    </div>
</x-app-layout>
