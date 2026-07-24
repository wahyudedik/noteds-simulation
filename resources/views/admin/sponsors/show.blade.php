<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.sponsors.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Sponsor</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Sponsor Info --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="text-center mb-4">
                            @if($sponsor->logo_url)
                                <img src="{{ $sponsor->logo_url }}" alt="{{ $sponsor->company_name }}" class="w-20 h-20 rounded-xl object-cover mx-auto mb-3">
                            @else
                                <div class="w-20 h-20 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-2xl mx-auto mb-3">
                                    {{ strtoupper(substr($sponsor->company_name, 0, 2)) }}
                                </div>
                            @endif
                            <h3 class="text-lg font-semibold text-gray-900">{{ $sponsor->company_name }}</h3>
                            @if($sponsor->industry)
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-700 rounded-full mt-1">{{ $sponsor->industry }}</span>
                            @endif
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Status</span>
                                @if($sponsor->is_active)
                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Aktif</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-500 rounded-full">Nonaktif</span>
                                @endif
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Kontak</span>
                                <span class="text-gray-900 font-medium">{{ $sponsor->contact_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Email</span>
                                <a href="mailto:{{ $sponsor->contact_email }}" class="text-blue-600 hover:underline">{{ $sponsor->contact_email }}</a>
                            </div>
                            @if($sponsor->contact_phone)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Telepon</span>
                                    <span class="text-gray-900">{{ $sponsor->contact_phone }}</span>
                                </div>
                            @endif
                            @if($sponsor->website_url)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Website</span>
                                    <a href="{{ $sponsor->website_url }}" target="_blank" class="text-blue-600 hover:underline truncate max-w-[150px]">{{ $sponsor->website_url }}</a>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-500">Bergabung</span>
                                <span class="text-gray-900">{{ $sponsor->created_at->format('d M Y') }}</span>
                            </div>
                        </div>

                        @if($sponsor->notes)
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <p class="text-xs text-gray-500 font-medium mb-1">Catatan</p>
                                <p class="text-sm text-gray-700">{{ $sponsor->notes }}</p>
                            </div>
                        @endif

                        <div class="mt-4 pt-4 border-t border-gray-100 flex gap-2">
                            <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="flex-1 text-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Edit</a>
                            <a href="{{ route('admin.sponsors.report', $sponsor) }}" class="flex-1 text-center px-3 py-2 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-lg transition">Laporan</a>
                        </div>
                    </div>
                </div>

                {{-- Sponsorships List --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-900">Sponsorship ({{ $sponsor->sponsorships->count() }})</h3>
                            <a href="{{ route('admin.sponsorships.create') }}?sponsor_id={{ $sponsor->id }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">+ Buat Sponsorship</a>
                        </div>

                        @if($sponsor->sponsorships->count() > 0)
                            <div class="space-y-3">
                                @foreach($sponsor->sponsorships as $sponsorship)
                                    <a href="{{ route('admin.sponsorships.show', $sponsorship) }}" class="block p-4 rounded-lg border border-gray-100 hover:border-gray-200 hover:bg-gray-50 transition">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-medium text-gray-900 text-sm">{{ $sponsorship->title }}</span>
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium {{ $sponsorship->status_color }} rounded-full">{{ $sponsorship->status_label }}</span>
                                        </div>
                                        <div class="flex items-center gap-4 text-xs text-gray-500">
                                            <span>{{ $sponsorship->package_label }}</span>
                                            <span>Rp {{ number_format($sponsorship->budget, 0, ',', '.') }}</span>
                                            <span>{{ $sponsorship->start_date->format('d M Y') }} — {{ $sponsorship->end_date->format('d M Y') }}</span>
                                        </div>
                                        {{-- Budget Progress --}}
                                        <div class="mt-2">
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-blue-600 h-1.5 rounded-full transition-all" style="width: {{ $sponsorship->progress }}%"></div>
                                            </div>
                                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                                <span>Terkonsumsi: Rp {{ number_format($sponsorship->spent, 0, ',', '.') }}</span>
                                                <span>{{ $sponsorship->progress }}%</span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500 text-sm">
                                <p>Belum ada sponsorship untuk sponsor ini.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Quick Stats --}}
                    <div class="grid grid-cols-3 gap-4 mt-6">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_sponsorships'] ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">Total Sponsorship</p>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                            <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_spent'] ?? 0, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500 mt-1">Total Belanja</p>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_impressions'] ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Total Impressions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
