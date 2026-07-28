<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                Kelola Pengguna
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filters --}}
            <div class="mb-6 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-4 shadow-sm">
                <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau email..."
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div class="w-40">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                        <select name="role" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Semua Role</option>
                            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                            <option value="creator" {{ request('role') === 'creator' ? 'selected' : '' }}>Creator</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="superadmin" {{ request('role') === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'role']))
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Users Table --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($users->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="text-left py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Pengguna</th>
                                        <th class="text-left py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Role</th>
                                        <th class="text-center py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Experience</th>
                                        <th class="text-center py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Poin</th>
                                        <th class="text-center py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Level</th>
                                        <th class="text-center py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Bergabung</th>
                                        <th class="text-right py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50 dark:bg-gray-700/50 dark:hover:bg-gray-600/50">
                                        <td class="py-3 px-2">
                                            <div class="flex items-center gap-3">
                                                @if($user->avatar)
                                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover">
                                                @else
                                                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-semibold text-xs">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <a href="{{ route('admin.users.show', $user) }}" class="font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:text-blue-400">{{ $user->name }}</a>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-2">
                                            @switch($user->role)
                                                @case('superadmin')
                                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-full">Superadmin</span>
                                                    @break
                                                @case('admin')
                                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 rounded-full">Admin</span>
                                                    @break
                                                @case('creator')
                                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded-full">Creator</span>
                                                    @break
                                                @default
                                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 dark:text-gray-300 rounded-full">User</span>
                                            @endswitch
                                        </td>
                                        <td class="py-3 px-2 text-center text-gray-500 dark:text-gray-400">{{ $user->simulations_count }}</td>
                                        <td class="py-3 px-2 text-center text-gray-500 dark:text-gray-400">{{ number_format($user->total_points) }}</td>
                                        <td class="py-3 px-2 text-center text-gray-500 dark:text-gray-400">Lv.{{ $user->current_level }}</td>
                                        <td class="py-3 px-2 text-center text-gray-500 dark:text-gray-400 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                                        <td class="py-3 px-2 text-right space-x-2">
                                            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-xs font-medium">Detail</a>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmSubmit(this.closest('form'), 'Hapus pengguna {{ addslashes($user->name) }}? Tindakan ini tidak dapat dibatalkan.', { title: 'Hapus Pengguna', confirmText: 'Ya, Hapus' })" class="text-red-600 dark:text-red-400 hover:text-red-700 text-xs font-medium">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <p>Tidak ada pengguna ditemukan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
