<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="w-5 h-5 inline-block mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Pengajuan Kreator
            </h2>
            <a href="{{ route('admin.creators.index') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- Stats Tabs --}}
            <div class="flex gap-2 mb-6">
                <a href="{{ route('admin.creators.applications', ['status' => 'pending']) }}"
                   class="px-4 py-2 text-sm font-medium rounded-lg transition {{ request('status', 'pending') === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                    Menunggu ({{ $counts['pending'] }})
                </a>
                <a href="{{ route('admin.creators.applications', ['status' => 'approved']) }}"
                   class="px-4 py-2 text-sm font-medium rounded-lg transition {{ request('status') === 'approved' ? 'bg-green-100 text-green-700' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                    Disetujui ({{ $counts['approved'] }})
                </a>
                <a href="{{ route('admin.creators.applications', ['status' => 'rejected']) }}"
                   class="px-4 py-2 text-sm font-medium rounded-lg transition {{ request('status') === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                    Ditolak ({{ $counts['rejected'] }})
                </a>
            </div>

            {{-- Applications List --}}
            @if($applications->count() > 0)
                <div class="space-y-4">
                    @foreach($applications as $application)
                        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-4">
                                    {{-- Avatar --}}
                                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg flex-shrink-0">
                                        {{ strtoupper(substr($application->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-base font-semibold text-gray-900">{{ $application->user->name }}</h3>
                                            @switch($application->status)
                                                @case('pending')
                                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Menunggu</span>
                                                    @break
                                                @case('approved')
                                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Disetujui</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700 rounded-full">Ditolak</span>
                                                    @break
                                            @endswitch
                                        </div>
                                        <p class="text-sm text-gray-500">{{ $application->user->email }}</p>
                                        <p class="text-sm text-gray-700 mt-2">{{ $application->reason }}</p>
                                        <p class="text-xs text-gray-400 mt-2">Diajukan {{ $application->created_at->diffForHumans() }}</p>
                                        @if($application->reviewed_at)
                                            <p class="text-xs text-gray-400 mt-1">Direview {{ $application->reviewed_at->diffForHumans() }} oleh {{ $application->reviewer?->name }}</p>
                                        @endif
                                        @if($application->review_notes)
                                            <p class="text-xs text-gray-500 italic mt-1">"{{ $application->review_notes }}"</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Actions --}}
                                @if($application->isPending())
                                    <div class="flex gap-2 shrink-0">
                                        <form action="{{ route('admin.creators.applications.approve', $application) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                                                Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.creators.applications.reject', $application) }}" method="POST" class="inline" x-data="{ showReject: false }">
                                            @csrf
                                            <div x-show="!showReject" x-cloak>
                                                <button type="button" @click="showReject = true" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                                                    Tolak
                                                </button>
                                            </div>
                                            <div x-show="showReject" x-cloak class="flex gap-2">
                                                <input type="text" name="review_notes" placeholder="Alasan penolakan" class="text-sm rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 w-48">
                                                <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                                                    Kirim
                                                </button>
                                                <button type="button" @click="showReject = false" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition">
                                                    Batal
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $applications->withQueryString()->links() }}
                </div>
            @else
                <div class="bg-white border border-gray-100 rounded-xl p-12 shadow-sm text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pengajuan</h3>
                    <p class="text-sm text-gray-500">Belum ada pengajuan creator dengan status ini.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
