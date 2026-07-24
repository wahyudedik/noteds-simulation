{{-- Standalone Vote Buttons Component --}}
@props(['votable', 'type' => 'thread'])

<div x-data="{ votesCount: {{ $votable->votes_count }}, userVote: null }"
     class="flex flex-col items-center gap-1 min-w-[48px]">
    <button @click="vote('{{ $type }}', {{ $votable->id }}, 1)"
            :class="userVote === 1 ? 'text-blue-600' : 'text-gray-300 hover:text-green-500'"
            class="p-1 rounded transition" title="Upvote">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4l-8 8h5v8h6v-8h5z"/></svg>
    </button>
    <span x-text="votesCount"
          :class="votesCount > 0 ? 'text-green-600' : (votesCount < 0 ? 'text-red-500' : 'text-gray-400')"
          class="text-lg font-bold"></span>
    <button @click="vote('{{ $type }}', {{ $votable->id }}, -1)"
            :class="userVote === -1 ? 'text-red-500' : 'text-gray-300 hover:text-red-400'"
            class="p-1 rounded transition" title="Downvote">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 20l8-8h-5V4H9v8H4z"/></svg>
    </button>
</div>
