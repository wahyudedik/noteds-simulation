<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" /></svg>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $simulation->title }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                ['label' => 'Home', 'url' => route('home')],
                ['label' => 'Marketplace', 'url' => route('marketplace.index')],
                ['label' => $simulation->title],
            ]" />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Thumbnail / Preview --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        @if($simulation->thumbnail)
                            <img
                                src="{{ Storage::disk('public')->url($simulation->thumbnail) }}"
                                alt="{{ $simulation->title }}"
                                class="w-full h-64 sm:h-80 object-cover"
                                loading="lazy"
                            >
                        @else
                            <div class="w-full h-64 sm:h-80 bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
                                <svg class="w-20 h-20 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Title & Description --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <div class="flex items-start justify-between flex-wrap gap-4">
                            <div class="flex-1 min-w-0">
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $simulation->title }}</h1>
                                <div class="flex items-center gap-3 mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                                        {{ $simulation->category }}
                                    </span>
                                    <span>·</span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        v{{ $simulation->version }}
                                    </span>
                                    <span>·</span>
                                    <span>{{ $simulation->published_at?->diffForHumans() ?? '' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="mt-6 prose prose-gray dark:prose-invert max-w-none text-sm">
                            {!! nl2br(e($simulation->description)) !!}
                        </div>
                    </div>

                    {{-- Demo Preview --}}
                    @if($listing->demo_available)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Demo Gratis</h3>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Coba demo ini selama {{ $listing->demo_limit_minutes }} menit sebelum membeli.
                            </p>
                            <a
                                href="{{ route('simulations.play', $simulation->slug) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium text-sm transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /></svg>
                                Mainkan Demo
                            </a>
                        </div>
                    @endif

                    {{-- Reviews / Ratings --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Ulasan & Rating</h3>

                        {{-- Rating Summary --}}
                        <div class="flex items-start gap-6 mb-6">
                            <div class="text-center">
                                <div class="text-4xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($simulation->average_rating, 1) }}</div>
                                <div class="flex items-center gap-0.5 mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= round($simulation->average_rating) ? 'text-amber-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                    @endfor
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $simulation->rating_count }} ulasan</p>
                            </div>

                            {{-- Rating Distribution Bars --}}
                            <div class="flex-1 space-y-1.5">
                                @foreach([5, 4, 3, 2, 1] as $star)
                                    @php
                                        $count = $ratingCounts[$star] ?? 0;
                                        $total = max($simulation->rating_count, 1);
                                        $percentage = ($count / $total) * 100;
                                    @endphp
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="w-3 text-gray-500 dark:text-gray-400 text-right">{{ $star }}</span>
                                        <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                        <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-amber-400 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="w-8 text-gray-500 dark:text-gray-400 text-right">{{ $count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Individual Reviews --}}
                        @if($simulation->ratings->count() > 0)
                            <div class="space-y-4 border-t border-gray-100 dark:border-gray-700 pt-4">
                                @foreach($simulation->ratings->take(10) as $rating)
                                    <div class="flex gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($rating->user->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $rating->user->name }}</span>
                                                <div class="flex items-center gap-0.5">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-3 h-3 {{ $i <= $rating->rating ? 'text-amber-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                                    @endfor
                                                </div>
                                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $rating->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada ulasan.</p>
                        @endif
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Purchase Card --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 sticky top-24">
                        {{-- Price --}}
                        <div class="text-center mb-4">
                            <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $listing->formatted_price }}</div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 mt-2">
                                {{ ucfirst($listing->license_type) }} License
                            </span>
                        </div>

                        {{-- CTA Button --}}
                        @if($hasPurchased)
                            <div class="mb-4">
                                <div class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-xl font-semibold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Sudah Dibeli
                                </div>
                                <a
                                    href="{{ route('simulations.play', $simulation->slug) }}"
                                    class="mt-3 w-full flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /></svg>
                                    Mainkan Sekarang
                                </a>
                            </div>
                        @else
                            <form action="#" method="POST" x-data>
                                @csrf
                                <button
                                    type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" /></svg>
                                    Beli Sekarang
                                </button>
                            </form>
                        @endif

                        {{-- Stats --}}
                        <div class="mt-6 space-y-3 border-t border-gray-100 dark:border-gray-700 pt-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Total Terjual</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($listing->total_sales) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Rating</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100 flex items-center gap-1">
                                    <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                    {{ number_format($simulation->average_rating, 1) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Dilihat</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $simulation->formatted_view_count }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Dimainkan</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $simulation->formatted_play_count }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Creator Info --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Creator</h3>
                        <a href="{{ route('creators.show', $simulation->user->username) }}" class="flex items-center gap-3 group">
                            @if($simulation->user->avatar)
                                <img
                                    src="{{ Storage::disk('public')->url($simulation->user->avatar) }}"
                                    alt="{{ $simulation->user->name }}"
                                    class="w-10 h-10 rounded-full object-cover"
                                >
                            @else
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($simulation->user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $simulation->user->name }}</span>
                                    @if($simulation->user->verified_at)
                                        <x-verified-badge />
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $simulation->user->simulations()->published()->count() }} simulasi</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Related Simulations --}}
            @if($relatedListings->count() > 0)
                <div class="mt-12">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Simulasi Sejenis</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($relatedListings as $relatedListing)
                            <x-marketplace-card :listing="$relatedListing" />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
