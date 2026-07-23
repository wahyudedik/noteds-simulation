<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit SEO: {{ $seo->page_key }}
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

                <form method="POST" action="{{ route('admin.seo.update', $seo) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        {{-- Page Key (Read Only) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Page Key</label>
                            <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600 font-mono">{{ $seo->page_key }}</div>
                        </div>

                        {{-- Meta Title --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title <span class="text-red-500">*</span></label>
                            <input type="text" name="meta_title" value="{{ old('meta_title', $seo->meta_title) }}" required maxlength="255"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        {{-- Meta Description --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description <span class="text-red-500">*</span></label>
                            <textarea name="meta_description" rows="3" required maxlength="500"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">{{ old('meta_description', $seo->meta_description) }}</textarea>
                        </div>

                        {{-- Meta Keywords --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
                            <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $seo->meta_keywords) }}" maxlength="500"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        <hr class="border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900">Open Graph (Social Media)</h3>

                        {{-- OG Title --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">OG Title</label>
                            <input type="text" name="og_title" value="{{ old('og_title', $seo->og_title) }}" maxlength="255"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        {{-- OG Description --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">OG Description</label>
                            <textarea name="og_description" rows="2" maxlength="500"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">{{ old('og_description', $seo->og_description) }}</textarea>
                        </div>

                        {{-- OG Image --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">OG Image URL</label>
                            <input type="url" name="og_image" value="{{ old('og_image', $seo->og_image) }}" maxlength="500"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        {{-- Canonical URL --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Canonical URL</label>
                            <input type="url" name="canonical_url" value="{{ old('canonical_url', $seo->canonical_url) }}" maxlength="500"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        <hr class="border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900">Structured Data (Schema.org JSON-LD)</h3>

                        {{-- Structured Data --}}
                        <div>
                            <textarea name="structured_data" rows="8"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-mono text-xs">{{ old('structured_data', $seo->structured_data ? json_encode($seo->structured_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                            <p class="text-xs text-gray-400 mt-1">Format JSON valid. Kosongkan jika tidak diperlukan.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-6">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                            Update
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
