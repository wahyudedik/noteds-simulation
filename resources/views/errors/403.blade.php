<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak | {{ config('app.name', 'Noteds') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 font-sans antialiased flex items-center justify-center min-h-screen">
    <div class="text-center px-4">
        <div class="mb-6">
            <svg class="w-24 h-24 text-red-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h1 class="text-6xl font-bold text-gray-900 mb-2">403</h1>
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Akses Ditolak</h2>
        <p class="text-sm text-gray-500 mb-8 max-w-md mx-auto">
            Kamu tidak memiliki izin untuk mengakses halaman ini. Jika kamu merasa ini adalah kesalahan, hubungi admin.
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
