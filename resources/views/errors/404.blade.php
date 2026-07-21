<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan | {{ config('app.name', 'Noteds') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 font-sans antialiased flex items-center justify-center min-h-screen">
    <div class="text-center px-4">
        <div class="mb-6">
            <svg class="w-24 h-24 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h1 class="text-6xl font-bold text-gray-900 mb-2">404</h1>
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Halaman Tidak Ditemukan</h2>
        <p class="text-sm text-gray-500 mb-8 max-w-md mx-auto">
            Sepertinya halaman yang kamu cari sudah dipindahkan, dihapus, atau tidak tersedia.
        </p>
        <div class="flex items-center justify-center gap-3">
            <a href="{{ url('/') }}" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition duration-150">
                Kembali ke Beranda
            </a>
            <button onclick="history.back()" class="px-6 py-2.5 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition duration-150">
                Halaman Sebelumnya
            </button>
        </div>
    </div>
</body>
</html>
