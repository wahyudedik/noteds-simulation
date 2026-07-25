<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 - Session Habis | {{ config('app.name', 'Noteds') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased flex items-center justify-center min-h-screen">
    <a href="#error-content" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:top-2 focus:left-2 focus:bg-blue-600 focus:text-white focus:px-4 focus:py-2 focus:rounded-lg focus:font-medium focus:shadow-lg">
        Lewati ke konten utama
    </a>
    <div id="error-content" class="text-center px-4">
        <div class="mb-6">
            <svg class="w-24 h-24 text-yellow-300 dark:text-yellow-500/50 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h1 class="text-6xl font-bold text-gray-900 dark:text-white mb-2">419</h1>
        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Session Habis</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
            Session kamu sudah habis karena tidak aktif selama beberapa waktu. Silakan muat ulang halaman untuk melanjutkan.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ url('/') }}" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition duration-150">
                Kembali ke Beranda
            </a>
            <button onclick="history.back()" class="w-full sm:w-auto px-6 py-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                Halaman Sebelumnya
            </button>
        </div>
    </div>
</body>
</html>
