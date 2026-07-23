<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                SEO Management
            </h2>
            <a href="{{ route('admin.seo.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah SEO
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Search --}}
            <div class="mb-6">
                <form method="GET" action="{{ route('admin.seo.index') }}" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari page key atau judul..."
                        class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" class="px-3 py-1.5 text-sm bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Cari</button>
                </form>
            </div>

            {{-- SEO Settings Table --}}
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($seoSettings->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Page Key</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Meta Title</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Meta Description</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Schema</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Updated By</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Updated</th>
                                        <th class="text-right py-3 px-2 text-gray-500 font-medium">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($seoSettings as $seo)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-3 px-2">
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-700 rounded-full font-mono">{{ $seo->page_key }}</span>
                                        </td>
                                        <td class="py-3 px-2 text-gray-900">{{ Str::limit($seo->meta_title, 40) }}</td>
                                        <td class="py-3 px-2 text-gray-500 text-xs">{{ Str::limit($seo->meta_description, 50) }}</td>
                                        <td class="py-3 px-2 text-center">
                                            @if($seo->structured_data)
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">✓</span>
                                            @else
                                                <span class="text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-2 text-gray-500 text-xs">{{ $seo->updater?->name ?? '-' }}</td>
                                        <td class="py-3 px-2 text-gray-400 text-xs">{{ $seo->updated_at->diffForHumans() }}</td>
                                        <td class="py-3 px-2 text-right space-x-2">
                                            <a href="{{ route('admin.seo.edit', $seo) }}" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Edit</a>
                                            <form method="POST" action="{{ route('admin.seo.destroy', $seo) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-medium">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $seoSettings->links() }}
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <p>Belum ada pengaturan SEO. <a href="{{ route('admin.seo.create') }}" class="text-blue-600 hover:underline">Tambah yang pertama!</a></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
