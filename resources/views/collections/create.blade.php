<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[['label' => 'Collection', 'url' => route('collections.index')], ['label' => 'Buat Collection']]" />

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-data="{ saving: false }" @submit="saving = true">
                <form action="{{ route('collections.store') }}" method="POST">
                    @csrf

                    <div class="space-y-5">
                        {{-- Title --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Judul Collection</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm"
                                placeholder="Contoh: Fisika Dasar - Mekanika" />
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" id="description" rows="3"
                                x-data="{ text: '{{ addslashes(old('description')) }}' }"
                                x-model="text" maxlength="1000"
                                class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm"
                                placeholder="Jelaskan tentang collection ini...">{{ old('description') }}</textarea>
                            <p class="text-xs text-gray-400 mt-1"><span x-text="text.length"></span>/1000 karakter</p>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Visibility --}}
                        <div>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_public" value="1" {{ old('is_public', '1') === '1' ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Publik</span>
                                    <p class="text-xs text-gray-500">Collection ini bisa dilihat oleh semua orang</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-6">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"
                                :disabled="saving">
                            <span x-show="!saving">Buat Collection</span>
                            <span x-show="saving" class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" class="opacity-75"></path></svg>
                                Membuat...
                            </span>
                        </button>
                        <a href="{{ route('collections.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
