{{-- Single Comment Component --}}
@php
    $replies = $comment->replies()->latest()->get();
    $maxDepth = 3;
    $isCollapsed = $depth >= $maxDepth && $replies->count() > 0;
@endphp

<div class="comment-item" id="comment-{{ $comment->id }}" style="margin-left: {{ $depth * 24 }}px;" x-data="{ expanded: false }">
    <div class="flex gap-3 {{ $depth > 0 ? 'mt-3' : '' }}">
        <a href="{{ route('creators.show', $comment->user->id) }}" class="flex-shrink-0">
            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-xs font-semibold overflow-hidden">
                @if($comment->user->avatar)
                    <img src="{{ Storage::disk('public')->url($comment->user->avatar) }}" alt="{{ $comment->user->name }}" class="w-full h-full object-cover" />
                @else
                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                @endif
            </div>
        </a>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <a href="{{ route('creators.show', $comment->user->id) }}" class="text-gray-900 text-sm font-medium hover:text-blue-600 transition">{{ $comment->user->name }}</a>
                @if($comment->is_pinned)
                    <span class="text-[10px] bg-yellow-50 text-yellow-700 px-1.5 py-0.5 rounded font-medium">DIPIN</span>
                @endif
                <span class="text-gray-400 text-xs">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            <p class="text-gray-600 text-sm mt-1 whitespace-pre-line">{{ $comment->body }}</p>

            {{-- Comment Actions --}}
            <div class="flex items-center gap-3 mt-2">
                @auth
                    <button onclick="toggleReplyForm({{ $comment->id }})" class="text-gray-400 hover:text-blue-600 text-xs font-medium transition">
                        Balas
                    </button>
                @endauth
                @if(auth()->id() === $comment->user_id || auth()->user()->isAdmin())
                    <button onclick="deleteComment({{ $comment->id }})" class="text-gray-400 hover:text-red-600 text-xs font-medium transition">
                        Hapus
                    </button>
                @endif
            </div>

            {{-- Reply Form --}}
            @auth
                <div id="reply-form-{{ $comment->id }}" class="comment-reply mt-3">
                    <div class="flex gap-2">
                        <input
                            id="reply-input-{{ $comment->id }}"
                            type="text"
                            class="flex-1 bg-white border border-gray-300 rounded-lg px-3 py-1.5 text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-400"
                            placeholder="Tulis balasan..."
                            onkeydown="if(event.key==='Enter'){postComment({{ $comment->id }});}"
                        />
                        <button
                            onclick="postComment({{ $comment->id }})"
                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition"
                        >
                            Kirim
                        </button>
                    </div>
                </div>
            @endauth

            {{-- Nested Replies --}}
            @if($replies->count() > 0)
                @if($isCollapsed)
                    {{-- Collapsed replies: show toggle link --}}
                    <button
                        @click="expanded = !expanded"
                        class="text-blue-600 hover:text-blue-700 text-xs font-medium mt-2 ml-1 transition flex items-center gap-1"
                    >
                        <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-90': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        <span x-text="expanded ? 'Sembunyikan balasan' : 'Lihat {{ $replies->count() }} balasan lainnya...'"></span>
                    </button>
                    <div x-show="expanded" x-collapse x-cloak>
                        @foreach($replies as $reply)
                            @include('simulations._comment', ['comment' => $reply, 'depth' => $depth + 1])
                        @endforeach
                    </div>
                @else
                    {{-- Normal depth: render replies directly --}}
                    @foreach($replies as $reply)
                        @include('simulations._comment', ['comment' => $reply, 'depth' => $depth + 1])
                    @endforeach
                @endif
            @endif
        </div>
    </div>
</div>
