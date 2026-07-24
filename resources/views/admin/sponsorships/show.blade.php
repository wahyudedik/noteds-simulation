<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.sponsorships.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Sponsorship</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main Info --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Header Card --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $sponsorship->title }}</h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    Sponsor: <a href="{{ route('admin.sponsors.show', $sponsorship->sponsor) }}" class="text-blue-600 hover:underline">{{ $sponsorship->sponsor->company_name }}</a>
                                </p>
                            </div>
                            <span class="inline-flex px-3 py-1 text-sm font-medium {{ $sponsorship->status_color }} rounded-full">{{ $sponsorship->status_label }}</span>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if($sponsorship->status === 'pending_review')
                                <form action="{{ route('admin.sponsorships.approve', $sponsorship) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                                        Setujui & Aktifkan
                                    </button>
                                </form>
                            @endif
                            @if($sponsorship->status === 'active')
                                <form action="{{ route('admin.sponsorships.pause', $sponsorship) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition">
                                        ⏸ Jeda
                                    </button>
                                </form>
                                <form action="{{ route('admin.sponsorships.complete', $sponsorship) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                                        Selesai
                                    </button>
                                </form>
                            @endif
                            @if($sponsorship->status === 'paused')
                                <form action="{{ route('admin.sponsorships.resume', $sponsorship) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        Lanjutkan
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('admin.sponsorships.edit', $sponsorship) }}" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Edit</a>
                            <a href="{{ route('admin.sponsorships.invoices', $sponsorship) }}" class="px-3 py-1.5 text-xs font-medium text-purple-700 bg-purple-100 hover:bg-purple-200 rounded-lg transition inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Invoice
                            </a>
                        </div>

                        {{-- Details Grid --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-gray-100">
                            <div>
                                <p class="text-xs text-gray-500">Paket</p>
                                <p class="text-sm font-medium text-gray-900">{{ $sponsorship->package_label }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Budget</p>
                                <p class="text-sm font-medium text-gray-900">Rp {{ number_format($sponsorship->budget, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Terkonsumsi</p>
                                <p class="text-sm font-medium text-gray-900">Rp {{ number_format($sponsorship->spent, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Sisa Budget</p>
                                <p class="text-sm font-medium {{ $sponsorship->remaining_budget > 0 ? 'text-green-600' : 'text-red-600' }}">Rp {{ number_format($sponsorship->remaining_budget, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        {{-- Budget Progress --}}
                        <div class="mt-4">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Progress Budget</span>
                                <span>{{ $sponsorship->progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ $sponsorship->progress }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Performance Stats --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_impressions'] ?? $sponsorship->total_impressions) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Impressions</p>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_clicks'] ?? $sponsorship->total_clicks) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Clicks</p>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['ctr'] ?? $sponsorship->ctr, 2) }}%</p>
                            <p class="text-xs text-gray-500 mt-1">CTR</p>
                        </div>
                    </div>

                    {{-- Linked Ads --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Iklan Terkait ({{ $sponsorship->platformAds->count() }})</h3>
                        @if($sponsorship->platformAds->count() > 0)
                            <div class="space-y-3">
                                @foreach($sponsorship->platformAds as $ad)
                                    <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 hover:bg-gray-50">
                                        <div class="flex items-center gap-3">
                                            @if($ad->image_path)
                                                <img src="{{ asset('storage/' . $ad->image_path) }}" alt="{{ $ad->title }}" class="w-10 h-10 rounded-lg object-cover">
                                            @else
                                                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">IMG</div>
                                            @endif
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $ad->title }}</p>
                                                <p class="text-xs text-gray-500">{{ ucfirst($ad->position) }} · {{ $ad->type }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right text-xs text-gray-500">
                                            <div>{{ number_format($ad->impressions) }} imp / {{ number_format($ad->clicks) }} clk</div>
                                            <div class="font-medium text-gray-700">CTR: {{ $ad->ctr }}%</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 text-gray-500 text-sm">
                                <p>Belum ada iklan terkait sponsorship ini.</p>
                                <p class="text-xs mt-1">Buat iklan melalui <a href="{{ route('admin.ads.index') }}" class="text-blue-600 hover:underline">Manajemen Iklan</a> dan hubungkan ke sponsorship ini.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Invoices Preview --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-900">Invoice ({{ $invoices->count() }})</h3>
                            <a href="{{ route('admin.sponsorships.invoices', $sponsorship) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lihat Semua →</a>
                        </div>
                        @if($invoices->count() > 0)
                            <div class="space-y-2">
                                @foreach($invoices->take(3) as $invoice)
                                    <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                                            <p class="text-xs text-gray-500">Jatuh tempo: {{ $invoice->due_date->format('d M Y') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</p>
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium {{ $invoice->status_color }} rounded-full">{{ $invoice->status_label }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-gray-500 text-sm">Belum ada invoice.</div>
                        @endif
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Positions --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Posisi Iklan</h3>
                        <div class="flex flex-wrap gap-2">
                            @if($sponsorship->positions)
                                @foreach($sponsorship->positions as $pos)
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">{{ ucfirst(str_replace('_', ' ', $pos)) }}</span>
                                @endforeach
                            @else
                                <span class="text-xs text-gray-500">Semua posisi</span>
                            @endif
                        </div>
                    </div>

                    {{-- Schedule --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Jadwal</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Mulai</span>
                                <span class="text-gray-900">{{ $sponsorship->start_date->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Selesai</span>
                                <span class="text-gray-900">{{ $sponsorship->end_date->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Durasi</span>
                                <span class="text-gray-900">{{ $sponsorship->start_date->diffInDays($sponsorship->end_date) }} hari</span>
                            </div>
                            @if($sponsorship->isCurrentlyRunning())
                                <div class="pt-2">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                                        Sedang Berjalan
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Category Filter --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Filter Kategori</h3>
                        @if($sponsorship->category_filter && count($sponsorship->category_filter) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($sponsorship->category_filter as $cat)
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $cat }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-500">Semua kategori</p>
                        @endif
                    </div>

                    {{-- Meta --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Informasi</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Dibuat</span>
                                <span class="text-gray-900">{{ $sponsorship->created_at->format('d M Y H:i') }}</span>
                            </div>
                            @if($sponsorship->approved_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Disetujui</span>
                                    <span class="text-gray-900">{{ $sponsorship->approved_at->format('d M Y H:i') }}</span>
                                </div>
                            @endif
                            @if($sponsorship->target_impressions)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Target Imp</span>
                                    <span class="text-gray-900">{{ number_format($sponsorship->target_impressions) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Notes --}}
                    @if($sponsorship->notes)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Catatan</h3>
                            <p class="text-sm text-gray-700">{{ $sponsorship->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
