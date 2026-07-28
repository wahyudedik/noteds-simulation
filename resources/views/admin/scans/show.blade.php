<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.scans.index') }}" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:text-gray-400 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800">Scan Log #{{ $log->id }}</h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Scan Details --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Detail Scan</h3>
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Experience</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ $log->simulation->title ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Versi</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">v{{ $log->version }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Tipe Scan</dt>
                            <dd>
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ match($log->scan_type) { 'auto_scan' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'sandbox_test' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400', 'manual_review' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-700', default => 'bg-gray-100 text-gray-500' } }}">
                                    {{ match($log->scan_type) { 'auto_scan' => 'Auto Scan (Static Analysis)', 'sandbox_test' => 'Sandbox Test (Dynamic Analysis)', 'manual_review' => 'Manual Review', default => $log->scan_type } }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Hasil</dt>
                            <dd>
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ match($log->result) { 'pass' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'flag' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400', 'reject' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', default => 'bg-gray-100 text-gray-500' } }}">
                                    {{ strtoupper($log->result) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Durasi</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ $log->scan_duration_ms }}ms</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Waktu</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ $log->created_at->format('d M Y, H:i:s') }}</dd>
                        </div>
                        @if($log->reviewer)
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Reviewer</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ $log->reviewer->name }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                {{-- Findings --}}
                @if($log->findings && count($log->findings) > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Temuan ({{ count($log->findings) }})</h3>
                        <div class="space-y-3">
                            @foreach($log->findings as $finding)
                                <div class="p-3 rounded-xl {{ match($finding['severity'] ?? 'info') { 'critical' => 'bg-red-50 border border-red-200', 'high' => 'bg-orange-50 border border-orange-200', 'medium' => 'bg-yellow-50 border border-yellow-200', 'flag' => 'bg-yellow-50 border border-yellow-200', 'low' => 'bg-blue-50 border border-blue-200', default => 'bg-gray-50 dark:bg-gray-700/50 border border-gray-200' } }}">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="inline-flex px-1.5 py-0.5 text-[10px] font-bold uppercase rounded {{ match($finding['severity'] ?? 'info') { 'critical' => 'bg-red-200 text-red-800', 'high' => 'bg-orange-200 text-orange-800', 'medium' => 'bg-yellow-200 text-yellow-800', 'flag' => 'bg-yellow-200 text-yellow-800', 'low' => 'bg-blue-200 text-blue-800', default => 'bg-gray-200 text-gray-800' } }}">
                                            {{ $finding['severity'] ?? 'info' }}
                                        </span>
                                        @if(isset($finding['file']))
                                            <span class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $finding['file'] }}</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 dark:text-gray-300">{{ $finding['description'] ?? '-' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-green-50 rounded-2xl border border-green-200 p-6">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <div class="font-medium text-green-800">Tidak ada temuan</div>
                                <div class="text-sm text-green-600 dark:text-green-400">Experience ini lolos dari deteksi pola berbahaya.</div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Manual Review Form (for admins) --}}
                @if($log->simulation)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Review Manual</h3>
                        <form method="POST" action="{{ route('admin.scans.manual-review', $log->simulation) }}">
                            @csrf
                            <div class="space-y-3 mb-4">
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" name="result" value="pass" class="text-green-500">
                                    <span class="text-green-700">(ok) Pass — Experience aman</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" name="result" value="flag" class="text-yellow-500">
                                    <span class="text-yellow-700">(!) Flag — Perlu perhatian</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" name="result" value="reject" class="text-red-500">
                                    <span class="text-red-700">(x) Reject — Tidak aman</span>
                                </label>
                            </div>
                            <textarea name="notes" rows="3" class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 mb-3" placeholder="Catatan review..."></textarea>
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition">Simpan Review</button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Riwayat Scan</h3>
                    <div class="space-y-2">
                        @foreach($allScans as $scan)
                            <a href="{{ route('admin.scans.show', $scan) }}" class="block p-3 rounded-xl {{ $scan->id === $log->id ? 'bg-blue-50 border border-blue-200' : 'bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100' }} transition">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium {{ match($scan->result) { 'pass' => 'text-green-600 dark:text-green-400', 'flag' => 'text-yellow-600 dark:text-yellow-400', 'reject' => 'text-red-600 dark:text-red-400', default => 'text-gray-600' } }}">
                                        {{ strtoupper($scan->result) }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ $scan->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ match($scan->scan_type) { 'auto_scan' => 'Auto Scan', 'sandbox_test' => 'Sandbox', 'manual_review' => 'Manual', default => $scan->scan_type } }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>

                @if($log->simulation)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Experience</h3>
                    <a href="{{ route('simulations.show', $log->simulation->slug) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">{{ $log->simulation->title }}</a>
                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">oleh {{ $log->simulation->user->name ?? 'N/A' }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
