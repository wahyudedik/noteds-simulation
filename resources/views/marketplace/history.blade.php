<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Riwayat Pembelian</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                ['label' => 'Home', 'url' => route('home')],
                ['label' => 'Riwayat Pembelian'],
            ]" />

            @if($purchases->count() > 0)
                <div class="mt-6 space-y-4">
                    @foreach($purchases as $purchase)
                        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-start gap-4">
                                    {{-- Thumbnail --}}
                                    <div class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-700">
                                        @if($purchase->simulation->thumbnail)
                                            <img src="{{ asset('storage/' . $purchase->simulation->thumbnail) }}" alt="{{ $purchase->simulation->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /></svg>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                                                    <a href="{{ route('marketplace.show', $purchase->simulation->slug) }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                                        {{ $purchase->simulation->title }}
                                                    </a>
                                                </h4>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                                    oleh {{ $purchase->simulation->user->name }}
                                                </p>
                                            </div>
                                            <div class="text-right ml-4">
                                                <div class="font-bold text-gray-900 dark:text-gray-100">{{ $purchase->formatted_amount }}</div>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $purchase->status_badge_class }}">
                                                    {{ ucfirst($purchase->payment_status) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-4 mt-3 text-xs text-gray-500 dark:text-gray-400">
                                            <span>{{ $purchase->created_at->format('d M Y, H:i') }}</span>
                                            @if($purchase->payment_method)
                                                <span class="capitalize">{{ str_replace('_', ' ', $purchase->payment_method) }}</span>
                                            @endif
                                            <span class="font-mono">{{ $purchase->midtrans_order_id }}</span>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="flex items-center gap-3 mt-3">
                                            @if($purchase->payment_status === 'completed')
                                                <a href="{{ route('simulations.show', $purchase->simulation->slug) }}"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /></svg>
                                                    Mainkan
                                                </a>
                                            @elseif($purchase->payment_status === 'pending')
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                    Menunggu Pembayaran
                                                </span>
                                            @elseif($purchase->payment_status === 'failed')
                                                <a href="{{ route('marketplace.show', $purchase->simulation->slug) }}"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                                                    Coba Lagi
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-6">
                        {{ $purchases->links() }}
                    </div>
                </div>
            @else
                {{-- Empty State --}}
                <div class="mt-6 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">Belum ada pembelian</h3>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">Mulai jelajahi marketplace dan temukan simulasi menarik.</p>
                    <div class="mt-6">
                        <a href="{{ route('marketplace.index') }}"
                           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                            Jelajahi Marketplace
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
