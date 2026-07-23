<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Iklan Baru
            </h2>
            <a href="{{ route('admin.ads.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('admin.ads.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf

                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Judul Iklan</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Type & Position --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Tipe Iklan</label>
                            <select name="type" id="type" required class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="banner" {{ old('type') === 'banner' ? 'selected' : '' }}>Banner</option>
                                <option value="interstitial" {{ old('type') === 'interstitial' ? 'selected' : '' }}>Interstitial</option>
                                <option value="video" {{ old('type') === 'video' ? 'selected' : '' }}>Video</option>
                                <option value="native" {{ old('type') === 'native' ? 'selected' : '' }}>Native</option>
                                <option value="adsense" {{ old('type') === 'adsense' ? 'selected' : '' }}>AdSense</option>
                            </select>
                        </div>
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700">Posisi</label>
                            <select name="position" id="position" required class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="header">Header</option>
                                <option value="sidebar">Sidebar</option>
                                <option value="pre_roll">Pre-Roll</option>
                                <option value="mid_roll">Mid-Roll</option>
                                <option value="post_simulation">Post-Simulation</option>
                                <option value="feed_sponsored">Feed Sponsored</option>
                                <option value="search_sponsored">Search Sponsored</option>
                            </select>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700">Konten HTML (untuk banner/interstitial)</label>
                        <textarea name="content" id="content" rows="4" placeholder="<iframe src='...' /> atau HTML konten iklan"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm font-mono">{{ old('content') }}</textarea>
                        @error('content') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Image --}}
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700">Gambar Iklan (opsional)</label>
                        <input type="file" name="image" id="image" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-xs text-gray-400">JPG/PNG/WebP, maks 512KB. Ukuran: 728x90, 300x250, 160x600</p>
                        @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Target URL --}}
                    <div>
                        <label for="target_url" class="block text-sm font-medium text-gray-700">URL Tujuan Klik</label>
                        <input type="url" name="target_url" id="target_url" value="{{ old('target_url') }}" placeholder="https://example.com"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        @error('target_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- AdSense --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="adsense_publisher_id" class="block text-sm font-medium text-gray-700">AdSense Publisher ID</label>
                            <input type="text" name="adsense_publisher_id" id="adsense_publisher_id" value="{{ old('adsense_publisher_id') }}" placeholder="ca-pub-XXXXX"
                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label for="adsense_ad_slot" class="block text-sm font-medium text-gray-700">AdSense Ad Slot</label>
                            <input type="text" name="adsense_ad_slot" id="adsense_ad_slot" value="{{ old('adsense_ad_slot') }}" placeholder="XXXXX"
                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                    </div>

                    {{-- Weight & Schedule --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700">Bobot Prioritas</label>
                            <input type="number" name="weight" id="weight" value="{{ old('weight', 1) }}" min="1" max="100"
                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Mulai Tayang</label>
                            <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Akhir Tayang</label>
                            <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                    </div>

                    {{-- Active --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                        <label class="text-sm text-gray-700">Iklan Aktif</label>
                    </div>

                    {{-- Submit --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.ads.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Batal</a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                            Simpan Iklan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
