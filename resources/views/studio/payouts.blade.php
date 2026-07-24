<x-studio-layout title="Payout">
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Balance Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500">Saldo Tersedia</p>
                <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($availableBalance, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-1">Minimum payout: Rp {{ number_format($minPayout, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500">Total Sudah Dibayar</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500">Metode Pembayaran</p>
                @if($paymentSettings)
                    <p class="text-sm font-medium text-gray-900 mt-1">
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

        {{-- Request Payout --}}
        @if($availableBalance >= $minPayout)
            <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ajukan Payout</h3>
                <form method="POST" action="{{ route('studio.payouts.request') }}">
                    @csrf
                    <div class="flex items-end gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp)</label>
                            <input type="number" name="amount" min="500000" max="{{ $availableBalance }}" value="{{ $availableBalance }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                required>
                            <p class="text-xs text-gray-400 mt-1">Maks: Rp {{ number_format($availableBalance, 0, ',', '.') }}</p>
                        </div>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"
                            onclick="return confirm('Yakin ingin mengajukan payout?')">
                            Ajukan Payout
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                <div class="text-center py-4">
                    <svg class="mx-auto w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <p class="text-gray-500 text-sm">Saldo belum mencukupi untuk payout.</p>
                    <p class="text-xs text-gray-400 mt-1">Minimum payout: Rp {{ number_format($minPayout, 0, ',', '.') }}. Saldo saat ini: Rp {{ number_format($availableBalance, 0, ',', '.') }}</p>
                </div>
            </div>
        @endif

        {{-- Payout History --}}
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Payout</h3>
                @if($payouts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-2 text-gray-500 font-medium">#</th>
                                    <th class="text-right py-3 px-2 text-gray-500 font-medium">Jumlah</th>
                                    <th class="text-left py-3 px-2 text-gray-500 font-medium">Metode</th>
                                    <th class="text-center py-3 px-2 text-gray-500 font-medium">Status</th>
                                    <th class="text-left py-3 px-2 text-gray-500 font-medium">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payouts as $payout)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-2 text-gray-500 font-mono text-xs">#{{ $payout->id }}</td>
                                    <td class="py-3 px-2 text-right font-semibold text-gray-900">{{ $payout->formatted_amount }}</td>
                                    <td class="py-3 px-2 text-gray-500">
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
                                    <td class="py-3 px-2 text-gray-400 text-xs">{{ $payout->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $payouts->links() }}
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <p class="text-sm">Belum ada riwayat payout.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-studio-layout>
