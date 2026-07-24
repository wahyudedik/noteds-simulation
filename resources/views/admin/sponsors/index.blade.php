<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                Kelola Sponsor
            </h2>
            <a href="{{ route('admin.sponsors.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                + Tambah Sponsor
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

            {{-- Filters --}}
            <div class="mb-6 bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                <form action="{{ route('admin.sponsors.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Nama Perusahaan</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama perusahaan..."
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        Filter
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.sponsors.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Sponsors Table --}}
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($sponsors->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Sponsor</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Kontak</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Industri</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Sponsorship</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Status</th>
                                        <th class="text-right py-3 px-2 text-gray-500 font-medium">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sponsors as $sponsor)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-3 px-2">
                                            <div class="flex items-center gap-3">
                                                @if($sponsor->logo_url)
                                                    <img src="{{ $sponsor->logo_url }}" alt="{{ $sponsor->company_name }}" class="w-8 h-8 rounded-lg object-cover">
                                                @else
                                                    <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600 font-semibold text-xs">
                                                        {{ strtoupper(substr($sponsor->company_name, 0, 2)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $sponsor->company_name }}</a>
                                                    @if($sponsor->website_url)
                                                        <div class="text-xs text-gray-500">{{ Str::limit($sponsor->website_url, 30) }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-2">
                                            <div class="text-gray-900">{{ $sponsor->contact_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $sponsor->contact_email }}</div>
                                        </td>
                                        <td class="py-3 px-2 text-gray-500">{{ $sponsor->industry ?? '-' }}</td>
                                        <td class="py-3 px-2 text-center text-gray-500">{{ $sponsor->sponsorships_count }}</td>
                                        <td class="py-3 px-2 text-center">
                                            @if($sponsor->is_active)
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Aktif</span>
                                            @else
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-500 rounded-full">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-2 text-right space-x-2">
                                            <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Detail</a>
                                            <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="text-amber-600 hover:text-amber-700 text-xs font-medium">Edit</a>
                                            <a href="{{ route('admin.sponsors.report', $sponsor) }}" class="text-purple-600 hover:text-purple-700 text-xs font-medium">Laporan</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $sponsors->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            <p class="text-sm">Belum ada sponsor.</p>
                            <a href="{{ route('admin.sponsors.create') }}" class="mt-3 inline-block text-blue-600 hover:text-blue-700 text-sm font-medium">+ Tambah Sponsor Pertama</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
