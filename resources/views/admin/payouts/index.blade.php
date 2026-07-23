<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Manajemen Payout
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Menunggu Review</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['pending_count'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">Rp {{ number_format($stats['total_pending'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Disetujui</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($stats['total_approved'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Sudah Dibayar</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">Rp {{ number_format($stats['total_paid'], 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $stats['paid_count'] }} transaksi</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Total Creator</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_creators'] }}</p>
                </div>
            </div>

            {{-- Status Tabs & Search --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-2">
                    @foreach([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'paid' => 'Dibayar',
                        'rejected' => 'Ditolak',
                        'all' => 'Semua',
                    ] as $key => $label)
                        <a href="{{ route('admin.payouts.index', ['status' => $key]) }}"
                            class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ $status === $key ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
                <form method="GET" action="{{ route('admin.payouts.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/email..."
                        class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" class="px-3 py-1.5 text-sm bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Cari</button>
                </form>
            </div>

            {{-- Payouts Table --}}
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($payouts->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Creator</th>
                                        <th class="text-right py-3 px-2 text-gray-500 font-medium">Jumlah</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Metode</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Rekening</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Status</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Diajukan</th>
                                        <th class="text-right py-3 px-2 text-gray-500 font-medium">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payouts as $payout)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-3 px-2">
                                            <div class="font-medium text-gray-900">{{ $payout->user->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $payout->user->email }}</div>
                                        </td>
                                        <td class="py-3 px-2 text-right">
                                            <span class="font-semibold text-gray-900">{{ $payout->formatted_amount }}</span>
                                            @if($payout->amount_usd)
                                                <div class="text-xs text-gray-400">${{ number_format($payout->amount_usd, 2) }}</div>
                                            @endif
                                        </td>
                                        <td class="py-3 px-2">
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                                                @switch($payout->method)
                                                    @case('bank_transfer') Bank Transfer @break
                                                    @case('paypal') PayPal @break
                                                    @case('midtrans') Midtrans @break
                                                    @default {{ ucfirst($payout->method) }}
                                                @endswitch
                                            </span>
                                        </td>
                                        <td class="py-3 px-2 text-gray-500 text-xs">
                                            @if($payout->method === 'bank_transfer')
                                                {{ $payout->bank_name ?? '-' }}<br>{{ $payout->account_number ?? '-' }}
                                            @elseif($payout->method === 'paypal')
                                                {{ $payout->paypal_email ?? '-' }}
                                            @else
                                                -
                                            @endif
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
                                        <td class="py-3 px-2 text-right">
                                            <a href="{{ route('admin.payouts.show', $payout) }}" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Detail</a>
                                        </td>
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
                            <svg class="mx-auto w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <p>Belum ada permintaan payout.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
