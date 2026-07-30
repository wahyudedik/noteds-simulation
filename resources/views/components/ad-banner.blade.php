@props(['position' => 'sidebar'])

@php
    $ad = app(\App\Services\AdService::class)->getAdForPosition($position, auth()->id());
    // Eager-load sponsor relationship if not already loaded
    if ($ad && $ad->sponsor_id && ! $ad->relationLoaded('sponsor')) {
        $ad->load('sponsor');
    }
@endphp

@if($ad)
    <div class="ad-banner ad-banner-{{ $position }}" data-ad-id="{{ $ad->id }}" data-position="{{ $position }}">
        @if($ad->type === 'adsense' && $ad->adsense_publisher_id && $ad->adsense_ad_slot)
            {{-- Google AdSense --}}
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="{{ $ad->adsense_publisher_id }}"
                 data-ad-slot="{{ $ad->adsense_ad_slot }}"
                 data-full-width-responsive="true"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>

        @elseif($ad->ad_network === 'monetag' && $ad->ad_network_config)
            {{-- Monetag Banner --}}
            @php $zoneId = $ad->ad_network_config['zone_id'] ?? null; @endphp
            @if($zoneId)
                <div id="monetag-zone-{{ $zoneId }}"></div>
                <script>
                    (function(d, z, id) {
                        var s = d.createElement('script');
                        s.src = '//d2r55xnwy6nx47.cloudfront.net/lib.js';
                        s.onload = function() { if(window.monetag) { window.monetag.initZone(id); } };
                        d.head.appendChild(s);
                    })(document, null, '{{ $zoneId }}');
                </script>
            @endif

        @elseif($ad->ad_network === 'propellerads' && $ad->ad_network_config)
            {{-- PropellerAds Banner --}}
            @php $zoneId = $ad->ad_network_config['zone_id'] ?? null; @endphp
            @if($zoneId)
                <div id="propeller-{{ $zoneId }}" data-zoneid="{{ $zoneId }}"></div>
            @endif

        @elseif($ad->ad_network === 'media_net' && $ad->adsense_publisher_id && $ad->adsense_ad_slot)
            {{-- Media.net (uses similar format to AdSense) --}}
            <div id="mediavnet-{{ $ad->adsense_ad_slot }}"></div>
            <script>
                window._mNHandle = window._mNHandle || {};
                window._mNHandle.queue = window._mNHandle.queue || [];
                document.write('<scr' + 'ipt src="//contextual.media.net/nmediadispatch.js?aID=' + '{{ $ad->adsense_publisher_id }}' + '&c=' + '{{ $ad->adsense_ad_slot }}' + '" ></scr' + 'ipt>');
            </script>

        @elseif($ad->ad_network === 'adsterra' && $ad->ad_network_config)
            {{-- Adsterra Banner --}}
            @php $zoneId = $ad->ad_network_config['zone_id'] ?? null; @endphp
            @if($zoneId)
                <div id="adsterra-{{ $zoneId }}"></div>
                <script>
                    (function(d, z, id) {
                        var s = d.createElement('script');
                        s.async = true;
                        s.src = '//d2r55xnwy6nx47.cloudfront.net/lib.js';
                        s.onload = function() { if(window.adsterra) { window.adsterra.initZone(id); } };
                        d.head.appendChild(s);
                    })(document, null, '{{ $zoneId }}');
                </script>
            @endif

        @elseif($ad->ad_network === 'ezoic' && $ad->ad_network_config)
            {{-- Ezoic Banner --}}
            @php $zoneId = $ad->ad_network_config['zone_id'] ?? null; @endphp
            @if($zoneId)
                <div id="ezoic-{{ $zoneId }}" class="ezoic-ad"></div>
            @endif

        @elseif($ad->type === 'video' && $ad->video_path)
            {{-- Video Ad --}}
            <div class="relative overflow-hidden rounded-lg">
                <video
                    class="w-full h-auto max-h-48 object-cover"
                    src="{{ Storage::disk('public')->url($ad->video_path) }}"
                    muted
                    playsinline
                    loop
                    x-init="$el.play()"
                    x-on:click="$el.paused ? $el.play() : $el.pause()"
                ></video>
                @if($ad->target_url)
                    <a
                        href="{{ $ad->target_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="ad-click-tracker absolute inset-0"
                        data-ad-id="{{ $ad->id }}"
                        aria-label="Advertisement: {{ $ad->title }}"
                    ></a>
                @endif
                <span class="absolute top-1 right-1 text-[10px] text-gray-400 bg-black/50 px-1 rounded">{{ $ad->sponsor ? 'Sponsored by ' . $ad->sponsor->company_name : 'Ad' }}</span>
            </div>

        @elseif($ad->image_path)
            {{-- Image/Banner Ad --}}
            <div class="relative overflow-hidden rounded-lg">
                @if($ad->target_url)
                    <a
                        href="{{ $ad->target_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="ad-click-tracker block"
                        data-ad-id="{{ $ad->id }}"
                        aria-label="Advertisement: {{ $ad->title }}"
                    >
                        <img
                            src="{{ Storage::disk('public')->url($ad->image_path) }}"
                            alt="{{ $ad->title }}"
                            class="w-full h-auto object-cover max-h-48"
                            loading="lazy"
                        />
                    </a>
                @else
                    <img
                        src="{{ Storage::disk('public')->url($ad->image_path) }}"
                        alt="{{ $ad->title }}"
                        class="w-full h-auto object-cover max-h-48"
                        loading="lazy"
                    />
                @endif
                <span class="absolute top-1 right-1 text-[10px] text-gray-400 bg-black/50 px-1 rounded">{{ $ad->sponsor ? 'Sponsored by ' . $ad->sponsor->company_name : 'Ad' }}</span>
            </div>

        @elseif($ad->content)
            {{-- Native/HTML Ad --}}
            <div class="relative rounded-lg border border-gray-200 dark:border-gray-700 p-3 bg-gray-50 dark:bg-gray-800">
                @if($ad->target_url)
                    <a
                        href="{{ $ad->target_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="ad-click-tracker block"
                        data-ad-id="{{ $ad->id }}"
                        aria-label="Advertisement: {{ $ad->title }}"
                    >
                        {!! $ad->content !!}
                    </a>
                @else
                    <div class="prose prose-sm max-w-none">{!! $ad->content !!}</div>
                @endif
                <span class="text-[10px] text-gray-400 mt-1 block">{{ $ad->sponsor ? 'Sponsored by ' . $ad->sponsor->company_name : 'Ad' }}</span>
            </div>
        @endif
    </div>

    @once
        <script>
            document.addEventListener('alpine:init', () => {
                // Track impressions
                document.querySelectorAll('.ad-banner').forEach(banner => {
                    const adId = banner.dataset.adId;
                    const position = banner.dataset.position;
                    if (adId && position) {
                        fetch('/admin/ad-track/impression', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({ ad_id: adId, position: position }),
                        }).catch(() => {});
                    }
                });

                // Track clicks
                document.querySelectorAll('.ad-click-tracker').forEach(tracker => {
                    tracker.addEventListener('click', () => {
                        const adId = tracker.dataset.adId;
                        if (adId) {
                            fetch('/admin/ad-track/click', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({ ad_id: adId }),
                            }).catch(() => {});
                        }
                    });
                });
            });
        </script>
    @endonce
@endif
