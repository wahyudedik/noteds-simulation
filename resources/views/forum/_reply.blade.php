{{-- Reply Component (recursive) --}}
@props(['reply', 'thread', 'depth' => 0])

@php
    $indentClass = match(true) {
        $depth >= 2 => 'ml-12',
        $depth === 1 => 'ml-6',
        default => '',
    };
@endphp

<div id="reply-{{ $reply->id }}" class="{{ $indentClass }} {{ $reply->is_accepted ? 'bg-green-50 border border-green-200' : 'bg-white border border-gray-100' }} rounded-xl p-4" x-data="{ showReplyForm: false, replyBody: '' }">
    <div class="flex items-start gap-3">
        {{-- Vote Column --}}
        <div x-data="{ votesCount: {{ $reply->votes_count }}, userVote: null }"
             class="flex flex-col items-center gap-0.5 min-w-[40px]">
            <button @click="vote('reply', {{ $reply->id }}, 1)"
                    :class="userVote === 1 ? 'text-blue-600' : 'text-gray-300 hover:text-green-500'"
                    class="p-0.5 rounded transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4l-8 8h5v8h6v-8h5z"/></svg>
            </button>
            <span x-text="votesCount" class="text-xs font-bold {{ $reply->votes_count > 0 ? 'text-green-600' : ($reply->votes_count < 0 ? 'text-red-500' : 'text-gray-400') }}"></span>
            <button @click="vote('reply', {{ $reply->id }}, -1)"
                    :class="userVote === -1 ? 'text-red-500' : 'text-gray-300 hover:text-red-400'"
                    class="p-0.5 rounded transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 20l8-8h-5V4H9v8H4z"/></svg>
            </button>
        </div>

        {{-- Content --}}
        <div class="flex-1 min-w-0">
            {{-- Author --}}
            <div class="flex items-center gap-2 mb-1.5">
                @if($reply->user->avatar)
                    <img src="{{ Storage::disk('public')->url($reply->user->avatar) }}" alt="" class="w-5 h-5 rounded-full object-cover" />
                @else
                    <div class="w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-[8px] font-bold">
                        {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                    </div>
                @endif
                <span class="text-xs font-medium text-gray-900">{{ $reply->user->name }}</span>
                <span class="text-xs text-gray-400">· {{ $reply->created_at->diffForHumans() }}</span>
                @if($reply->is_accepted)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-semibold rounded-full bg-green-100 text-green-700">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        Jawaban Diterima
                    </span>
                @endif
            </div>

            {{-- Body --}}
            <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $reply->body }}</div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 mt-2">
                @auth
                    @if(!$thread->is_locked)
                        <button @click="showReplyForm = !showReplyForm" class="text-xs text-gray-400 hover:text-blue-600 transition font-medium">
                            Balas
                        </button>
                    @endif

                    {{-- Accept as best answer (thread author only) --}}
                    @if($thread->isOwnedBy(auth()->user()) && !$reply->is_accepted)
                        <form action="{{ route('forum.reply.accept', $reply->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs text-gray-400 hover:text-green-600 transition font-medium">
                                <svg class="w-3.5 h-3.5 inline" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                                Terima Jawaban
                            </button>
                        </form>
                    @endif

                    {{-- Delete (owner or admin) --}}
                    @if($reply->isOwnedBy(auth()->user()) || auth()->user()->isAdmin())
                        <form action="{{ route('forum.reply.destroy', $reply->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus balasan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-gray-400 hover:text-red-500 transition font-medium">
                                Hapus
                            </button>
                        </form>
                    @endif
                @endauth
            </div>

            {{-- Inline Reply Form --}}
            @auth
                <div x-show="showReplyForm" x-cloak class="mt-3">
                    <form action="{{ route('forum.reply', $thread->slug) }}" method="POST">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $reply->id }}" />
                        <textarea name="body" rows="3" required x-model="replyBody"
                                  placeholder="Balas ke {{ $reply->user->name }}..."
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-y"></textarea>
                        <div class="flex items-center gap-2 mt-2">
                            <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition">
                                Kirim
                            </button>
                            <button type="button" @click="showReplyForm = false" class="px-4 py-1.5 text-xs text-gray-500 hover:text-gray-700 transition">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            @endauth

            {{-- Nested Replies --}}
            @if($reply->children->count() > 0)
                <div class="mt-3 space-y-3">
                    @foreach($reply->children as $child)
                        @include('forum._reply', ['reply' => $child, 'thread' => $thread, 'depth' => $depth + 1])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
