<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Kesalahan Server | {{ config('app.name', 'Noteds') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 font-sans antialiased flex items-center justify-center min-h-screen">
    <div class="text-center px-4">
        <div class="mb-6">
            <svg class="w-24 h-24 text-orange-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
        </div>
        <h1 class="text-6xl font-bold text-gray-900 mb-2">500</h1>
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Kesalahan Server</h2>
        <p class="text-sm text-gray-500 mb-8 max-w-md mx-auto">
            Terjadi kesalahan pada server kami. Tim teknis sudah diberitahu dan sedang memperbaikinya. Silakan coba lagi nanti.
        </p>
        <div class="flex items-center justify-center gap-3">
            <a href="{{ url('/') }}" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition duration-150">
                Kembali ke Beranda
            </a>
            <button onclick="location.reload()" class="px-6 py-2.5 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition duration-150">
                Muat Ulang
            </button>
        </div>
    </div>
</body>
</html>
