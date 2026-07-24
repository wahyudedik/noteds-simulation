<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.sponsorships.show', $sponsorship) }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Sponsorship: {{ $sponsorship->title }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <form method="POST" action="{{ route('admin.sponsorships.update', $sponsorship) }}">
                    @csrf
                    @method('PUT')
                    <div class="space-y-5">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Sponsorship <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title', $sponsorship->title) }}" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="package_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Paket <span class="text-red-500">*</span></label>
                                <select name="package_type" id="package_type" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    @foreach(['basic' => 'Basic', 'standard' => 'Standard', 'premium' => 'Premium', 'custom' => 'Custom'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('package_type', $sponsorship->package_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="budget" class="block text-sm font-medium text-gray-700 mb-1">Budget (Rp) <span class="text-red-500">*</span></label>
                                <input type="number" name="budget" id="budget" value="{{ old('budget', $sponsorship->budget) }}" required min="0" step="1000"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                                @error('budget') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $sponsorship->start_date->format('Y-m-d')) }}" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                                @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $sponsorship->end_date->format('Y-m-d')) }}" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                                @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Posisi Iklan <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-2">
                                @php
                                    $positions = [
                                        'header' => 'Header',
                                        'sidebar' => 'Sidebar',
                                        'pre_roll' => 'Pre-Roll',
                                        'mid_roll' => 'Mid-Roll',
                                        'post_simulation' => 'Post Simulasi',
                                        'feed_sponsored' => 'Feed Sponsored',
                                        'search_sponsored' => 'Search Sponsored',
                                    ];
                                    $currentPositions = old('positions', $sponsorship->positions ?? []);
                                @endphp
                                @foreach($positions as $value => $label)
                                    <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" name="positions[]" value="{{ $value }}" {{ in_array($value, $currentPositions) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                        <span class="text-sm text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('positions') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="target_impressions" class="block text-sm font-medium text-gray-700 mb-1">Target Impressions</label>
                                <input type="number" name="target_impressions" id="target_impressions" value="{{ old('target_impressions', $sponsorship->target_impressions) }}" min="0"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                            </div>
                            <div>
                                <label for="category_filter" class="block text-sm font-medium text-gray-700 mb-1">Filter Kategori</label>
                                <input type="text" name="category_filter" id="category_filter"
                                    value="{{ old('category_filter', is_array($sponsorship->category_filter) ? implode(', ', $sponsorship->category_filter) : '') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Kosongkan = semua kategori" />
                                <p class="text-xs text-gray-500 mt-1">Pisahkan dengan koma jika lebih dari satu</p>
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('notes', $sponsorship->notes) }}</textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <a href="{{ route('admin.sponsorships.show', $sponsorship) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Batal</a>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">Perbarui Sponsorship</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
