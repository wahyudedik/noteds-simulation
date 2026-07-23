<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.marketplace.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Listing Marketplace</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Listing</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between"><dt class="text-gray-500">Simulasi</dt><dd class="font-medium text-gray-900">{{ $listing->simulation->title ?? '-' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Kreator</dt><dd class="font-medium text-gray-900">{{ $listing->creator->name ?? '-' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Harga</dt><dd class="font-medium text-gray-900">{{ $listing->formatted_price }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Lisensi</dt><dd class="font-medium text-gray-900">{{ $listing->license_type }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Demo</dt><dd class="font-medium text-gray-900">{{ $listing->demo_available ? "Ya ({$listing->demo_limit_minutes} menit)" : 'Tidak' }}</dd></div>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between"><dt class="text-gray-500">Total Penjualan</dt><dd class="font-bold text-blue-600">{{ $listing->total_sales }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Total Revenue</dt><dd class="font-bold text-emerald-600">Rp {{ number_format($listing->total_revenue, 0, ',', '.') }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Status</dt>
                                <dd>
                                    @if ($listing->is_active)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">Aktif</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Nonaktif</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Purchases --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Riwayat Pembelian</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembeli</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($purchases as $purchase)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $purchase->user->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $purchase->formatted_amount }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $purchase->payment_method ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $purchase->status_badge_class }}">{{ $purchase->payment_status }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $purchase->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">Belum ada pembelian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $purchases->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
