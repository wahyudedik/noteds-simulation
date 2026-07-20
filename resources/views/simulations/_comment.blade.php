{{-- Single Comment Component --}}
@php
    $replies = $comment->replies()->latest()->get();
    $maxDepth = 3;
@endphp

<div class="comment-item" id="comment-{{ $comment->id }}" style="margin-left: {{ $depth * 24 }}px;">
    <div class="flex gap-3 {{ $depth > 0 ? 'mt-3' : '' }}">
        <a href="{{ route('creators.show', $comment->user->id) }}" class="flex-shrink-0">
            <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-white text-xs font-semibold overflow-hidden">
                @if($comment->user->avatar)
                    <img src="{{ Storage::disk('public')->url($comment->user->avatar) }}" alt="{{ $comment->user->name }}" class="w-full h-full object-cover" />
                @else
                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                @endif
            </div>
        </a>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <a href="{{ route('creators.show', $comment->user->id) }}" class="text-white text-sm font-medium hover:text-blue-400 transition">{{ $comment->user->name }}</a>
                @if($comment->is_pinned)
                    <span class="text-[10px] bg-yellow-500/20 text-yellow-400 px-1.5 py-0.5 rounded font-medium">DIPIN</span>
                @endif
                <span class="text-gray-500 text-xs">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            <p class="text-gray-300 text-sm mt-1 whitespace-pre-line">{{ $comment->body }}</p>

            {{-- Comment Actions --}}
            <div class="flex items-center gap-3 mt-2">
                @auth
                    <button onclick="toggleReplyForm({{ $comment->id }})" class="text-gray-500 hover:text-blue-400 text-xs font-medium transition">
                        Balas
                    </button>
                @endauth
                @if(auth()->id() === $comment->user_id || auth()->user()->isAdmin())
                    <button onclick="deleteComment({{ $comment->id }})" class="text-gray-500 hover:text-red-400 text-xs font-medium transition">
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
                            class="flex-1 bg-gray-800 border border-gray-700 rounded-lg px-3 py-1.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-500"
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

            {{-- Nested Replies (max 3 levels) --}}
            @if($depth < $maxDepth && $replies->count() > 0)
                @foreach($replies as $reply)
                    @include('simulations._comment', ['comment' => $reply, 'depth' => $depth + 1])
                @endforeach
            @elseif($depth >= $maxDepth && $replies->count() > 0)
                <p class="text-gray-500 text-xs mt-2 ml-1">
                    <a href="#" class="text-blue-400 hover:text-blue-300">Lihat {{ $replies->count() }} balasan lainnya...</a>
                </p>
            @endif
        </div>
    </div>
</div>
