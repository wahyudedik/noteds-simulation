<x-studio-layout :pageTitle="'Edit: ' . $simulation->title">
    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('studio.simulations.update', $simulation->slug) }}" enctype="multipart/form-data" x-data="studioUpload()">
            @csrf
            @method('PUT')

            <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h3>

                {{-- Title --}}
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Simulasi <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $simulation->title) }}" required maxlength="255"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm @error('title') border-red-500 @enderror" />
                    @error('title')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" id="description" rows="4" maxlength="5000"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm @error('description') border-red-500 @enderror">{{ old('description', $simulation->description) }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Category --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                        <select name="category" id="category" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm @error('category') border-red-500 @enderror">
                            <option value="">Pilih Kategori</option>
                            @foreach(['Fisika', 'Kimia', 'Biologi', 'Matematika', 'Ekonomi', 'Sejarah', 'Geografi', 'Informatika', 'Teknik', 'Seni', 'Bahasa', 'Lainnya'] as $cat)
                                <option value="{{ $cat }}" {{ old('category', $simulation->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="subcategory" class="block text-sm font-medium text-gray-700 mb-1">Sub-kategori</label>
                        <input type="text" name="subcategory" id="subcategory" value="{{ old('subcategory', $simulation->subcategory) }}" maxlength="100"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" />
                    </div>
                </div>

                {{-- Tags --}}
                <div>
                    <label for="tags" class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                    <input type="text" name="tags" id="tags" value="{{ old('tags', $simulation->tagModels->pluck('name')->implode(', ')) }}" maxlength="500"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                           placeholder="fisika, newton, mekanika (pisahkan dengan koma)" />
                </div>
            </div>

            {{-- Simulation Package --}}
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Package Simulasi</h3>

                {{-- Current Status --}}
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg mb-4">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm">
                        <span class="text-gray-600">Versi saat ini:</span>
                        <span class="font-medium text-gray-900">v{{ $simulation->version ?? '1.0.0' }}</span>
                    </div>
                </div>

                {{-- New ZIP Upload --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload ZIP Baru (opsional)</label>
                    <p class="text-xs text-gray-500 mb-2">Kosongkan jika tidak ingin mengubah file simulasi. Jika diunggah, versi akan otomatis naik.</p>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition cursor-pointer"
                         @click="$refs.zipInput.click()" @dragover.prevent @drop.prevent="handleZipDrop($event)">
                        <template x-if="!zipName">
                            <div>
                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="text-sm text-gray-500">Klik atau seret file ZIP baru</p>
                            </div>
                        </template>
                        <template x-if="zipName">
                            <div>
                                <svg class="w-8 h-8 text-green-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm font-medium text-gray-900" x-text="zipName"></p>
                                <p class="text-xs text-gray-400 mt-1" x-text="zipSize"></p>
                            </div>
                        </template>
                    </div>
                    <input type="file" name="simulation_zip" x-ref="zipInput" accept=".zip"
                           class="hidden" @change="handleZipSelect($event)" />
                    @error('simulation_zip')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Thumbnail --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail Baru (opsional)</label>
                    @if($simulation->thumbnail)
                        <div class="flex items-center gap-3 mb-2">
                            <img src="{{ Storage::url($simulation->thumbnail) }}" class="w-24 h-16 object-cover rounded-lg" />
                            <span class="text-xs text-gray-500">Thumbnail saat ini</span>
                        </div>
                    @endif
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition cursor-pointer"
                         @click="$refs.thumbInput.click()">
                        <template x-if="!thumbPreview">
                            <div>
                                <p class="text-xs text-gray-500">Upload thumbnail baru (opsional)</p>
                            </div>
                        </template>
                        <template x-if="thumbPreview">
                            <div>
                                <img :src="thumbPreview" class="w-32 h-20 object-cover rounded-lg mx-auto" />
                                <p class="text-xs text-gray-500 mt-1" x-text="thumbName"></p>
                            </div>
                        </template>
                    </div>
                    <input type="file" name="thumbnail" x-ref="thumbInput" accept="image/jpeg,image/png,image/webp"
                           class="hidden" @change="handleThumbSelect($event)" />
                    @error('thumbnail')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Publish Options --}}
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Opsi Publikasi</h3>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $simulation->is_published) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                    <div>
                        <span class="text-sm font-medium text-gray-900">Publikasikan</span>
                        <p class="text-xs text-gray-500">{{ $simulation->is_published ? 'Simulasi sudah dipublikasikan' : 'Centang untuk mempublikasikan simulasi' }}</p>
                    </div>
                </label>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('studio.simulations') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition"
                        :disabled="uploading">
                    <span x-show="!uploading">Simpan Perubahan</span>
                    <span x-show="uploading" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" class="opacity-75"></path></svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function studioUpload() {
            return {
                uploading: false,
                zipName: '',
                zipSize: '',
                thumbPreview: '',
                thumbName: '',
                handleZipSelect(e) {
                    const file = e.target.files[0];
                    if (file) {
                        this.zipName = file.name;
                        this.zipSize = this.formatSize(file.size);
                    }
                },
                handleZipDrop(e) {
                    const file = e.dataTransfer.files[0];
                    if (file && file.name.endsWith('.zip')) {
                        this.$refs.zipInput.files = e.dataTransfer.files;
                        this.zipName = file.name;
                        this.zipSize = this.formatSize(file.size);
                    }
                },
                handleThumbSelect(e) {
                    const file = e.target.files[0];
                    if (file) {
                        this.thumbName = file.name;
                        const reader = new FileReader();
                        reader.onload = (ev) => { this.thumbPreview = ev.target.result; };
                        reader.readAsDataURL(file);
                    }
                },
                formatSize(bytes) {
                    if (bytes < 1024) return bytes + ' B';
                    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
                    return (bytes / 1048576).toFixed(1) + ' MB';
                }
            }
        }
    </script>
    @endpush
</x-studio-layout>
