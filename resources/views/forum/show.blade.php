<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <a href="{{ route('forum.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-semibold rounded-full text-white" style="background-color: {{ $thread->category->color ?? '#6366F1' }}">
                            {{ $thread->category->name }}
                        </span>
                        @if($thread->is_pinned)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/></svg>
                                Disematkan
                            </span>
                        @endif
                        @if($thread->is_solved)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-semibold rounded-full bg-green-100 text-green-700">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                                Terjawab
                            </span>
                        @endif
                        @if($thread->is_locked)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-semibold rounded-full bg-gray-100 text-gray-500">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                                Terkunci
                            </span>
                        @endif
                    </div>
                    <h1 class="text-xl font-bold text-gray-900 truncate">{{ $thread->title }}</h1>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- Thread Content --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <div class="flex items-start gap-4">
                {{-- Vote Column --}}
                <div x-data="{ votesCount: {{ $thread->votes_count }}, userVote: {{ $userVote ?? 'null' }} }"
                     class="flex flex-col items-center gap-1 min-w-[56px]">
                    <button @click="vote('thread', {{ $thread->id }}, 1)"
                            :class="userVote === 1 ? 'text-blue-600' : 'text-gray-300 hover:text-green-500'"
                            class="p-1 rounded transition" title="Upvote">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4l-8 8h5v8h6v-8h5z"/></svg>
                    </button>
                    <span x-text="votesCount" :class="votesCount > 0 ? 'text-green-600' : (votesCount < 0 ? 'text-red-500' : 'text-gray-400')"
                          class="text-lg font-bold"></span>
                    <button @click="vote('thread', {{ $thread->id }}, -1)"
                            :class="userVote === -1 ? 'text-red-500' : 'text-gray-300 hover:text-red-400'"
                            class="p-1 rounded transition" title="Downvote">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 20l8-8h-5V4H9v8H4z"/></svg>
                    </button>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    {{-- Author Info --}}
                    <div class="flex items-center gap-3 mb-4">
                        @if($thread->user->avatar)
                            <img src="{{ Storage::disk('public')->url($thread->user->avatar) }}" alt="" class="w-8 h-8 rounded-full object-cover" />
                        @else
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($thread->user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <a href="{{ route('creators.show', $thread->user_id) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600 transition">{{ $thread->user->name }}</a>
                            <p class="text-xs text-gray-400">{{ $thread->created_at->diffForHumans() }} · {{ number_format($thread->views_count) }} dilihat</p>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="prose prose-sm max-w-none text-gray-700 whitespace-pre-wrap">{{ $thread->body }}</div>

                    {{-- Simulation Link --}}
                    @if($thread->simulation)
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                                <a href="{{ route('simulations.show', $thread->simulation->slug) }}" class="text-sm font-medium text-blue-700 hover:text-blue-800 transition">
                                    {{ $thread->simulation->title }}
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Admin Actions --}}
                    @if(auth()->check() && auth()->user()->isAdmin())
                        <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                            <form action="{{ route('forum.pin', $thread->slug) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/></svg>
                                    {{ $thread->is_pinned ? 'Unpin' : 'Pin' }}
                                </button>
                            </form>
                            <form action="{{ route('forum.lock', $thread->slug) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition inline-flex items-center gap-1">
                                    @if($thread->is_locked)
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm6-9h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6h1.9c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2z"/></svg>
                                        Unlock
                                    @else
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                                        Lock
                                    @endif
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Thread Owner Actions --}}
                    @auth
                        @if($thread->isOwnedBy(auth()->user()))
                            <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                                <a href="{{ route('forum.edit', $thread->slug) }}" class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                                <form action="{{ route('forum.destroy', $thread->slug) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus thread ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        {{-- Replies --}}
        <div class="mt-6">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">{{ $thread->replies_count }} Balasan</h2>

            <div class="space-y-3">
                @forelse($replies->whereNull('parent_id') as $reply)
                    @include('forum._reply', ['reply' => $reply, 'thread' => $thread, 'depth' => 0])
                @empty
                    <div class="bg-white rounded-xl border border-gray-100 p-8 text-center">
                        <p class="text-gray-400 text-sm">Belum ada balasan. Jadilah yang pertama membalas!</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Reply Form --}}
        @auth
            @if(!$thread->is_locked)
                <div class="mt-6 bg-white rounded-xl border border-gray-100 p-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Tulis Balasan</h3>
                    <form action="{{ route('forum.reply', $thread->slug) }}" method="POST">
                        @csrf
                        <input type="hidden" name="parent_id" value="" x-ref="parentId" />
                        <textarea name="body" rows="4" required
                                  placeholder="Tulis balasan Anda di sini... Gunakan @namapengguna untuk mention."
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-y"></textarea>
                        <div class="flex items-center justify-end mt-3">
                            <button type="submit" class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                Kirim Balasan
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="mt-6 bg-gray-50 rounded-xl border border-gray-200 p-5 text-center">
                    <p class="text-gray-500 text-sm">
                        <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                        Thread ini sudah dikunci. Balasan baru tidak dapat dikirim.
                    </p>
                </div>
            @endif
        @else
            <div class="mt-6 bg-gray-50 rounded-xl border border-gray-200 p-5 text-center">
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Masuk</a>
                <span class="text-gray-500 text-sm"> untuk membalas thread ini.</span>
            </div>
        @endauth
    </div>

    @push('scripts')
    <script>
        function vote(votableType, votableId, value) {
            fetch('{{ route("forum.vote") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    votable_type: votableType,
                    votable_id: votableId,
                    value: value,
                }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // Update Alpine state
                    const Alpine = window.Alpine;
                    if (Alpine) {
                        // Dispatch event for Alpine to react
                        window.dispatchEvent(new CustomEvent('vote-updated', { detail: data }));
                    }
                    location.reload();
                } else {
                    window.showToast(data.message || 'Gagal memberikan vote.', 'error');
                }
            })
            .catch(() => window.showToast('Terjadi kesalahan.', 'error'));
        }
    </script>
    @endpush
</x-app-layout>
