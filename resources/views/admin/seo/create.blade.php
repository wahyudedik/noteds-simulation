<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Pengaturan SEO
            </h2>
            <a href="{{ route('admin.seo.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.seo.store') }}">
                    @csrf

                    <div class="space-y-6">
                        {{-- Page Key --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Page Key <span class="text-red-500">*</span></label>
                            <input type="text" name="page_key" value="{{ old('page_key') }}" required
                                placeholder="contoh: home, simulation:{slug}, category:{name}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <p class="text-xs text-gray-400 mt-1">Identifier unik halaman. Gunakan <code class="bg-gray-100 px-1 rounded">simulation:slug</code> untuk simulasi spesifik.</p>
                        </div>

                        {{-- Meta Title --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title <span class="text-red-500">*</span></label>
                            <input type="text" name="meta_title" value="{{ old('meta_title') }}" required maxlength="255"
                                placeholder="Judul untuk search engine (50-60 karakter ideal)"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        {{-- Meta Description --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description <span class="text-red-500">*</span></label>
                            <textarea name="meta_description" rows="3" required maxlength="500"
                                placeholder="Deskripsi untuk search engine (150-160 karakter ideal)"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">{{ old('meta_description') }}</textarea>
                        </div>

                        {{-- Meta Keywords --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
                            <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}" maxlength="500"
                                placeholder="keyword1, keyword2, keyword3"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        <hr class="border-gray-200">

                        <h3 class="text-sm font-semibold text-gray-900">Open Graph (Social Media)</h3>

                        {{-- OG Title --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">OG Title</label>
                            <input type="text" name="og_title" value="{{ old('og_title') }}" maxlength="255"
                                placeholder="Judul saat dibagikan di social media"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        {{-- OG Description --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">OG Description</label>
                            <textarea name="og_description" rows="2" maxlength="500"
                                placeholder="Deskripsi saat dibagikan di social media"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">{{ old('og_description') }}</textarea>
                        </div>

                        {{-- OG Image --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">OG Image URL</label>
                            <input type="url" name="og_image" value="{{ old('og_image') }}" maxlength="500"
                                placeholder="https://example.com/image.jpg"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        {{-- Canonical URL --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Canonical URL</label>
                            <input type="url" name="canonical_url" value="{{ old('canonical_url') }}" maxlength="500"
                                placeholder="https://noteds.test/page-key"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        <hr class="border-gray-200">

                        <h3 class="text-sm font-semibold text-gray-900">Structured Data (Schema.org JSON-LD)</h3>

                        {{-- Structured Data --}}
                        <div>
                            <textarea name="structured_data" rows="8"
                                placeholder='{"@@context":"https://schema.org","@@type":"WebPage","name":"Page Name"}'
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-mono text-xs">{{ old('structured_data') }}</textarea>
                            <p class="text-xs text-gray-400 mt-1">Format JSON valid. Kosongkan jika tidak diperlukan.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-6">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                            Simpan
                        </button>
                        <a href="{{ route('admin.seo.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
