<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                User Analytics
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.analytics.index') }}" class="px-3 py-1.5 text-sm font-medium rounded-lg transition bg-gray-100 text-gray-600 hover:bg-gray-200">
                    ← Overview
                </a>
                @foreach(['7' => '7 Hari', '30' => '30 Hari', '90' => '90 Hari'] as $p => $label)
                    <a href="{{ route('admin.analytics.users', ['period' => $p]) }}"
                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ $period === $p ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Summary Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalUsers) }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Aktif ({{ $period }} hari)</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($activeUsers) }}</p>
                    @if($totalUsers > 0)
                        <p class="text-xs text-gray-400 mt-1">{{ round(($activeUsers / $totalUsers) * 100, 1) }}% dari total</p>
                    @endif
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Plays</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalPlays) }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Komentar</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalComments) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Registration Trends Chart --}}
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tren Registrasi</h3>
                    <div class="h-64 flex items-end gap-1">
                        @forelse($registrations as $reg)
                            <div class="flex-1 flex flex-col items-center">
                                <div class="w-full bg-blue-500 rounded-t transition-all hover:bg-blue-600"
                                     style="height: {{ max(4, ($reg->count / max($registrations->max('count'), 1)) * 220) }}px"
                                     title="{{ $reg->date }}: {{ $reg->count }} registrasi"></div>
                                @if($loop->index % 7 === 0)
                                    <span class="text-[10px] text-gray-400 mt-1">{{ \Carbon\Carbon::parse($reg->date)->format('d') }}</span>
                                @endif
                            </div>
                        @empty
                            <div class="flex-1 flex items-center justify-center text-gray-400 text-sm">Belum ada data registrasi</div>
                        @endforelse
                    </div>
                </div>

                {{-- Role Breakdown --}}
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Role Breakdown</h3>
                    @if($roleBreakdown->count() > 0)
                        <div class="space-y-4">
                            @php
                                $roleColors = ['superadmin' => 'bg-red-500', 'admin' => 'bg-orange-500', 'creator' => 'bg-blue-500', 'user' => 'bg-gray-500'];
                                $roleLabels = ['superadmin' => 'Superadmin', 'admin' => 'Admin', 'creator' => 'Creator', 'user' => 'User'];
                                $maxRoleCount = $roleBreakdown->max('count') ?: 1;
                            @endphp
                            @foreach($roleBreakdown as $role)
                                @php $percentage = $totalUsers > 0 ? round(($role->count / $totalUsers) * 100, 1) : 0; @endphp
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700">{{ $roleLabels[$role->role] ?? ucfirst($role->role) }}</span>
                                        <span class="text-sm text-gray-500">{{ number_format($role->count) }} ({{ $percentage }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                        <div class="{{ $roleColors[$role->role] ?? 'bg-gray-500' }} h-full rounded-full transition-all" style="width: {{ ($role->count / $maxRoleCount) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Belum ada data role.</p>
                    @endif
                </div>
            </div>

            {{-- Top Creators --}}
            <div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Top Creators</h3>
                    <p class="text-xs text-gray-500 mt-1">Berdasarkan jumlah simulasi yang diunggah</p>
                </div>
                @if($topCreators->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="text-left py-3 px-4 text-gray-500 font-medium w-10">#</th>
                                    <th class="text-left py-3 px-4 text-gray-500 font-medium">Nama</th>
                                    <th class="text-left py-3 px-4 text-gray-500 font-medium">Email</th>
                                    <th class="text-center py-3 px-4 text-gray-500 font-medium">Simulasi</th>
                                    <th class="text-left py-3 px-4 text-gray-500 font-medium">Bergabung</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($topCreators as $index => $creator)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="py-3 px-4 text-gray-500">{{ $index + 1 }}</td>
                                        <td class="py-3 px-4">
                                            <a href="{{ route('admin.users.show', $creator->id) }}" class="font-medium text-gray-900 hover:text-blue-600 transition">
                                                {{ $creator->name }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-4 text-gray-500">{{ $creator->email }}</td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                                                {{ $creator->simulations_count }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-gray-400 text-xs">{{ $creator->created_at->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500 text-sm">Belum ada creator.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
