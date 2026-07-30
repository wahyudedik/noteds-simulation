<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan: {{ $network->display_name }}
            </h2>
            <a href="{{ route('admin.ad-networks.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('admin.ad-networks.update', $network) }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Network Info --}}
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $network->display_name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $network->network }}</p>
                    </div>

                    {{-- Enable Toggle --}}
                    <div class="flex items-center gap-3">
                        <input type="hidden" name="is_enabled" value="0">
                        <input type="checkbox" name="is_enabled" value="1" {{ $network->is_enabled ? 'checked' : '' }}
                               class="rounded border-gray-300 text-green-600 dark:text-green-400 focus:ring-green-500">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktifkan Network Ini</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Jika diaktifkan, iklan dari network ini akan tersedia untuk dipasang</p>
                        </div>
                    </div>

                    {{-- Publisher ID --}}
                    <div>
                        <label for="publisher_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Publisher / Partner ID</label>
                        <input type="text" name="publisher_id" id="publisher_id" value="{{ old('publisher_id', $network->publisher_id) }}"
                               placeholder="Contoh: ca-pub-1234567890 atau 12345"
                               class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">ID dari dashboard ad network masing-masing</p>
                    </div>

                    {{-- Site ID --}}
                    <div>
                        <label for="site_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Site / Property ID</label>
                        <input type="text" name="site_id" id="site_id" value="{{ old('site_id', $network->site_id) }}"
                               placeholder="Contoh: noteds.com atau abc123"
                               class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    {{-- Script Tag --}}
                    <div>
                        <label for="script_tag" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Script Tag Global (untuk <head>)</label>
                        <textarea name="script_tag" id="script_tag" rows="3"
                                  placeholder='<script async src="https://example.com/script.js"></script>'
                                  class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm font-mono">{{ old('script_tag', $network->script_tag) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Script ini akan dimuat di semua halaman. Kosongkan jika tidak perlu.</p>
                    </div>

                    {{-- Ads.txt Entry --}}
                    <div>
                        <label for="ads_txt_entry" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ads.txt Entry</label>
                        <input type="text" name="ads_txt_entry" id="ads_txt_entry" value="{{ old('ads_txt_entry', $network->ads_txt_entry) }}"
                               placeholder="domain.com, pub-12345, DIRECT, abcdef"
                               class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm font-mono">
                    </div>

                    {{-- Safety: Allowed Ad Types --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Format Iklan yang Diizinkan (Keamanan)</label>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="allow_banner" value="0">
                                <input type="checkbox" name="allow_banner" value="1" {{ $network->allow_banner ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-green-600 dark:text-green-400">
                                <label class="text-sm text-gray-700 dark:text-gray-300">Banner ✓</label>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="allow_native" value="0">
                                <input type="checkbox" name="allow_native" value="1" {{ $network->allow_native ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-green-600 dark:text-green-400">
                                <label class="text-sm text-gray-700 dark:text-gray-300">Native ✓</label>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="allow_video" value="0">
                                <input type="checkbox" name="allow_video" value="1" {{ $network->allow_video ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-yellow-600 dark:text-yellow-400">
                                <label class="text-sm text-gray-700 dark:text-gray-300">Video ⚠</label>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="allow_interstitial" value="0">
                                <input type="checkbox" name="allow_interstitial" value="1" {{ $network->allow_interstitial ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-yellow-600 dark:text-yellow-400">
                                <label class="text-sm text-gray-700 dark:text-gray-300">Interstitial ⚠</label>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-red-500 dark:text-red-400">Pop-under <strong>dinonaktifkan</strong> untuk semua network demi keamanan pengguna.</p>
                    </div>

                    {{-- Estimated RPM --}}
                    <div>
                        <label for="estimated_rpm" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Est. RPM ($)</label>
                        <input type="number" name="estimated_rpm" id="estimated_rpm" value="{{ old('estimated_rpm', $network->estimated_rpm) }}"
                               step="0.01" min="0" max="100"
                               class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    {{-- Ad Unit Slots (Dynamic) --}}
                    <div x-data="adSlotManager()">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ad Unit Slots (Zone ID per Posisi)</label>
                        <div class="space-y-2">
                            <template x-for="(slot, index) in slots" :key="index">
                                <div class="flex items-center gap-2">
                                    <input type="hidden" :name="'slot_keys[' + index + ']'" :value="slot.key">
                                    <input type="text" :value="slot.key" disabled
                                           class="w-1/3 bg-gray-100 dark:bg-gray-600 text-sm rounded-lg border-0 text-gray-500 dark:text-gray-400">
                                    <input type="text" x-model="slot.value" :name="'slot_values[' + index + ']'"
                                           placeholder="Zone ID / Slot ID"
                                           class="flex-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <button type="button" @click="slots.splice(index, 1)" class="text-red-400 hover:text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="addSlot()" class="mt-2 text-xs text-blue-600 dark:text-blue-400 hover:underline">+ Tambah Slot</button>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Map posisi iklan ke Zone ID / Slot ID dari dashboard ad network.</p>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan</label>
                        <textarea name="notes" id="notes" rows="2"
                                  class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">{{ old('notes', $network->notes) }}</textarea>
                    </div>

                    {{-- Submit --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('admin.ad-networks.index') }}" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800">Batal</a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function adSlotManager() {
            return {
                @php
                    $existing = $network->ad_unit_slots ?? [];
                @endphp
                slots: [
                    @foreach($existing as $key => $value)
                    { key: '{{ $key }}', value: '{{ $value }}' },
                    @endforeach
                ],
                addSlot() {
                    const positions = ['header', 'sidebar', 'footer', 'in_content', 'feed_sponsored'];
                    const used = this.slots.map(s => s.key);
                    const available = positions.filter(p => !used.includes(p));
                    if (available.length > 0) {
                        this.slots.push({ key: available[0], value: '' });
                    }
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
