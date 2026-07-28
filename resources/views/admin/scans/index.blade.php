<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Security Scan Logs</h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 dark:text-gray-300">? Kembali</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6">
        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $counts['total'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Scan</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $counts['pass'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Lolos</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $counts['flag'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Di-Flag</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $counts['reject'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Ditolak</div>
            </div>
        </div>

        {{-- Filter Tabs --}}
        <div class="flex gap-4 mb-6 flex-wrap">
            <div class="flex gap-2">
                @foreach(['auto_scan' => 'Auto Scan', 'sandbox_test' => 'Sandbox', 'manual_review' => 'Manual'] as $key => $label)
                    <a href="{{ route('admin.scans.index', array_filter(['scan_type' => request('scan_type') === $key ? null : $key, 'result' => request('result')])) }}"
                       class="px-3 py-1.5 text-xs font-medium rounded-full transition {{ request('scan_type') === $key ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' : 'bg-gray-100 text-gray-500 dark:text-gray-400 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            <div class="flex gap-2">
                @foreach(['pass' => 'Pass', 'flag' => 'Flag', 'reject' => 'Reject'] as $key => $label)
                    <a href="{{ route('admin.scans.index', array_filter(['result' => request('result') === $key ? null : $key, 'scan_type' => request('scan_type')])) }}"
                       class="px-3 py-1.5 text-xs font-medium rounded-full transition {{ request('result') === $key ? match($key) { 'pass' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'flag' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400', 'reject' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', default => 'bg-gray-100 text-gray-500' } : 'bg-gray-100 text-gray-500 dark:text-gray-400 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Scan Logs --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Experience</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tipe</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Hasil</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Durasi</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Waktu</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 dark:bg-gray-700/50 dark:hover:bg-gray-600/50 transition">
                            <td class="px-5 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($log->simulation->title ?? 'N/A', 40) }}</div>
                                <div class="text-xs text-gray-400 dark:text-gray-500">v{{ $log->version }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ match($log->scan_type) { 'auto_scan' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'sandbox_test' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400', 'manual_review' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-700', default => 'bg-gray-100 text-gray-500' } }}">
                                    {{ match($log->scan_type) { 'auto_scan' => 'Auto Scan', 'sandbox_test' => 'Sandbox', 'manual_review' => 'Manual', default => $log->scan_type } }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ match($log->result) { 'pass' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'flag' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400', 'reject' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', default => 'bg-gray-100 text-gray-500' } }}">
                                    {{ strtoupper($log->result) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $log->scan_duration_ms }}ms</td>
                            <td class="px-5 py-4 text-xs text-gray-400 dark:text-gray-500">{{ $log->created_at->diffForHumans() }}</td>
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.scans.show', $log) }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-500 dark:text-gray-400">Tidak ada scan log ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $logs->links() }}</div>
    </div>
</x-app-layout>
