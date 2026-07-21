<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Notifikasi - {{ config('app.name') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">

    @include('components.app-header')

    <main class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Notifikasi</h1>
            @if($notifications->count() > 0)
                <button
                    onclick="markAllAsRead()"
                    class="text-sm text-blue-600 hover:text-blue-700 font-medium transition"
                >
                    Tandai semua sudah dibaca
                </button>
            @endif
        </div>

        @if($notifications->count() > 0)
            <div class="space-y-2" id="notifications-list">
                @foreach($notifications as $notification)
                    <div
                        class="p-4 rounded-xl transition cursor-pointer {{ $notification->read_at ? 'bg-white hover:bg-gray-50' : 'bg-blue-50 hover:bg-blue-100 border-l-2 border-blue-500' }}"
                        onclick="markAsRead('{{ $notification->id }}', '{{ $notification->data['url'] ?? '#' }}')"
                    >
                        <div class="flex items-start gap-3">
                            {{-- Icon based on type --}}
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                @if($notification->type === 'comment') bg-blue-100 text-blue-600
                                @elseif($notification->type === 'follow') bg-green-100 text-green-600
                                @elseif($notification->type === 'reaction') bg-purple-100 text-purple-600
                                @elseif($notification->type === 'rating') bg-yellow-100 text-yellow-600
                                @else bg-gray-100 text-gray-500
                                @endif
                            ">
                                @if($notification->type === 'comment')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                @elseif($notification->type === 'follow')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                                @elseif($notification->type === 'reaction')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                @elseif($notification->type === 'rating')
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm {{ $notification->read_at ? 'text-gray-500' : 'text-gray-900 font-medium' }}">
                                    {{ $notification->title }}
                                </p>
                                <p class="text-xs {{ $notification->read_at ? 'text-gray-400' : 'text-gray-600' }} mt-1">
                                    {{ $notification->body }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>

                            @if(!$notification->read_at)
                                <div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2"></div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                <h3 class="text-gray-500 text-lg font-medium">Belum ada notifikasi</h3>
                <p class="text-gray-400 text-sm mt-2">Notifikasi akan muncul di sini ketika ada aktivitas terkait Anda.</p>
            </div>
        @endif
    </main>

    <script>
        function markAsRead(id, url) {
            fetch('/notifications/' + id + '/mark-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(response) {
                if (!response.ok || !(response.headers.get('content-type') || '').includes('application/json')) {
                    return null;
                }
                return response.json();
            })
            .then(function(data) {
                if (data && data.message) {
                    window.showToast(data.message, 'success');
                }
                if (url && url !== '#') {
                    window.location.href = url;
                } else {
                    window.location.reload();
                }
            })
            .catch(function() {
                if (url && url !== '#') {
                    window.location.href = url;
                }
            });
        }

        function markAllAsRead() {
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(response) {
                if (!response.ok || !(response.headers.get('content-type') || '').includes('application/json')) {
                    return null;
                }
                return response.json();
            })
            .then(function(data) {
                if (data && data.message) {
                    window.showToast(data.message, 'success');
                }
                window.location.reload();
            })
            .catch(function() {
                window.location.reload();
            });
        }
    </script>

    {{-- Footer --}}
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
