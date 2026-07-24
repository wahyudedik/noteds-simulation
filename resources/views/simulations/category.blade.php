<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $category }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Explore', 'url' => route('simulations.explore')], ['label' => $category]]" />

            <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $category }}</h1>
                    <span class="text-sm text-gray-500">({{ $simulations->total() }} simulasi)</span>
                </div>
                <div class="flex items-center gap-1">
                    @php
                        $sortOptions = [
                            'popular' => 'Paling Dimainkan',
                            'newest' => 'Terbaru',
                            'rating' => 'Rating Tertinggi',
                        ];
                    @endphp
                    @foreach($sortOptions as $key => $label)
                        <a href="{{ route('simulations.category', ['category' => $category, 'sort' => $key]) }}"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg transition {{ ($sort ?? 'popular') === $key ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            @if($simulations->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($simulations as $sim)
                        @include('components.simulation-card', ['simulation' => $sim])
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $simulations->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    <p class="text-gray-500">Belum ada simulasi di kategori ini.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
