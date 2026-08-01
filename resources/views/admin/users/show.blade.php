<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                <svg class="w-5 h-5 inline-block mr-1 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ $user->name }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition">
                ? Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- User Info Sidebar --}}
                <div class="md:col-span-1 space-y-4">
                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-6 shadow-sm text-center">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full object-cover mx-auto mb-3">
                        @else
                            <div class="w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-2xl mx-auto mb-3">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>

                        <div class="mt-3">
                            @switch($user->role)
                                @case('superadmin')
                                    <span class="inline-flex px-3 py-1 text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-full">Superadmin</span>
                                    @break
                                @case('admin')
                                    <span class="inline-flex px-3 py-1 text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 rounded-full">Admin</span>
                                    @break
                                @case('creator')
                                    <span class="inline-flex px-3 py-1 text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded-full">Creator</span>
                                    @break
                                @default
                                    <span class="inline-flex px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 dark:text-gray-300 rounded-full">User</span>
                            @endswitch
                        </div>

                        @if($user->bio)
                            <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">{{ $user->bio }}</p>
                        @endif

                        <div class="mt-4 text-xs text-gray-400 dark:text-gray-500">
                            Bergabung {{ $user->created_at->format('d M Y') }}
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-5 shadow-sm">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Statistik</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Experience</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $stats['simulations'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Published</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $stats['published'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Total Views</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_views']) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Total Plays</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_plays']) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Komentar</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $stats['comments'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Followers</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $stats['followers'] }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Verification Status --}}
                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-5 shadow-sm">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Status Verifikasi</h4>
                        @if($user->isVerifiedCreator())
                            <div class="flex items-center gap-2 mb-3">
                                <x-verified-badge :type="$user->verification_badge" size="md" />
                                <span class="text-sm text-gray-600 dark:text-gray-400">Terverifikasi</span>
                            </div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Diverifikasi {{ $user->verified_at->format('d M Y H:i') }}</p>
                            @if($user->verification_notes)
                                <p class="text-xs text-gray-500 dark:text-gray-400 italic mb-3">"{{ $user->verification_notes }}"</p>
                            @endif
                        @else
                            <p class="text-sm text-gray-400 dark:text-gray-500 italic mb-3">Belum terverifikasi</p>
                        @endif
                        @if($user->isCreator())
                            @if($user->verified_at === null)
                                <form action="{{ route('admin.users.verify-creator', $user) }}" method="POST" class="space-y-2">
                                    @csrf
                                    <input type="text" name="verification_notes" placeholder="Catatan verifikasi (opsional)" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <button type="submit" class="w-full px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition inline-flex items-center justify-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        Verifikasi Creator
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.users.revoke-verification', $user) }}" method="POST">
                                    @csrf
                                    <button type="button" onclick="confirmSubmit(this.closest('form'), 'Cabut verifikasi {{ $user->name }}?')" class="w-full px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition inline-flex items-center justify-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                        Cabut Verifikasi
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>

                    {{-- Gamification --}}
                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-5 shadow-sm">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Gamifikasi</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Poin</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($user->total_points) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Level</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">Lv.{{ $user->current_level }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Streak</span>
                                <span class="text-sm font-semibold text-orange-600 dark:text-orange-400">{{ $user->streak ?? 0 }} hari</span>
                            </div>
                        </div>
                        @if($user->badges->count() > 0)
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($user->badges as $badge)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-50 text-yellow-700 text-xs rounded-full" title="{{ $badge->name }}">
                                            {{ $badge->icon }} {{ Str::limit($badge->name, 15) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Main Content --}}
                <div class="md:col-span-2 space-y-4">

                    {{-- Role Management --}}
                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-6 shadow-sm">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Ubah Role</h4>
                        <form action="{{ route('admin.users.update-role', $user) }}" method="POST" class="flex items-end gap-3">
                            @csrf
                            @method('PUT')
                            <div class="flex-1">
                                <select name="role" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                    <option value="creator" {{ $user->role === 'creator' ? 'selected' : '' }}>Creator</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="superadmin" {{ $user->role === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                                </select>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                                Simpan
                            </button>
                        </form>
                    </div>

                    {{-- Approve as Creator --}}
                    @if(!in_array($user->role, ['superadmin', 'admin', 'creator']))
                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-6 shadow-sm">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Pengajuan Creator</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Pengguna ini belum memiliki akses creator. Setujui untuk mengaktifkan Simulation Studio.</p>
                        <form action="{{ route('admin.users.approve-creator', $user) }}" method="POST">
                            @csrf
                            @method('POST')
                            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                                Setujui sebagai Creator
                            </button>
                        </form>
                    </div>
                    @endif

                    {{-- Recent Simulations --}}
                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-6 shadow-sm">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Experience Terbaru</h4>
                        @if($user->simulations->count() > 0)
                            <div class="space-y-3">
                                @foreach($user->simulations as $sim)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('admin.simulations.show', $sim) }}" class="font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:text-blue-400 text-sm">
                                                {{ Str::limit($sim->title, 40) }}
                                            </a>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                {{ $sim->category }} � {{ number_format($sim->view_count) }} views
                                            </div>
                                        </div>
                                        <div class="ml-3 flex-shrink-0">
                                            @if($sim->is_published)
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-full">Published</span>
                                            @else
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 rounded-full">Draft</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-400 dark:text-gray-500 italic">Belum ada experience.</p>
                        @endif
                    </div>

                    {{-- Danger Zone --}}
                    <div class="bg-white dark:bg-gray-800 border border-red-200 dark:border-red-900/50 rounded-xl p-6 shadow-sm">
                        <h4 class="text-sm font-medium text-red-600 dark:text-red-400 mb-2">Zona Berbahaya</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Tindakan yang dilakukan di sini tidak dapat dibatalkan.</p>
                        <div class="flex gap-3">
                            <form action="{{ route('admin.users.deactivate', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="button" onclick="confirmSubmit(this.closest('form'), 'Nonaktifkan akun {{ $user->name }}?')" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition">
                                    Nonaktifkan
                                </button>
                            </form>
                            @if(!$user->isAdmin())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmSubmit(this.closest('form'), 'Hapus pengguna {{ $user->name }}? Tindakan ini tidak dapat dibatalkan.', { title: 'Hapus Pengguna', confirmText: 'Ya, Hapus' })" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                                    Hapus Pengguna
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- confirmSubmit is globally available via app.js --}}
</x-app-layout>
