<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                Simulation Studio
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
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Simulasi</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_simulations'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $stats['published'] }} published &middot; {{ $stats['draft'] }} draft</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Views</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_views']) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Plays</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_plays']) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Likes</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_likes']) }}</p>
                </div>
            </div>

            {{-- Recent Simulations --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Simulasi Terbaru</h3>

                    @if($recentSimulations->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
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
                                    <tr class="border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="py-3 px-2">
                                            <a href="{{ route('admin.simulations.show', $sim) }}" class="font-medium text-gray-900 dark:text-white hover:text-blue-600">
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
