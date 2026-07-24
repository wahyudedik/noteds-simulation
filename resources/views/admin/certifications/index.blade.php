<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                Creator Certification
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="text-sm text-gray-500 mb-1">Total Sertifikasi</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $totalCerts }}</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="text-sm text-gray-500 mb-1">Aktif</div>
                    <div class="text-2xl font-bold text-emerald-600">{{ $activeCerts }}</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="text-sm text-gray-500 mb-1">Kreator Eligible</div>
                    <div class="text-2xl font-bold text-amber-600">{{ count($eligibleCreators) }}</div>
                </div>
            </div>

            {{-- Eligible Creators --}}
            @if (count($eligibleCreators) > 0)
                <div class="bg-amber-50 rounded-xl border border-amber-200 p-5">
                    <h3 class="text-sm font-semibold text-amber-800 mb-3">
                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        Kreator yang Eligible untuk Sertifikasi
                    </h3>
                    <div class="space-y-2">
                        @foreach ($eligibleCreators as $item)
                            <div class="flex items-center justify-between bg-white rounded-lg px-4 py-3 border border-amber-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center text-sm font-bold text-amber-700">{{ substr($item['user']->name, 0, 1) }}</div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $item['user']->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $item['user']->email }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">
                                        Eligible: {{ $item['level'] }}
                                    </span>
                                    <form method="POST" action="{{ route('admin.certifications.award') }}">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $item['user']->id }}" />
                                        <input type="hidden" name="level" value="{{ $item['level'] }}" />
                                        <button type="submit" onclick="return confirm('Berikan sertifikasi {{ $item['level'] }} ke {{ $item['user']->name }}?')"
                                            class="px-3 py-1 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">Berikan</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Filters --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <form method="GET" class="flex flex-wrap gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kreator..."
                        class="flex-1 min-w-[200px] rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                    <select name="level" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Semua Level</option>
                        <option value="verified" {{ request('level') === 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="expert" {{ request('level') === 'expert' ? 'selected' : '' }}>Expert</option>
                        <option value="platinum" {{ request('level') === 'platinum' ? 'selected' : '' }}>Platinum</option>
                    </select>
                    <select name="status" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="revoked" {{ request('status') === 'revoked' ? 'selected' : '' }}>Revoked</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">Filter</button>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kreator</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Awarded</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expires</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reviewer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($certifications as $cert)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $cert->user->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $cert->user->email ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $cert->level_badge_class }}">
                                            {{ $cert->level_icon }} {{ $cert->level_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $cert->status_badge_class }}">{{ $cert->status }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $cert->awarded_at?->format('d M Y') ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $cert->expires_at?->format('d M Y') ?? 'Seumur Hidup' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $cert->reviewer->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        @if ($cert->isActive())
                                            <form method="POST" action="{{ route('admin.certifications.revoke', $cert) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" onclick="return confirm('Cabut sertifikasi ini?')"
                                                    class="text-red-600 hover:text-red-800">Cabut</button>
                                            </form>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">Belum ada sertifikasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $certifications->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
