<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
            Upload Simulasi Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.simulations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        {{-- Title --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Simulasi *</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                placeholder="Contoh: Hukum Newton Gerak" />
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori *</label>
                                <select name="category" id="category" required
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="">Pilih Kategori</option>
                                    <option value="Fisika" {{ old('category') === 'Fisika' ? 'selected' : '' }}>Fisika</option>
                                    <option value="Kimia" {{ old('category') === 'Kimia' ? 'selected' : '' }}>Kimia</option>
                                    <option value="Biologi" {{ old('category') === 'Biologi' ? 'selected' : '' }}>Biologi</option>
                                    <option value="Matematika" {{ old('category') === 'Matematika' ? 'selected' : '' }}>Matematika</option>
                                    <option value="Geografi" {{ old('category') === 'Geografi' ? 'selected' : '' }}>Geografi</option>
                                    <option value="Sejarah" {{ old('category') === 'Sejarah' ? 'selected' : '' }}>Sejarah</option>
                                    <option value="Umum" {{ old('category') === 'Umum' ? 'selected' : '' }}>Umum</option>
                                </select>
                                @error('category')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="subcategory" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sub Kategori</label>
                                <input type="text" name="subcategory" id="subcategory" value="{{ old('subcategory') }}"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    placeholder="Contoh: Mekanika" />
                            </div>
                        </div>

                        {{-- Tags --}}
                        <div>
                            <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tags</label>
                            <input type="text" name="tags" id="tags" value="{{ old('tags') }}"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                placeholder="Pisahkan dengan koma: newton, gaya, akselerasi" />
                            <p class="text-xs text-gray-400 mt-1">Pisahkan setiap tag dengan koma</p>
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                            <textarea name="description" id="description" rows="4"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                placeholder="Deskripsi singkat tentang simulasi ini...">{{ old('description') }}</textarea>
                        </div>

                        {{-- Simulation File --}}
                        <div>
                            <label for="simulation_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">File Simulasi (.zip) *</label>
                            <input type="file" name="simulation_file" id="simulation_file" required accept=".zip"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm
                                file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            <p class="text-xs text-gray-400 mt-1">Format: .zip (maks 50MB). Harus berisi file <code>index.html</code> dan opsional <code>manifest.json</code>.</p>

                            {{-- Structure Tooltip --}}
                            <div x-data="{ open: false }" class="mt-3">
                                <button type="button" @click="open = !open"
                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span x-text="open ? 'Sembunyikan struktur' : 'Lihat struktur package yang diharapkan'"></span>
                                    <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" x-cloak
                                    class="mt-3 p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-lg text-xs">
                                    <p class="font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                                        Struktur Simulation Package:
                                    </p>
                                    <pre class="font-mono text-gray-600 dark:text-gray-400 leading-relaxed overflow-x-auto"><code>simulation.zip
├── manifest.json          <span class="text-gray-400 dark:text-gray-500"># Metadata simulasi (judul, kategori, versi, dll.)</span>
├── index.html             <span class="text-gray-400 dark:text-gray-500"># File entry point simulasi</span>
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
└── README.md              <span class="text-gray-400 dark:text-gray-500"># Dokumentasi teknis (opsional)</span></code></pre>

                                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                        <p class="font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                            Format <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded">manifest.json</code> (opsional):
                                        </p>
                                        <pre class="font-mono text-gray-600 dark:text-gray-400 leading-relaxed overflow-x-auto"><code>{
  "title": "Hukum Newton",
  "description": "Simulasi interaktif Hukum Newton",
  "category": "Fisika",
  "version": "1.0.0",
  "author": "Nama Creator",
  "entry_point": "index.html"
}</code></pre>
                                    </div>

                                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                        <p class="text-gray-500 dark:text-gray-400 leading-relaxed">
                                            <svg class="inline w-4 h-4 mr-1 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                                            <strong>Catatan:</strong> File <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded">index.html</code> wajib ada sebagai entry point.
                                            Jika <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded">manifest.json</code> disertakan, metadata akan otomatis terisi dari file tersebut.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @error('simulation_file')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Thumbnail --}}
                        <div>
                            <label for="thumbnail" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Thumbnail</label>
                            <input type="file" name="thumbnail" id="thumbnail" accept="image/jpeg,image/png,image/webp"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm
                                file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, atau WebP (maks 5MB). Jika tidak diupload, thumbnail default akan digunakan.</p>
                        </div>

                        {{-- Publish --}}
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            <label for="is_published" class="text-sm text-gray-700 dark:text-gray-300">
                                Langsung publikasikan (Published)
                            </label>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                Upload Simulasi
                            </button>
                            <a href="{{ route('admin.simulations.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
