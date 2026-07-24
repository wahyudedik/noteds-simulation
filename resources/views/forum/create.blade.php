<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <a href="{{ route('forum.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Buat Thread Baru</h1>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <form action="{{ route('forum.store') }}" method="POST">
            @csrf

            <div class="bg-white rounded-xl border border-gray-100 p-6 space-y-5">
                {{-- Category --}}
                <div>
                    <label for="forum_category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="forum_category_id" id="forum_category_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih kategori...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('forum_category_id', request('category') ? $categories->firstWhere('slug', request('category'))?->id : '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('forum_category_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           placeholder="Judul thread yang jelas dan deskriptif..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Body --}}
                <div>
                    <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Isi Thread</label>
                    <textarea name="body" id="body" rows="10" required
                              placeholder="Tuliskan pertanyaan, ide, atau topik diskusi Anda di sini...&#10;&#10;Tip: Gunakan @namapengguna untuk mention pengguna lain."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-y">{{ old('body') }}</textarea>
                    @error('body')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Optional Simulation Link --}}
                <div class="border-t border-gray-100 pt-5">
                    <p class="text-xs text-gray-400 mb-2">Opsional: Lampirkan simulasi terkait</p>
                    <p class="text-xs text-gray-400">Kamu bisa menambahkan link simulasi nanti di isi thread.</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 mt-4">
                <a href="{{ route('forum.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    Publikasikan Thread
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
