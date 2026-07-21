<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Collection Saya - {{ config('app.name') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">

    @include('components.app-header')

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Collection Saya</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola learning collection Anda</p>
            </div>
            <a href="{{ route('collections.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Collection
            </a>
        </div>

        {{-- Status Messages --}}
        @if(session('status'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
                @if(session('status') === 'collection-created')
                    Collection berhasil dibuat.
                @elseif(session('status') === 'collection-updated')
                    Collection berhasil diperbarui.
                @elseif(session('status') === 'collection-deleted')
                    Collection berhasil dihapus.
                @endif
            </div>
        @endif

        @if($collections->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($collections as $collection)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                        {{-- Thumbnail --}}
                        <div class="aspect-video bg-gradient-to-br from-blue-500 to-purple-600 relative">
                            @if($collection->thumbnail)
                                <img src="{{ Storage::disk('public')->url($collection->thumbnail) }}" alt="{{ $collection->title }}" class="w-full h-full object-cover" />
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                </div>
                            @endif
                            <div class="absolute top-2 right-2">
                                @if($collection->is_public)
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded-full">Publik</span>
                                @else
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">Privat</span>
                                @endif
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="p-4">
                            <a href="{{ route('collections.show', $collection->slug) }}" class="text-gray-900 font-semibold text-sm hover:text-blue-600 transition line-clamp-1">
                                {{ $collection->title }}
                            </a>
                            <p class="text-gray-500 text-xs mt-1">{{ $collection->simulations_count }} simulasi &middot; {{ $collection->formatted_view_count }} dilihat</p>
                            @if($collection->description)
                                <p class="text-gray-500 text-xs mt-2 line-clamp-2">{{ $collection->description }}</p>
                            @endif

                            <div class="flex items-center gap-2 mt-3">
                                <a href="{{ route('collections.edit', $collection) }}" class="flex-1 text-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition">
                                    Edit
                                </a>
                                <form action="{{ route('collections.destroy', $collection) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmSubmit(this.closest('form'), 'Hapus collection ini?')" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-lg transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $collections->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <h3 class="text-gray-500 text-lg font-medium">Belum ada collection</h3>
                <p class="text-gray-400 text-sm mt-2">Buat collection untuk mengorganisasi simulasi favorit Anda.</p>
                <a href="{{ route('collections.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    Buat Collection Pertama
                </a>
            </div>
        @endif
    </main>

    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('logo.jpeg') }}" alt="Noteds" class="w-6 h-6 rounded object-cover" />
                    <span class="font-semibold text-gray-900">Noteds</span>
                </div>
                <p class="text-sm text-gray-500">
                    Interactive Simulations &copy; {{ date('Y') }}
                </p>
            </div>
        </div>
    </footer>

    <x-toast />
</body>
</html>
