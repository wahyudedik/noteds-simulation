<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                Semua Simulasi
            </h2>
            <a href="{{ route('admin.simulations.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Upload Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($simulations->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Judul</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Kreator</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Kategori</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Views</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Plays</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Status</th>
                                        <th class="text-right py-3 px-2 text-gray-500 font-medium">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($simulations as $sim)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-3 px-2 font-medium text-gray-900">{{ Str::limit($sim->title, 35) }}</td>
                                        <td class="py-3 px-2 text-gray-500">{{ $sim->user->name }}</td>
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
                                        <td class="py-3 px-2 text-right space-x-2">
                                            <a href="{{ route('admin.simulations.show', $sim) }}" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Lihat</a>
                                            <a href="{{ route('admin.simulations.edit', $sim) }}" class="text-green-600 hover:text-green-700 text-xs font-medium">Edit</a>
                                            <form action="{{ route('admin.simulations.destroy', $sim) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmSubmit(this.closest('form'), 'Yakin hapus simulasi ini?')" class="text-red-600 hover:text-red-700 text-xs font-medium">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $simulations->links() }}
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>Belum ada simulasi.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
