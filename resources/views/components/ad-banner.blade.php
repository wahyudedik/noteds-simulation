@props(['position' => 'sidebar'])

@php
    $ad = app(\App\Services\AdService::class)->getAdForPosition($position, auth()->id());
@endphp

@if($ad)
    <div class="ad-banner ad-banner-{{ $position }}" data-ad-id="{{ $ad->id }}" data-position="{{ $position }}">
        @if($ad->type === 'adsense' && $ad->adsense_publisher_id && $ad->adsense_ad_slot)
            {{-- AdSense Ad --}}
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="{{ $ad->adsense_publisher_id }}"
                 data-ad-slot="{{ $ad->adsense_ad_slot }}"
                 data-full-width-responsive="true"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
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
                <span class="absolute top-1 right-1 text-[10px] text-gray-400 bg-black/50 px-1 rounded">Ad</span>
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
                <span class="absolute top-1 right-1 text-[10px] text-gray-400 bg-black/50 px-1 rounded">Ad</span>
            </div>
        @elseif($ad->content)
            {{-- Native/HTML Ad --}}
            <div class="relative rounded-lg border border-gray-200 p-3 bg-gray-50">
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
                <span class="text-[10px] text-gray-400 mt-1 block">Ad</span>
            </div>
        @endif
    </div>

    @once
        <script>
            document.addEventListener('alpine:init', () => {
                // Track impressions
                document.querySelectorAll('.ad-banner').forEach(banner => {
                    const adId = banner.dataset.adId;
                    if (adId) {
                        navigator.sendBeacon('{{ route("api.ads.impression") }}', JSON.stringify({
                            ad_type: 'platform',
                            ad_id: adId,
                            position: banner.dataset.position
                        }));
                    }
                });

                // Track clicks
                document.querySelectorAll('.ad-click-tracker').forEach(link => {
                    link.addEventListener('click', () => {
                        const adId = link.dataset.adId;
                        if (adId) {
                            navigator.sendBeacon('{{ route("api.ads.click") }}', JSON.stringify({
                                ad_type: 'platform',
                                ad_id: adId
                            }));
                        }
                    });
                });
            });
        </script>
    @endonce
@endif
