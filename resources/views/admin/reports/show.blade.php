<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reports.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800">Detail Laporan #{{ $report->id }}</h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Report Info --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Informasi Laporan</h3>
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">Pelapor</dt>
                            <dd class="font-medium text-gray-900">{{ $report->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Waktu</dt>
                            <dd class="font-medium text-gray-900">{{ $report->created_at->format('d M Y, H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Alasan</dt>
                            <dd>
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ match($report->reason) { 'malware' => 'bg-red-100 text-red-700', 'spam_ads' => 'bg-orange-100 text-orange-700', 'inappropriate' => 'bg-yellow-100 text-yellow-700', default => 'bg-gray-100 text-gray-700' } }}">
                                    {{ match($report->reason) { 'malware' => 'Malware/Kode Berbahaya', 'spam_ads' => 'Spam/Iklan', 'inappropriate' => 'Konten Tidak Pantas', default => 'Lainnya' } }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Status</dt>
                            <dd>
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ match($report->status) { 'pending' => 'bg-orange-100 text-orange-700', 'reviewed' => 'bg-blue-100 text-blue-700', 'resolved' => 'bg-green-100 text-green-700', 'dismissed' => 'bg-gray-100 text-gray-500', default => 'bg-gray-100 text-gray-500' } }}">
                                    {{ match($report->status) { 'pending' => 'Menunggu', 'reviewed' => 'Direview', 'resolved' => 'Selesai', 'dismissed' => 'Ditolak', default => $report->status } }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                    @if($report->description)
                        <div class="mt-4 p-3 bg-gray-50 rounded-xl">
                            <div class="text-xs text-gray-500 mb-1">Deskripsi</div>
                            <p class="text-sm text-gray-700">{{ $report->description }}</p>
                        </div>
                    @endif
                </div>

                {{-- Simulation Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Simulasi yang Dilaporkan</h3>
                    <div class="flex items-start gap-4">
                        @if($report->simulation->thumbnail)
                            <img src="{{ Storage::disk('public')->url($report->simulation->thumbnail) }}" class="w-20 h-14 object-cover rounded-lg" alt="">
                        @else
                            <div class="w-20 h-14 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                            </div>
                        @endif
                        <div>
                            <a href="{{ route('simulations.show', $report->simulation->slug) }}" class="font-medium text-gray-900 hover:text-blue-600 transition">{{ $report->simulation->title }}</a>
                            <div class="text-xs text-gray-500 mt-1">oleh {{ $report->simulation->user->name }} · {{ $report->simulation->formatted_play_count }} dimainkan</div>
                            <div class="flex gap-2 mt-2">
                                <a href="{{ route('admin.simulations.show', $report->simulation) }}" class="text-xs text-blue-600 hover:underline">Lihat Detail</a>
                                <span class="text-gray-300">·</span>
                                <a href="{{ route('simulations.show', $report->simulation->slug) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Buka Simulasi</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Previous Reports --}}
                @if($previousReports->isNotEmpty())
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Laporan Sebelumnya ({{ $previousReports->count() }})</h3>
                        <div class="space-y-3">
                            @foreach($previousReports as $prev)
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                    <div class="text-sm">
                                        <span class="font-medium text-gray-700">{{ $prev->user->name }}</span>
                                        <span class="text-gray-400">· {{ match($prev->reason) { 'malware' => 'Malware', 'spam_ads' => 'Spam', 'inappropriate' => 'Tidak Pantas', default => 'Lainnya' } }}</span>
                                        <span class="text-gray-400">· {{ $prev->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Action Panel --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Tindakan</h3>
                    @if($report->status === 'pending')
                        <form method="POST" action="{{ route('admin.reports.review', $report) }}">
                            @csrf
                            @method('PATCH')
                            <div class="space-y-3 mb-4">
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" name="status" value="reviewed" class="text-blue-500">
                                    <span>Tandai Direview</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" name="status" value="resolved" class="text-green-500">
                                    <span>Selesaikan (Valid)</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" name="status" value="dismissed" class="text-gray-500">
                                    <span>Tolak Laporan</span>
                                </label>
                            </div>
                            <textarea name="action_taken" rows="3" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 mb-3" placeholder="Catatan tindakan (opsional)..."></textarea>
                            <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition">Simpan</button>
                        </form>
                    @else
                        <div class="text-sm text-gray-500">
                            <p>Laporan ini sudah ditindaklanjuti.</p>
                            @if($report->action_taken)
                                <div class="mt-2 p-3 bg-gray-50 rounded-xl">
                                    <div class="text-xs text-gray-400 mb-1">Tindakan</div>
                                    <p class="text-gray-700">{{ $report->action_taken }}</p>
                                </div>
                            @endif
                            @if($report->reviewer)
                                <div class="mt-2 text-xs text-gray-400">
                                    Direview oleh {{ $report->reviewer->name }} · {{ $report->reviewed_at?->diffForHumans() }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Quick Actions --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Aksi Cepat</h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.simulations.show', $report->simulation) }}" class="block w-full text-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
                            Lihat Simulasi
                        </a>
                        <a href="{{ route('admin.simulations.edit', $report->simulation) }}" class="block w-full text-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
                            Edit Simulasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
