@props(['items'])

@if($items && count($items) > 0)
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Beranda</a>
        @foreach($items as $item)
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            @if(isset($item['url']))
                <a href="{{ $item['url'] }}" class="hover:text-blue-600 transition">{{ $item['label'] }}</a>
            @else
                <span class="text-gray-900 font-medium">{{ $item['label'] }}</span>
            @endif
        @endforeach
    </nav>
@endif
