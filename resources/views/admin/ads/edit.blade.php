<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Iklan: {{ $ad->title }}
            </h2>
            <a href="{{ route('admin.ads.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('admin.ads.update', $ad) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Judul Iklan</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $ad->title) }}" required
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Type & Position --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Tipe Iklan</label>
                            <select name="type" id="type" required class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                @foreach(['banner', 'interstitial', 'video', 'native', 'adsense'] as $type)
                                    <option value="{{ $type }}" {{ old('type', $ad->type) === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700">Posisi</label>
                            <select name="position" id="position" required class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                @foreach(['header' => 'Header', 'sidebar' => 'Sidebar', 'pre_roll' => 'Pre-Roll', 'mid_roll' => 'Mid-Roll', 'post_simulation' => 'Post-Simulation', 'feed_sponsored' => 'Feed Sponsored', 'search_sponsored' => 'Search Sponsored'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('position', $ad->position) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700">Konten HTML</label>
                        <textarea name="content" id="content" rows="4"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm font-mono">{{ old('content', $ad->content) }}</textarea>
                    </div>

                    {{-- Current Image --}}
                    @if($ad->image_path)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gambar Saat Ini</label>
                            <img src="{{ asset('storage/' . $ad->image_path) }}" alt="{{ $ad->title }}" class="mt-1 h-20 rounded-lg object-contain border">
                        </div>
                    @endif

                    {{-- New Image --}}
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700">Ganti Gambar (opsional)</label>
                        <input type="file" name="image" id="image" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    {{-- Target URL --}}
                    <div>
                        <label for="target_url" class="block text-sm font-medium text-gray-700">URL Tujuan Klik</label>
                        <input type="url" name="target_url" id="target_url" value="{{ old('target_url', $ad->target_url) }}"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    {{-- AdSense --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="adsense_publisher_id" class="block text-sm font-medium text-gray-700">AdSense Publisher ID</label>
                            <input type="text" name="adsense_publisher_id" id="adsense_publisher_id" value="{{ old('adsense_publisher_id', $ad->adsense_publisher_id) }}"
                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label for="adsense_ad_slot" class="block text-sm font-medium text-gray-700">AdSense Ad Slot</label>
                            <input type="text" name="adsense_ad_slot" id="adsense_ad_slot" value="{{ old('adsense_ad_slot', $ad->adsense_ad_slot) }}"
                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                    </div>

                    {{-- Weight & Schedule --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700">Bobot Prioritas</label>
                            <input type="number" name="weight" id="weight" value="{{ old('weight', $ad->weight) }}" min="1" max="100"
                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Mulai Tayang</label>
                            <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date', $ad->start_date?->format('Y-m-d\TH:i')) }}"
                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Akhir Tayang</label>
                            <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date', $ad->end_date?->format('Y-m-d\TH:i')) }}"
                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                    </div>

                    {{-- Active --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $ad->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                        <label class="text-sm text-gray-700">Iklan Aktif</label>
                    </div>

                    {{-- Submit --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.ads.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Batal</a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                            Update Iklan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
