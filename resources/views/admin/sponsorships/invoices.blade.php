<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.sponsorships.show', $sponsorship) }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Invoice — {{ $sponsorship->title }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Create Invoice Form --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Buat Invoice Baru</h3>
                <form action="{{ route('admin.sponsorships.invoices.create', $sponsorship) }}" method="POST" class="flex flex-wrap gap-3 items-end">
                    @csrf
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp)</label>
                        <input type="number" name="amount" required min="0" step="1000" placeholder="0"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="min-w-[150px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jatuh Tempo</label>
                        <input type="date" name="due_date" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @error('due_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <input type="text" name="notes" placeholder="Opsional"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        Buat Invoice
                    </button>
                </form>
            </div>

            {{-- Invoices List --}}
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($invoices->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Nomor Invoice</th>
                                        <th class="text-right py-3 px-2 text-gray-500 font-medium">Jumlah</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Jatuh Tempo</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Status</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Dibayar</th>
                                        <th class="text-right py-3 px-2 text-gray-500 font-medium">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-3 px-2 font-medium text-gray-900">{{ $invoice->invoice_number }}</td>
                                        <td class="py-3 px-2 text-right text-gray-900 font-medium">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                                        <td class="py-3 px-2 text-center text-gray-500">{{ $invoice->due_date->format('d M Y') }}</td>
                                        <td class="py-3 px-2 text-center">
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium {{ $invoice->status_color }} rounded-full">{{ $invoice->status_label }}</span>
                                        </td>
                                        <td class="py-3 px-2 text-center text-gray-500 text-xs">
                                            @if($invoice->paid_at)
                                                {{ $invoice->paid_at->format('d M Y') }}<br>
                                                <span class="text-gray-400">{{ $invoice->payment_method }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="py-3 px-2 text-right space-x-2">
                                            @if($invoice->status === 'draft')
                                                <form action="{{ route('admin.invoices.send', $invoice) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Kirim</button>
                                                </form>
                                            @endif
                                            @if(in_array($invoice->status, ['draft', 'sent', 'overdue']))
                                                <form action="{{ route('admin.invoices.mark-paid', $invoice) }}" method="POST" class="inline" x-data="{ show: false }">
                                                    @csrf
                                                    <input type="hidden" name="payment_method" value="Transfer Bank">
                                                    <button type="submit" class="text-green-600 hover:text-green-700 text-xs font-medium">Bayar</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($invoice->notes)
                                        <tr class="bg-gray-50">
                                            <td colspan="6" class="py-2 px-2 text-xs text-gray-500">
                                                <em>Catatan: {{ $invoice->notes }}</em>
                                            </td>
                                        </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Summary --}}
                        <div class="mt-6 pt-4 border-t border-gray-100 grid grid-cols-3 gap-4">
                            <div class="text-center">
                                <p class="text-lg font-bold text-gray-900">Rp {{ number_format($invoices->sum('amount'), 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">Total Invoice</p>
                            </div>
                            <div class="text-center">
                                <p class="text-lg font-bold text-green-600">Rp {{ number_format($invoices->where('status', 'paid')->sum('amount'), 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">Sudah Dibayar</p>
                            </div>
                            <div class="text-center">
                                <p class="text-lg font-bold text-amber-600">Rp {{ number_format($invoices->where('status', '!=', 'paid')->sum('amount'), 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">Belum Dibayar</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" /></svg>
                            <p class="text-sm">Belum ada invoice untuk sponsorship ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
