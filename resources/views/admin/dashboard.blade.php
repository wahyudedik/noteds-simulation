<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Admin Panel
            </h2>
            <a href="{{ route('admin.simulations.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Upload Simulasi
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Stats Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Simulasi</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_simulations'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $stats['published'] }} published &middot; {{ $stats['draft'] }} draft</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Views</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_views']) }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Plays</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_plays']) }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Likes</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_likes']) }}</p>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Laporan Pengguna</div>
                        <div class="text-xs text-gray-500">Review & tindak laporan</div>
                    </div>
                </a>
                <a href="{{ route('admin.scans.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Security Scans</div>
                        <div class="text-xs text-gray-500">Auto-scan, sandbox, review</div>
                    </div>
                </a>
                <a href="{{ route('admin.simulations.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Kelola Simulasi</div>
                        <div class="text-xs text-gray-500">CRUD & publish</div>
                    </div>
                </a>
            </div>

            {{-- Recent Simulations --}}
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Simulasi Terbaru</h3>

                    @if($recentSimulations->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Judul</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Kategori</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Views</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Plays</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Status</th>
                                        <th class="text-right py-3 px-2 text-gray-500 font-medium">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentSimulations as $sim)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-3 px-2">
                                            <a href="{{ route('admin.simulations.show', $sim) }}" class="font-medium text-gray-900 hover:text-blue-600">
                                                {{ Str::limit($sim->title, 40) }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-2 text-gray-500">{{ $sim->category }}</td>
                                        <td class="py-3 px-2 text-center text-gray-500">{{ number_format($sim->view_count) }}</td>
                                        <td class="py-3 px-2 text-center text-gray-500">{{ number_format($sim->play_count) }}</td>
                                        <td class="py-3 px-2 text-center">
                                            @if($sim->is_published)
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Published</span>
                                            @else
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-2 text-right">
                                            <a href="{{ route('admin.simulations.edit', $sim) }}" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Edit</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>Belum ada simulasi. <a href="{{ route('admin.simulations.create') }}" class="text-blue-600 hover:underline">Upload yang pertama!</a></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
