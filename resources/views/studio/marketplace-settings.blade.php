<x-studio-layout :pageTitle="'Marketplace Settings — ' . $simulation->title">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Marketplace: {{ $simulation->title }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Konfigurasi listing simulasi ini di marketplace</p>
        </div>
        <a href="{{ route('studio.simulations') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">← Kembali ke Simulasi</a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400 text-sm rounded-lg p-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400 text-sm rounded-lg p-3 mb-6">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Current Status --}}
    @if($listing)
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-4 mb-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Status Listing</p>
                    <div class="flex items-center gap-2 mt-1">
                        @if($listing->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                Tidak Aktif
                            </span>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Penjualan</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $listing->total_sales }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $listing->formatted_price }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Listing Form --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-6 mb-6 shadow-sm">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">
            {{ $listing ? 'Edit Listing' : 'Buat Listing Baru' }}
        </h3>

        <form action="{{ $listing
            ? route('studio.simulations.marketplace.update', $simulation->slug)
            : route('studio.simulations.marketplace.store', $simulation->slug)
        }}" method="POST">
            @csrf
            @if($listing)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Price --}}
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga (Rp)</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $listing->price ?? 50000) }}"
                        min="1000" max="100000000" step="1000" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Minimum Rp 1.000</p>
                </div>

                {{-- Currency --}}
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mata Uang</label>
                    <select name="currency" id="currency" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="IDR" {{ old('currency', $listing->currency ?? 'IDR') === 'IDR' ? 'selected' : '' }}>IDR (Rupiah)</option>
                        <option value="USD" {{ old('currency', $listing->currency ?? 'IDR') === 'USD' ? 'selected' : '' }}>USD (Dollar)</option>
                    </select>
                </div>

                {{-- License Type --}}
                <div>
                    <label for="license_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Lisensi</label>
                    <select name="license_type" id="license_type" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="single" {{ old('license_type', $listing->license_type ?? 'single') === 'single' ? 'selected' : '' }}>Single User</option>
                        <option value="institutional" {{ old('license_type', $listing->license_type ?? '') === 'institutional' ? 'selected' : '' }}>Institutional</option>
                        <option value="subscription" {{ old('license_type', $listing->license_type ?? '') === 'subscription' ? 'selected' : '' }}>Subscription</option>
                    </select>
                </div>

                {{-- Active Status --}}
                <div class="flex items-center gap-3 pt-6">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $listing->is_active ?? true) ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    </label>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Aktif di Marketplace</span>
                </div>
            </div>

            {{-- Demo Section --}}
            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3 mb-4">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="demo_available" value="0">
                        <input type="checkbox" name="demo_available" value="1" {{ old('demo_available', $listing->demo_available ?? false) ? 'checked' : '' }}
                            class="sr-only peer" x-data x-on:change="$el.closest('form').querySelector('[name=demo_limit_minutes]').disabled = !$el.checked">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    </label>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Sediakan Demo Gratis</span>
                </div>

                <div class="ml-14">
                    <label for="demo_limit_minutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Batas Waktu Demo (menit)</label>
                    <input type="number" name="demo_limit_minutes" id="demo_limit_minutes"
                        value="{{ old('demo_limit_minutes', $listing->demo_limit_minutes ?? 0) }}"
                        min="0" max="60" {{ ($listing->demo_available ?? false) ? '' : 'disabled' }}
                        class="w-full md:w-48 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <p class="text-xs text-gray-400 mt-1">0 = tanpa batas waktu</p>
                </div>
            </div>

            {{-- Submit --}}
            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white tracking-wide hover:bg-blue-500 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ $listing ? 'Simpan Perubahan' : 'Buat Listing' }}
                </button>
            </div>
        </form>
    </div>

    {{-- Delete / Remove from Marketplace --}}
    @if($listing)
        <div class="bg-white dark:bg-gray-800 border border-red-100 dark:border-red-900/50 rounded-xl p-6 shadow-sm">
            <h3 class="font-semibold text-red-600 dark:text-red-400 mb-2">Hapus dari Marketplace</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Menghapus listing akan menonaktifkan penjualan simulasi ini. Pembelian yang sudah ada tidak akan terpengaruh.
            </p>
            <form action="{{ route('studio.simulations.marketplace.remove', $simulation->slug) }}" method="POST"
                x-data
                x-on:submit.prevent="confirm('Yakin ingin menghapus listing ini dari marketplace?') && $el.submit()">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-sm text-white tracking-wide hover:bg-red-500 focus:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Hapus dari Marketplace
                </button>
            </form>
        </div>
    @endif

    {{-- Marketplace Info --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mt-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300">Tentang Marketplace</h4>
                <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">
                    Platform mengenakan biaya sebesar <strong>20%</strong> dari setiap transaksi penjualan.
                    Sisa 80% akan masuk ke saldo creator yang dapat ditarik melalui halaman Payout.
                </p>
            </div>
        </div>
    </div>
</x-studio-layout>
