<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                Kelola Kategori
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Add Category Form --}}
            <div class="mb-6 bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                <h3 class="text-sm font-medium text-gray-900 mb-4">Tambah Kategori Baru</h3>
                <form action="{{ route('admin.categories.store') }}" method="POST" class="flex flex-wrap gap-3 items-end">
                    @csrf
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                        <input type="text" name="name" required placeholder="contoh: Fisika"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div class="w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Icon (opsional)</label>
                        <input type="text" name="icon" placeholder="contoh: ⚡"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div class="w-32">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                        <input type="number" name="sort_order" value="0" min="0"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        Tambah
                    </button>
                </form>
            </div>

            {{-- Categories Table --}}
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($categories->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Urutan</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Nama</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Slug</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Simulasi</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Status</th>
                                        <th class="text-right py-3 px-2 text-gray-500 font-medium">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50" id="category-{{ $category->id }}">
                                        <td class="py-3 px-2 text-gray-500">{{ $category->sort_order }}</td>
                                        <td class="py-3 px-2">
                                            <span class="font-medium text-gray-900">
                                                @if($category->icon) {{ $category->icon }} @endif
                                                {{ $category->name }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-2 text-gray-500"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">{{ $category->slug }}</code></td>
                                        <td class="py-3 px-2 text-center text-gray-500">{{ $category->simulations_count }}</td>
                                        <td class="py-3 px-2 text-center">
                                            @if($category->is_active)
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Aktif</span>
                                            @else
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-500 rounded-full">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-2 text-right space-x-2">
                                            <button onclick="editCategory({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->description ?? '') }}', '{{ $category->icon ?? '' }}', {{ $category->sort_order }})" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Edit</button>
                                            @if($category->simulations_count === 0)
                                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmSubmit(this.closest('form'), 'Hapus kategori {{ addslashes($category->name) }}?')" class="text-red-600 hover:text-red-700 text-xs font-medium">Hapus</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $categories->links() }}
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>Belum ada kategori.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Category Modal --}}
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit Kategori</h3>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                        <input type="text" name="name" id="editName" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" id="editDescription" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                            <input type="text" name="icon" id="editIcon" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                            <input type="number" name="sort_order" id="editSortOrder" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editCategory(id, name, description, icon, sortOrder) {
            document.getElementById('editForm').action = '/admin/categories/' + id;
            document.getElementById('editName').value = name;
            document.getElementById('editDescription').value = description;
            document.getElementById('editIcon').value = icon;
            document.getElementById('editSortOrder').value = sortOrder;
            const modal = document.getElementById('editModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('editModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</x-app-layout>
