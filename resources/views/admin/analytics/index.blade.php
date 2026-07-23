<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Platform Analytics
            </h2>
            <div class="flex items-center gap-2">
                @foreach(['7' => '7 Hari', '30' => '30 Hari', '90' => '90 Hari'] as $p => $label)
                    <a href="{{ route('admin.analytics.index', ['period' => $p]) }}"
                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ $period === $p ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Current Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($currentStats['total_users']) }}</p>
                    @if(isset($growth['users']))
                        <p class="text-xs text-green-600 mt-1">+{{ number_format($growth['users']) }} periode lalu</p>
                    @endif
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Simulasi</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($currentStats['total_simulations']) }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $currentStats['published_simulations'] }} published</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Views</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($currentStats['total_views']) }}</p>
                    @if(isset($growth['views']))
                        <p class="text-xs text-green-600 mt-1">+{{ number_format($growth['views']) }} periode lalu</p>
                    @endif
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Plays</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($currentStats['total_plays']) }}</p>
                    @if(isset($growth['plays']))
                        <p class="text-xs text-green-600 mt-1">+{{ number_format($growth['plays']) }} periode lalu</p>
                    @endif
                </div>
            </div>

            {{-- Revenue & Comments --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Revenue Iklan</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($currentStats['total_ad_revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Komentar</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($currentStats['total_comments']) }}</p>
                </div>
            </div>

            {{-- Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Registrations Chart --}}
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Registrasi Baru</h3>
                    <div class="h-64 flex items-end gap-1">
                        @forelse($analytics->take(30) as $a)
                            <div class="flex-1 flex flex-col items-center">
                                <div class="w-full bg-blue-500 rounded-t transition-all hover:bg-blue-600"
                                     style="height: {{ $a->new_registrations > 0 ? max(4, ($a->new_registrations / max($analytics->max('new_registrations'), 1)) * 220) : 0 }}px"
                                     title="{{ $a->date->format('d M') }}: {{ $a->new_registrations }} registrasi"></div>
                                @if($loop->index % 7 === 0)
                                    <span class="text-[10px] text-gray-400 mt-1">{{ $a->date->format('d') }}</span>
                                @endif
                            </div>
                        @empty
                            <div class="flex-1 flex items-center justify-center text-gray-400 text-sm">Belum ada data</div>
                        @endforelse
                    </div>
                </div>

                {{-- Views Chart --}}
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Total Views (Harian)</h3>
                    <div class="h-64 flex items-end gap-1">
                        @forelse($analytics->take(30) as $a)
                            <div class="flex-1 flex flex-col items-center">
                                <div class="w-full bg-emerald-500 rounded-t transition-all hover:bg-emerald-600"
                                     style="height: {{ $a->total_views > 0 ? max(4, ($a->total_views / max($analytics->max('total_views'), 1)) * 220) : 0 }}px"
                                     title="{{ $a->date->format('d M') }}: {{ number_format($a->total_views) }} views"></div>
                                @if($loop->index % 7 === 0)
                                    <span class="text-[10px] text-gray-400 mt-1">{{ $a->date->format('d') }}</span>
                                @endif
                            </div>
                        @empty
                            <div class="flex-1 flex items-center justify-center text-gray-400 text-sm">Belum ada data</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Top Categories --}}
            <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kategori Terpopuler</h3>
                @if($topCategories->count() > 0)
                    <div class="space-y-3">
                        @foreach($topCategories as $cat)
                            @php
                                $maxCount = $topCategories->max('count') ?: 1;
                                $percentage = ($cat->count / $maxCount) * 100;
                            @endphp
                            <div class="flex items-center gap-4">
                                <div class="w-24 text-sm text-gray-700 font-medium truncate">{{ $cat->category }}</div>
                                <div class="flex-1 bg-gray-100 rounded-full h-5 overflow-hidden">
                                    <div class="bg-blue-500 h-full rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                                </div>
                                <div class="w-20 text-right text-sm text-gray-500">{{ number_format($cat->count) }} simulasi</div>
                                <div class="w-24 text-right text-xs text-gray-400">{{ number_format($cat->total_views) }} views</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Belum ada data kategori.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
