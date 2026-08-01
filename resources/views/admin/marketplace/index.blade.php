<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                Marketplace
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Listing</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalListings }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Aktif</div>
                    <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $activeListings }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Penjualan</div>
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalSales }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Revenue</div>
                    <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
                <form method="GET" class="flex flex-wrap gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari experience..."
                        class="flex-1 min-w-[200px] rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                    <select name="status" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition">Filter</button>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Experience</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kreator</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Lisensi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Penjualan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Revenue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($listings as $listing)
                                <tr class="hover:bg-gray-50 dark:bg-gray-700/50 dark:hover:bg-gray-600/50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $listing->simulation->title ?? '-' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $listing->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $listing->creator->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $listing->formatted_price }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-slate-100 dark:bg-slate-700 text-slate-700">{{ $listing->license_type }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $listing->total_sales }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-emerald-600 dark:text-emerald-400">Rp {{ number_format($listing->total_revenue, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        @if ($listing->is_active)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700">Aktif</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm space-x-2">
                                        <a href="{{ route('admin.marketplace.show', $listing) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800">Detail</a>
                                        <form method="POST" action="{{ route('admin.marketplace.toggle', $listing) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-amber-600 dark:text-amber-400 hover:text-amber-800">Toggle</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada listing marketplace.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $listings->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
