<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Collection Tersimpan - {{ config('app.name') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">

    @include('components.app-header')

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Collection Tersimpan</h1>
                <p class="text-gray-500 text-sm mt-1">Collection yang Anda simpan dari pengguna lain</p>
            </div>
            <a href="{{ route('collections.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                → Collection Saya
            </a>
        </div>

        @if($savedCollections->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($savedCollections as $saved)
                    @php $collection = $saved->collection; @endphp
                    <a href="{{ route('collections.show', $collection->slug) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition group">
                        <div class="aspect-video bg-gradient-to-br from-blue-500 to-purple-600 relative overflow-hidden">
                            @if($collection->thumbnail)
                                <img src="{{ Storage::disk('public')->url($collection->thumbnail) }}" alt="{{ $collection->title }}" class="w-full h-full object-cover" />
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                </div>
                            @endif
                            <div class="absolute bottom-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded-full">
                                {{ $collection->simulations->count() }} simulasi
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="text-gray-900 font-semibold text-sm group-hover:text-blue-600 transition line-clamp-1">{{ $collection->title }}</h3>
                            @if($collection->description)
                                <p class="text-gray-500 text-xs mt-1 line-clamp-2">{{ $collection->description }}</p>
                            @endif
                            <div class="flex items-center gap-2 mt-3">
                                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-semibold overflow-hidden">
                                    @if($collection->user->avatar)
                                        <img src="{{ Storage::disk('public')->url($collection->user->avatar) }}" alt="" class="w-full h-full object-cover" />
                                    @else
                                        {{ strtoupper(substr($collection->user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <span class="text-xs text-gray-500">{{ $collection->user->name }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $savedCollections->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                <h3 class="text-gray-500 text-lg font-medium">Belum ada collection tersimpan</h3>
                <p class="text-gray-400 text-sm mt-2">Simpan collection dari pengguna lain untuk melihatnya di sini.</p>
                <a href="{{ route('home') }}" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">Jelajahi Simulasi</a>
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
</body>
</html>
