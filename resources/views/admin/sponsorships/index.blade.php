<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Kelola Sponsorship
            </h2>
            <a href="{{ route('admin.sponsorships.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                + Buat Sponsorship
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Dashboard Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_sponsorships'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Aktif</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_invoices'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Invoice Pending</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_budget'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total Budget</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_spent'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total Terkonsumsi</p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="mb-6 bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                <form action="{{ route('admin.sponsorships.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Judul</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul sponsorship..."
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div class="w-44">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending_review" {{ request('status') === 'pending_review' ? 'selected' : '' }}>Menunggu Review</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>Dijeda</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.sponsorships.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Sponsorships Table --}}
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($sponsorships->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Sponsorship</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Sponsor</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Paket</th>
                                        <th class="text-right py-3 px-2 text-gray-500 font-medium">Budget</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Periode</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Status</th>
                                        <th class="text-right py-3 px-2 text-gray-500 font-medium">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sponsorships as $sponsorship)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-3 px-2">
                                            <a href="{{ route('admin.sponsorships.show', $sponsorship) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $sponsorship->title }}</a>
                                        </td>
                                        <td class="py-3 px-2 text-gray-700">{{ $sponsorship->sponsor->company_name }}</td>
                                        <td class="py-3 px-2">
                                            <span class="text-xs font-medium text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">{{ $sponsorship->package_label }}</span>
                                        </td>
                                        <td class="py-3 px-2 text-right text-gray-700">
                                            <div>Rp {{ number_format($sponsorship->budget, 0, ',', '.') }}</div>
                                            <div class="text-xs text-gray-500">Sisa: Rp {{ number_format($sponsorship->remaining_budget, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="py-3 px-2 text-center text-xs text-gray-500">
                                            {{ $sponsorship->start_date->format('d M Y') }}<br>
                                            <span class="text-gray-400">s/d</span><br>
                                            {{ $sponsorship->end_date->format('d M Y') }}
                                        </td>
                                        <td class="py-3 px-2 text-center">
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium {{ $sponsorship->status_color }} rounded-full">{{ $sponsorship->status_label }}</span>
                                        </td>
                                        <td class="py-3 px-2 text-right space-x-2">
                                            <a href="{{ route('admin.sponsorships.show', $sponsorship) }}" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Detail</a>
                                            <a href="{{ route('admin.sponsorships.edit', $sponsorship) }}" class="text-amber-600 hover:text-amber-700 text-xs font-medium">Edit</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $sponsorships->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <p class="text-sm">Belum ada sponsorship.</p>
                            <a href="{{ route('admin.sponsorships.create') }}" class="mt-3 inline-block text-blue-600 hover:text-blue-700 text-sm font-medium">+ Buat Sponsorship Pertama</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
