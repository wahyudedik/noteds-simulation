@props(['simulation' => null])

@php
    /** @var \App\Models\Simulation $simulation */
    $simulation ??= $simulation;

    // Get approved creator ads for this simulation
    $creatorAds = \App\Models\CreatorAd::where('simulation_id', $simulation->id)
        ->where('review_status', 'approved')
        ->where('is_active', true)
        ->get();

    // Also get a platform ad for mid-roll position
    $platformAd = app(\App\Services\AdService::class)->getAdForPosition('mid_roll', auth()->id());
@endphp

@if($creatorAds->isNotEmpty() || $platformAd)
    <div class="my-6 space-y-4">
        {{-- Creator Ads --}}
        @foreach($creatorAds as $creatorAd)
            <div class="creator-ad bg-white border border-gray-200 rounded-xl p-4" data-ad-id="{{ $creatorAd->id }}">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] text-gray-400 uppercase tracking-wide">Sponsored</span>
                </div>
                @php
                    $config = $creatorAd->ad_config ?? [];
                @endphp

                @if(($config['type'] ?? '') === 'banner' && ! empty($config['image_url']))
                    <a
                        href="{{ $config['target_url'] ?? '#' }}"
                        target="_blank"
                        rel="noopener noreferrer nofollow"
                        class="block"
                    >
                        <img
                            src="{{ $config['image_url'] }}"
                            alt="{{ $config['title'] ?? 'Sponsored' }}"
                            class="w-full h-auto rounded-lg max-h-32 object-cover"
                            loading="lazy"
                        />
                    </a>
                @elseif(($config['type'] ?? '') === 'native')
                    <div class="flex gap-3">
                        @if(! empty($config['image_url']))
                            <img
                                src="{{ $config['image_url'] }}"
                                alt="{{ $config['title'] ?? 'Sponsored' }}"
                                class="w-20 h-20 rounded-lg object-cover flex-shrink-0"
                                loading="lazy"
                            />
                        @endif
                        <div>
                            @if(! empty($config['title']))
                                <h4 class="text-sm font-semibold text-gray-900">{{ $config['title'] }}</h4>
                            @endif
                            @if(! empty($config['description']))
                                <p class="text-xs text-gray-500 mt-0.5">{{ $config['description'] }}</p>
                            @endif
                            @if(! empty($config['target_url']))
                                <a
                                    href="{{ $config['target_url'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer nofollow"
                                    class="text-xs text-blue-600 hover:underline mt-1 inline-block"
                                >Kunjungi</a>
                            @endif
                        </div>
                    </div>
                @elseif(! empty($config['code_snippet']))
                    {{-- Custom code snippet ad --}}
                    <div class="ad-custom-code">
                        @if(! empty($config['target_url']))
                            <a href="{{ $config['target_url'] }}" target="_blank" rel="noopener noreferrer nofollow" class="block">
                                @endif
                                {!! $creatorAd->code_snippet !!}
                                @if(! empty($config['target_url']))
                            </a>
                        @endif
                    </div>
                @endif

                @if(! empty($creatorAd->code_snippet) && ($config['type'] ?? '') !== 'native' && ($config['type'] ?? '') !== 'banner')
                    <div class="mt-2">
                        {!! $creatorAd->code_snippet !!}
                    </div>
                @endif

                <span class="text-[10px] text-gray-400 mt-2 block text-right">Ad by Creator</span>
            </div>
        @endforeach

        {{-- Platform Ad (mid-roll) --}}
        @if($platformAd)
            <x-ad-banner position="mid_roll" />
        @endif
    </div>
@endif
