@props(['simulation'])

<a href="{{ route('simulations.show', $simulation->slug) }}" class="simulation-card block group">
    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition duration-200">
        {{-- Thumbnail --}}
        <div class="relative aspect-video bg-gray-200 overflow-hidden">
            @if($simulation->thumbnail)
                <img
                    src="{{ Storage::disk('public')->url($simulation->thumbnail) }}"
                    alt="{{ $simulation->title }}"
                    class="w-full h-full object-cover transition duration-300"
                />
            @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600">
                    <svg class="w-10 h-10 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                </div>
            @endif

            {{-- Play overlay --}}
            <div class="thumbnail-overlay absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 transition duration-200">
                <div class="w-14 h-14 bg-white/90 rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-blue-600 ml-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </div>
            </div>

            {{-- Version badge --}}
            <div class="absolute bottom-2 right-2 bg-black/70 text-white text-xs px-2 py-0.5 rounded">
                v{{ $simulation->version }}
            </div>
        </div>

        {{-- Info --}}
        <div class="p-3">
            <div class="flex items-start gap-3">
                {{-- Creator avatar --}}
                <div class="w-9 h-9 rounded-full bg-gray-300 flex-shrink-0 overflow-hidden">
                    @if($simulation->user->avatar)
                        <img src="{{ Storage::disk('public')->url($simulation->user->avatar) }}" alt="" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-blue-100 text-blue-600 font-semibold text-sm">
                            {{ strtoupper(substr($simulation->user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 text-sm leading-snug line-clamp-2 group-hover:text-blue-600 transition">
                        {{ $simulation->title }}
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $simulation->user->name }}</p>
                    <div class="flex items-center gap-2 text-xs text-gray-500 mt-0.5">
                        @php $avgRating = $simulation->average_rating ?? $simulation->ratings()->avg('rating'); @endphp
                        @if($avgRating)
                            <div class="flex items-center gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3 h-3 {{ $i <= round($avgRating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                @endfor
                                <span class="ml-0.5">{{ number_format($avgRating, 1) }}</span>
                            </div>
                            <span>&middot;</span>
                        @endif
                        <span>{{ $simulation->category }}</span>
                        <span>&middot;</span>
                        <span>{{ $simulation->formatted_play_count }} dimainkan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</a>
