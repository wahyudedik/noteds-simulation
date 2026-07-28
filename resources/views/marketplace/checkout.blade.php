<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" /></svg>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Checkout</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                ['label' => 'Home', 'url' => route('home')],
                ['label' => 'Marketplace', 'url' => route('marketplace.index')],
                ['label' => $simulation->title, 'url' => route('marketplace.show', $simulation->slug)],
                ['label' => 'Checkout'],
            ]" />

            <div class="mt-6 grid grid-cols-1 lg:grid-cols-5 gap-8">
                {{-- Order Summary --}}
                <div class="lg:col-span-3">
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Ringkasan Pesanan</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-700">
                                    @if($simulation->thumbnail)
                                        <img src="{{ asset('storage/' . $simulation->thumbnail) }}" alt="{{ $simulation->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $simulation->title }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        oleh {{ $simulation->user->name }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                                            {{ $listing->license_type === 'single' ? 'Single User' : ($listing->license_type === 'institutional' ? 'Institutional' : 'Subscription') }}
                                        </span>
                                        @if($listing->demo_available)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                                Demo tersedia
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Info --}}
                    <div class="mt-6 bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Informasi Pembayaran</h3>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Pembayaran akan diproses melalui Midtrans. Anda akan melihat popup pembayaran Midtrans setelah menekan tombol "Bayar Sekarang".
                            </p>
                            <div class="mt-4 grid grid-cols-3 gap-3">
                                <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Transfer Bank</div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-1">BCA, BNI, Mandiri</div>
                                </div>
                                <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">E-Wallet</div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-1">GoPay, OVO, DANA</div>
                                </div>
                                <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Kartu Kredit</div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-1">Visa, Mastercard</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Summary --}}
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden sticky top-6">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Pembayaran</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Harga</span>
                                <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $listing->formatted_price }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Biaya Platform (20%)</span>
                                <span class="text-gray-900 dark:text-gray-100 font-medium">Rp 0</span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <div class="flex justify-between">
                                    <span class="text-base font-semibold text-gray-900 dark:text-gray-100">Total</span>
                                    <span class="text-base font-bold text-emerald-600 dark:text-emerald-400">{{ $listing->formatted_price }}</span>
                                </div>
                            </div>

                            {{-- Pay Button --}}
                            <button
                                id="pay-button"
                                class="w-full mt-4 inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                @if($snap_token && !$is_mock) onclick="payWithSnap()" @endif
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                                Bayar Sekarang
                            </button>

                            <p class="text-xs text-center text-gray-500 dark:text-gray-400 mt-3">
                                Dengan melakukan pembayaran, Anda menyetujui Syarat & Ketentuan yang berlaku.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Midtrans Snap.js --}}
    @if($snap_token && !$is_mock)
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $client_key }}"></script>
        <script>
            function payWithSnap() {
                snap.pay('{{ $snap_token }}', {
                    onSuccess: function(result) {
                        window.location.href = '{{ route("marketplace.success") }}?order_id=' + result.order_id;
                    },
                    onPending: function(result) {
                        window.location.href = '{{ route("marketplace.success") }}?order_id=' + result.order_id;
                    },
                    onError: function(result) {
                        alert('Pembayaran gagal. Silakan coba lagi.');
                        window.location.href = '{{ route("marketplace.show", $simulation->slug) }}';
                    },
                    onClose: function() {
                        // User closed the popup
                    }
                });
            }
        </script>
    @elseif($is_mock)
        <script>
            document.getElementById('pay-button').addEventListener('click', function() {
                // Mock mode - simulate successful payment
                fetch('{{ route("marketplace.callback") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        order_id: '{{ $purchase->midtrans_order_id }}',
                        status_code: '200',
                        transaction_status: 'settlement',
                        gross_amount: '{{ $purchase->amount }}',
                        payment_type: 'mock',
                        server_key: '',
                        signature_key: ''
                    })
                }).then(function() {
                    window.location.href = '{{ route("marketplace.success") }}?order_id={{ $purchase->midtrans_order_id }}';
                });
            });
        </script>
    @endif
</x-app-layout>
