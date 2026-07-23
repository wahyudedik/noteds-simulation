<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Kelola Creator
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filters --}}
            <div class="bg-white border border-gray-100 rounded-xl p-4 mb-6 shadow-sm">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari creator..."
                           class="border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 w-64" />
                    <select name="tier" class="border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Tier</option>
                        <option value="basic" {{ request('tier') === 'basic' ? 'selected' : '' }}>Basic</option>
                        <option value="verified" {{ request('tier') === 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="expert" {{ request('tier') === 'expert' ? 'selected' : '' }}>Expert</option>
                        <option value="platinum" {{ request('tier') === 'platinum' ? 'selected' : '' }}>Platinum</option>
                    </select>
                    <label class="flex items-center gap-1.5 text-sm text-gray-600">
                        <input type="checkbox" name="suspended" value="1" {{ request('suspended') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-red-600 focus:ring-red-500" />
                        Ditangguhkan
                    </label>
                    <button type="submit" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">Filter</button>
                </form>
            </div>

            {{-- Creators Table --}}
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                @if($creators->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="text-left py-3 px-4 text-gray-500 font-medium">Creator</th>
                                    <th class="text-center py-3 px-4 text-gray-500 font-medium">Simulasi</th>
                                    <th class="text-center py-3 px-4 text-gray-500 font-medium">Reputasi</th>
                                    <th class="text-center py-3 px-4 text-gray-500 font-medium">Tier</th>
                                    <th class="text-center py-3 px-4 text-gray-500 font-medium">Status</th>
                                    <th class="text-right py-3 px-4 text-gray-500 font-medium">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($creators as $creator)
                                    @php
                                        $rep = $creator->reputation;
                                    @endphp
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-3">
                                                @if($creator->avatar)
                                                    <img src="{{ Storage::url($creator->avatar) }}" alt="" class="w-8 h-8 rounded-full object-cover" />
                                                @else
                                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-semibold">{{ strtoupper(substr($creator->name, 0, 1)) }}</div>
                                                @endif
                                                <div>
                                                    <a href="{{ route('admin.creators.show', $creator) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $creator->name }}</a>
                                                    <p class="text-xs text-gray-400">{{ $creator->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-center text-gray-500">{{ $creator->simulations_count }}</td>
                                        <td class="py-3 px-4 text-center">
                                            @if($rep)
                                                <div class="flex items-center justify-center gap-1.5">
                                                    <div class="w-16 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                                        <div class="h-full rounded-full {{ $rep->score >= 80 ? 'bg-green-500' : ($rep->score >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $rep->score }}%"></div>
                                                    </div>
                                                    <span class="text-xs font-medium">{{ $rep->score }}</span>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if($rep)
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full
                                                    {{ match($rep->revenue_tier) { 'platinum' => 'bg-purple-100 text-purple-700', 'expert' => 'bg-blue-100 text-blue-700', 'verified' => 'bg-green-100 text-green-700', default => 'bg-gray-100 text-gray-600' } }}">
                                                    {{ ucfirst($rep->revenue_tier) }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if($rep && $rep->score < 20)
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700 rounded-full">Suspended</span>
                                            @else
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Aktif</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <a href="{{ route('admin.creators.show', $creator) }}" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-3">{{ $creators->links() }}</div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <p>Belum ada creator.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
