<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $category }} - {{ config('app.name') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">

    {{-- Navigation --}}
    <nav class="bg-white shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('logo.jpeg') }}" alt="NotEDs" class="w-7 h-7 rounded-lg object-cover" />
                    <span class="text-lg font-bold text-gray-900">Noteds</span>
                </a>
                <div class="flex items-center gap-3">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-sm text-blue-600 font-medium">Studio</a>
                        @endif
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-700">{{ auth()->user()->name }}</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700">Masuk</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-700 text-sm">← Kembali</a>
            <span class="text-gray-400">/</span>
            <h1 class="text-2xl font-bold text-gray-900">{{ $category }}</h1>
            <span class="text-sm text-gray-500">({{ $simulations->total() }} simulasi)</span>
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
                <div class="text-5xl mb-3">📭</div>
                <p class="text-gray-500">Belum ada simulasi di kategori ini.</p>
            </div>
        @endif
    </main>
</body>
</html>
