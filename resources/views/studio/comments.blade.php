<x-studio-layout :pageTitle="'Moderasi Komentar'">
    {{-- Filter Tabs --}}
    <div class="flex items-center gap-2 mb-6 flex-wrap">
        @php
            $filters = [
                'all' => 'Semua',
                'unreplied' => 'Belum Dibalas',
                'replied' => 'Sudah Dibalas',
                'reported' => 'Dilaporkan',
                'pinned' => 'Disematkan',
            ];
        @endphp
        @foreach($filters as $key => $label)
            <a href="{{ route('studio.comments', ['filter' => $key]) }}"
               class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ $filter === $key ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Comments List --}}
    @if($comments->count() > 0)
        <div class="space-y-3">
            @foreach($comments as $comment)
                <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-5 {{ $comment->is_pinned ? 'ring-2 ring-yellow-200 bg-yellow-50/30' : '' }} {{ $comment->is_reported ? 'ring-2 ring-red-200 bg-red-50/30' : '' }}">
                    <div class="flex items-start gap-3">
                        <img src="{{ $comment->user->avatar ? Storage::url($comment->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&background=E5E7EB&color=374151&size=32' }}"
                             alt="{{ $comment->user->name }}"
                             class="w-10 h-10 rounded-full object-cover shrink-0" />
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-semibold text-gray-900">{{ $comment->user->name }}</span>
                                <span class="text-xs text-gray-400">·</span>
                                <a href="{{ route('simulations.show', $comment->simulation->slug) }}" class="text-xs text-blue-600 hover:underline" target="_blank">
                                    {{ Str::limit($comment->simulation->title, 40) }}
                                </a>
                                <span class="text-xs text-gray-400">· {{ $comment->created_at->diffForHumans() }}</span>
                                @if($comment->is_pinned)
                                    <span class="px-1.5 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Disematkan</span>
                                @endif
                                @if($comment->is_reported)
                                    <span class="px-1.5 py-0.5 text-xs font-medium bg-red-100 text-red-700 rounded-full">Dilaporkan</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-700 mt-1.5">{{ $comment->body }}</p>

                            {{-- Replies --}}
                            @if($comment->replies->count() > 0)
                                <div class="mt-3 ml-2 pl-3 border-l-2 border-gray-100 space-y-2">
                                    @foreach($comment->replies as $reply)
                                        <div class="text-sm">
                                            <span class="font-medium text-gray-900">{{ $reply->user->name }}</span>
                                            <span class="text-xs text-gray-400 ml-1">{{ $reply->created_at->diffForHumans() }}</span>
                                            <p class="text-gray-600 mt-0.5">{{ $reply->body }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 mt-3">
                                {{-- Reply Form --}}
                                <form method="POST" action="{{ route('studio.comments.reply', $comment->id) }}" class="flex items-center gap-2 flex-1"
                                      x-data="{ replyOpen: false }">
                                    @csrf
                                    <button type="button" @click="replyOpen = !replyOpen"
                                            class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                        Balas
                                    </button>
                                    <div x-show="replyOpen" x-cloak class="flex items-center gap-2 w-full">
                                        <input type="text" name="body" placeholder="Tulis balasan..." required maxlength="2000"
                                               class="flex-1 px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">Kirim</button>
                                    </div>
                                </form>

                                {{-- Pin/Unpin --}}
                                <form method="POST" action="{{ route('studio.comments.pin', $comment->id) }}">
                                    @csrf
                                    <button type="submit" class="text-xs {{ $comment->is_pinned ? 'text-yellow-600' : 'text-gray-400' }} hover:text-yellow-600 font-medium">
                                        {{ $comment->is_pinned ? 'Lepas Sematan' : 'Sematkan' }}
                                    </button>
                                </form>

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('studio.comments.destroy', $comment->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmSubmit(this.closest('form'), 'Hapus komentar ini?')" class="text-xs text-gray-400 hover:text-red-500 font-medium">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $comments->links() }}
        </div>
    @else
        <div class="text-center py-16 bg-white border border-gray-100 rounded-xl shadow-sm">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada komentar</h3>
            <p class="text-sm text-gray-500">Belum ada komentar yang cocok dengan filter ini.</p>
        </div>
    @endif
</x-studio-layout>
