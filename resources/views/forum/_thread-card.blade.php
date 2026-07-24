{{-- Thread Card Component --}}
@props(['thread'])

<a href="{{ route('forum.show', $thread->slug) }}"
   class="block bg-white rounded-xl border border-gray-100 p-4 hover:shadow-md hover:border-gray-200 transition group">
    <div class="flex items-start gap-4">
        {{-- Vote Column --}}
        <div class="flex flex-col items-center gap-1 text-center min-w-[48px]">
            <span class="text-lg font-bold {{ $thread->votes_count > 0 ? 'text-green-600' : ($thread->votes_count < 0 ? 'text-red-500' : 'text-gray-400') }}">
                {{ $thread->votes_count }}
            </span>
            <span class="text-[10px] text-gray-400 uppercase tracking-wide">vote</span>
        </div>

        {{-- Content --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                {{-- Category Badge --}}
                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-semibold rounded-full text-white" style="background-color: {{ $thread->category->color ?? '#6366F1' }}">
                    {{ $thread->category->name }}
                </span>

                {{-- Status Badges --}}
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

            {{-- Title --}}
            <h3 class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition line-clamp-2">
                {{ $thread->title }}
            </h3>

            {{-- Simulation Link --}}
            @if($thread->simulation)
                <p class="text-xs text-blue-500 mt-1 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $thread->simulation->title }}
                </p>
            @endif

            {{-- Meta --}}
            <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                <span class="flex items-center gap-1">
                    <div class="w-4 h-4 rounded-full bg-gray-200 flex items-center justify-center text-[8px] font-bold text-gray-500">
                        {{ strtoupper(substr($thread->user->name, 0, 1)) }}
                    </div>
                    {{ $thread->user->name }}
                </span>
                <span>{{ $thread->created_at->diffForHumans() }}</span>
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    {{ $thread->replies_count }}
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    {{ number_format($thread->views_count) }}
                </span>
            </div>
        </div>
    </div>
</a>
