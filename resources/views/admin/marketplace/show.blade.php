<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.marketplace.index') }}" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Detail Listing Marketplace</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Listing</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Experience</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $listing->simulation->title ?? '-' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Kreator</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $listing->creator->name ?? '-' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Harga</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $listing->formatted_price }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Lisensi</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $listing->license_type }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Demo</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $listing->demo_available ? "Ya ({$listing->demo_limit_minutes} menit)" : 'Tidak' }}</dd></div>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Statistik</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Total Penjualan</dt><dd class="font-bold text-blue-600 dark:text-blue-400">{{ $listing->total_sales }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Total Revenue</dt><dd class="font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($listing->total_revenue, 0, ',', '.') }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Status</dt>
                                <dd>
                                    @if ($listing->is_active)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700">Aktif</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Nonaktif</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Purchases --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Pembelian</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Pembeli</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Metode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($purchases as $purchase)
                                <tr class="hover:bg-gray-50 dark:bg-gray-700/50 dark:hover:bg-gray-600/50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $purchase->user->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 dark:text-gray-300">{{ $purchase->formatted_amount }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 dark:text-gray-300">{{ $purchase->payment_method ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $purchase->status_badge_class }}">{{ $purchase->payment_status }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $purchase->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada pembelian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $purchases->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
