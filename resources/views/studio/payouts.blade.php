<x-studio-layout :pageTitle="'Payouts'">
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Balance Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500 dark:text-gray-400">Saldo Tersedia</p>
                <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($availableBalance, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Minimum payout: Rp {{ number_format($minPayout, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Sudah Dibayar</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500 dark:text-gray-400">Metode Pembayaran</p>
                @if($paymentSettings)
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                        @switch($paymentSettings->preferred_method)
                            @case('bank_transfer') {{ $paymentSettings->bank_name ?? 'Bank Transfer' }} @break
                            @case('paypal') PayPal @break
                            @case('midtrans') Midtrans @break
                            @default {{ ucfirst($paymentSettings->preferred_method) }}
                        @endswitch
                    </p>
                    <a href="{{ route('studio.payment-settings') }}" class="text-xs text-blue-600 hover:underline mt-1 inline-block">Update →</a>
                @else
                    <p class="text-sm text-yellow-600 mt-1">Belum diatur</p>
                    <a href="{{ route('studio.payment-settings') }}" class="text-xs text-blue-600 hover:underline mt-1 inline-block">Atur Sekarang →</a>
                @endif
            </div>
        </div>

        {{-- Earnings Breakdown --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Rincian Pendapatan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Ad Revenue --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" /></svg>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Pendapatan Iklan</span>
                    </div>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($earnings['ad_revenue'], 0, ',', '.') }}</p>
                </div>

                {{-- Marketplace Revenue --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" /></svg>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Pendapatan Marketplace</span>
                    </div>
                    <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($earnings['marketplace_earnings'], 0, ',', '.') }}</p>
                    @if($earnings['marketplace_gross_sales'] > 0)
                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 space-y-0.5">
                            <p>Penjualan kotor: Rp {{ number_format($earnings['marketplace_gross_sales'], 0, ',', '.') }}</p>
                            <p>Platform fee ({{ config('midtrans.platform_fee_percentage', 20) }}%): -Rp {{ number_format($earnings['marketplace_platform_fee'], 0, ',', '.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Total Summary --}}
            <div class="mt-4 p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-200 dark:border-emerald-800">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-emerald-700 dark:text-emerald-300">Total Pendapatan Bersih</span>
                    <span class="text-xl font-bold text-emerald-700 dark:text-emerald-300">Rp {{ number_format($earnings['total_net'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Request Payout --}}
        @if($availableBalance >= $minPayout)
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ajukan Payout</h3>
                <form method="POST" action="{{ route('studio.payouts.request') }}">
                    @csrf
                    <div class="flex items-end gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah (Rp)</label>
                            <input type="number" name="amount" min="500000" max="{{ $availableBalance }}" value="{{ $availableBalance }}"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                required>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Maks: Rp {{ number_format($availableBalance, 0, ',', '.') }}</p>
                        </div>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"
                            onclick="confirmSubmit(this.closest('form'), 'Yakin ingin mengajukan payout? Minimum payout adalah Rp 500.000.', { title: 'Ajukan Payout', confirmText: 'Ya, Ajukan' })">
                            Ajukan Payout
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-6 shadow-sm">
                <div class="text-center py-4">
                    <svg class="mx-auto w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Saldo belum mencukupi untuk payout.</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Minimum payout: Rp {{ number_format($minPayout, 0, ',', '.') }}. Saldo saat ini: Rp {{ number_format($availableBalance, 0, ',', '.') }}</p>
                </div>
            </div>
        @endif

        {{-- Payout History --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Riwayat Payout</h3>
                @if($payouts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-600">
                                    <th class="text-left py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">#</th>
                                    <th class="text-right py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Jumlah</th>
                                    <th class="text-left py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Metode</th>
                                    <th class="text-center py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Status</th>
                                    <th class="text-left py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payouts as $payout)
                                <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-3 px-2 text-gray-500 dark:text-gray-400 font-mono text-xs">#{{ $payout->id }}</td>
                                    <td class="py-3 px-2 text-right font-semibold text-gray-900 dark:text-white">{{ $payout->formatted_amount }}</td>
                                    <td class="py-3 px-2 text-gray-500 dark:text-gray-400">
                                        @switch($payout->method)
                                            @case('bank_transfer') Bank Transfer @break
                                            @case('paypal') PayPal @break
                                            @case('midtrans') Midtrans @break
                                            @default {{ ucfirst($payout->method) }}
                                        @endswitch
                                    </td>
                                    <td class="py-3 px-2 text-center">
                                        <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ $payout->status_badge_class }}">
                                            @switch($payout->status)
                                                @case('pending') Menunggu @break
                                                @case('processing') Diproses @break
                                                @case('approved') Disetujui @break
                                                @case('paid') Dibayar @break
                                                @case('rejected') Ditolak @break
                                                @default {{ ucfirst($payout->status) }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td class="py-3 px-2 text-gray-400 dark:text-gray-500 text-xs">{{ $payout->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $payouts->links() }}
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <p class="text-sm">Belum ada riwayat payout.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-studio-layout>
