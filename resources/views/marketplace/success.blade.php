<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Pembayaran Berhasil</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                {{-- Success Icon --}}
                <div class="p-8 text-center border-b border-gray-200 dark:border-gray-700">
                    <div class="mx-auto w-20 h-20 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-2xl font-bold text-gray-900 dark:text-gray-100">Pembayaran Berhasil!</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Terima kasih telah membeli simulasi ini. Anda sekarang memiliki akses penuh.
                    </p>
                </div>

                {{-- Purchase Details --}}
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-700">
                            @if($simulation->thumbnail)
                                <img src="{{ asset('storage/' . $simulation->thumbnail) }}" alt="{{ $simulation->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /></svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $simulation->title }}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">oleh {{ $simulation->user->name }}</p>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-emerald-600 dark:text-emerald-400">{{ $purchase->formatted_amount }}</div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Order ID</span>
                            <span class="text-gray-900 dark:text-gray-100 font-mono text-xs">{{ $purchase->midtrans_order_id }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Status</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                Berhasil
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Tanggal</span>
                            <span class="text-gray-900 dark:text-gray-100">{{ $purchase->paid_at?->format('d M Y, H:i') ?? $purchase->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        @if($purchase->payment_method)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Metode Pembayaran</span>
                            <span class="text-gray-900 dark:text-gray-100 capitalize">{{ str_replace('_', ' ', $purchase->payment_method) }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="p-6 border-t border-gray-200 dark:border-gray-700 space-y-3">
                    <a href="{{ route('simulations.show', $simulation->slug) }}"
                       class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Mainkan Simulasi
                    </a>
                    <a href="{{ route('marketplace.history') }}"
                       class="w-full inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                        Lihat Riwayat Pembelian
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
