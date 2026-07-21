{{-- Notification Bell Component --}}
{{-- Include this in any navigation bar. It shows a bell icon with unread count badge. --}}
@auth
    <a href="{{ route('notifications.index') }}" class="relative text-gray-500 hover:text-gray-700 transition" title="Notifikasi">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <span class="notification-badge absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center hidden">0</span>
    </a>
@endauth

<script>
    (function() {
        @auth
        fetch('{{ route("notifications.unread-count") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(function(r) {
            if (!r.ok || !(r.headers.get('content-type') || '').includes('application/json')) {
                return null;
            }
            return r.json();
        })
        .then(function(data) {
            if (!data) return;
            var badges = document.querySelectorAll('.notification-badge');
            badges.forEach(function(badge) {
                if (data.count > 0) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            });
        })
        .catch(function() {});
        @endauth
    })();
</script>
