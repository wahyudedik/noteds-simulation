@props(['listing'])

@php
    $simulation = $listing->simulation;
    $creator = $listing->creator;
@endphp

<div class="marketplace-card block group">
    <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm dark:shadow-gray-900/50 hover:shadow-md transition duration-200 border border-gray-100 dark:border-gray-700">
        {{-- Thumbnail --}}
        <a href="{{ route('marketplace.show', $simulation->slug) }}" class="block">
            <div class="relative aspect-video bg-gray-200 overflow-hidden">
                @if($simulation->thumbnail)
                    @php
                        $variants = $simulation->thumbnail_variants;
                        $hasVariants = is_array($variants) && !empty($variants);
                    @endphp
                    <img
                        @if($hasVariants)
                            srcset="
                                @if(!empty($variants['thumb'])){{ asset('storage/'.$variants['thumb']) }} 300w,@endif
                                @if(!empty($variants['medium'])){{ asset('storage/'.$variants['medium']) }} 600w,@endif
                                {{ asset('storage/'.$simulation->thumbnail) }} 1200w
                            "
                            sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 25vw"
                            src="{{ asset('storage/'.($variants['medium'] ?? $simulation->thumbnail)) }}"
                        @else
                            src="{{ Storage::disk('public')->url($simulation->thumbnail) }}"
                        @endif
                        alt="{{ $simulation->title }}"
                        class="w-full h-full object-cover transition duration-300 group-hover:scale-105"
                        loading="lazy"
                        width="600"
                        height="400"
                        fetchpriority="low"
                    />
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600">
                        <svg class="w-10 h-10 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                    </div>
                @endif

                {{-- Price Badge --}}
                <div class="absolute top-3 left-3 bg-emerald-600 text-white text-sm font-bold px-3 py-1 rounded-lg shadow-lg">
                    {{ $listing->formatted_price }}
                </div>

                {{-- License Badge --}}
                <div class="absolute top-3 right-3 bg-white/90 dark:bg-gray-800/90 text-gray-700 dark:text-gray-300 text-xs font-medium px-2 py-1 rounded-md shadow">
                    @switch($listing->license_type)
                        @case('single')
                            🔓 Single Use
                            @break
                        @case('institutional')
                            🏫 Institutional
                            @break
                        @case('subscription')
                            🔄 Subscription
                            @break
                        @default
                            {{ $listing->license_type }}
                    @endswitch
                </div>

                {{-- Demo badge --}}
                @if($listing->demo_available)
                    <div class="absolute bottom-3 left-3 bg-blue-600/90 text-white text-xs font-medium px-2 py-1 rounded-md">
                        🎮 Demo Available
                    </div>
                @endif
            </div>
        </a>

        {{-- Info --}}
        <div class="p-4">
            <div class="flex items-start gap-3">
                {{-- Creator avatar --}}
                <a href="{{ route('creators.show', $creator->username) }}" class="w-9 h-9 rounded-full bg-gray-300 dark:bg-gray-600 flex-shrink-0 overflow-hidden hover:ring-2 hover:ring-blue-300 transition" onclick="event.stopPropagation();">
                    @if($creator->avatar)
                        <img src="{{ Storage::disk('public')->url($creator->avatar) }}" alt="{{ $creator->name }}" class="w-full h-full object-cover" loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 font-semibold text-sm">
                            {{ strtoupper(substr($creator->name, 0, 1)) }}
                        </div>
                    @endif
                </a>

                <div class="flex-1 min-w-0">
                    <a href="{{ route('marketplace.show', $simulation->slug) }}" class="block">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm leading-snug line-clamp-2 group-hover:text-blue-600 transition">
                            {{ $simulation->title }}
                        </h3>
                    </a>
                    <a href="{{ route('creators.show', $creator->username) }}" class="text-xs text-gray-500 dark:text-gray-400 mt-1 hover:text-blue-600 transition inline-flex items-center gap-1" onclick="event.stopPropagation();">
                        {{ $creator->name }}
                        @if($creator->isVerifiedCreator())
                            <x-verified-badge :type="$creator->verification_badge" size="sm" />
                        @endif
                    </a>
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mt-1 flex-wrap">
                        @if($simulation->average_rating)
                            <div class="flex items-center gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3 h-3 {{ $i <= round($simulation->average_rating) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                @endfor
                                <span class="ml-0.5">{{ number_format($simulation->average_rating, 1) }}</span>
                            </div>
                            <span>&middot;</span>
                        @endif
                        <span>{{ $simulation->category }}</span>
                        <span>&middot;</span>
                        <span>{{ number_format($listing->total_sales) }} terjual</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
