<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                Error Logs
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.logs.download') }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download
                </a>
                @if(auth()->user()->role === 'superadmin')
                    <form action="{{ route('admin.logs.clear') }}" method="POST">
                        @csrf
                        <button type="button" onclick="confirmSubmit(this.closest('form'), 'Yakin ingin menghapus seluruh log? Tindakan ini tidak dapat dibatalkan.', { title: 'Hapus Semua Log', confirmText: 'Ya, Hapus' })" class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Clear Log
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Session Status --}}
        @if(session('status'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                {{ session('status') }}
            </div>
        @endif

        {{-- Stats Grid --}}
        <div class="grid grid-cols-3 md:grid-cols-5 lg:grid-cols-9 gap-3 mb-6">
            <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm">
                <div class="text-xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                <div class="text-xs text-gray-500">Total</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm">
                <div class="text-xl font-bold text-red-600">{{ $stats['error'] }}</div>
                <div class="text-xs text-gray-500">Error</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm">
                <div class="text-xl font-bold text-red-800">{{ $stats['critical'] }}</div>
                <div class="text-xs text-gray-500">Critical</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm">
                <div class="text-xl font-bold text-red-900">{{ $stats['emergency'] }}</div>
                <div class="text-xs text-gray-500">Emergency</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm">
                <div class="text-xl font-bold text-pink-600">{{ $stats['alert'] }}</div>
                <div class="text-xs text-gray-500">Alert</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm">
                <div class="text-xl font-bold text-yellow-600">{{ $stats['warning'] }}</div>
                <div class="text-xs text-gray-500">Warning</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm">
                <div class="text-xl font-bold text-blue-600">{{ $stats['notice'] }}</div>
                <div class="text-xs text-gray-500">Notice</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm">
                <div class="text-xl font-bold text-green-600">{{ $stats['info'] }}</div>
                <div class="text-xs text-gray-500">Info</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm">
                <div class="text-xl font-bold text-gray-400">{{ $stats['debug'] }}</div>
                <div class="text-xs text-gray-500">Debug</div>
            </div>
        </div>

        {{-- Filter & Search --}}
        <div class="flex flex-col md:flex-row gap-4 mb-6">
            {{-- Level Filter Tabs --}}
            <div class="flex gap-2 flex-wrap">
                @php
                    $levels = [
                        '' => ['label' => 'Semua', 'class' => 'bg-gray-100 text-gray-700'],
                        'emergency' => ['label' => 'Emergency', 'class' => 'bg-red-900 text-white'],
                        'alert' => ['label' => 'Alert', 'class' => 'bg-pink-100 text-pink-700'],
                        'critical' => ['label' => 'Critical', 'class' => 'bg-red-800 text-white'],
                        'error' => ['label' => 'Error', 'class' => 'bg-red-100 text-red-700'],
                        'warning' => ['label' => 'Warning', 'class' => 'bg-yellow-100 text-yellow-700'],
                        'notice' => ['label' => 'Notice', 'class' => 'bg-blue-100 text-blue-700'],
                        'info' => ['label' => 'Info', 'class' => 'bg-green-100 text-green-700'],
                        'debug' => ['label' => 'Debug', 'class' => 'bg-gray-100 text-gray-500'],
                    ];
                @endphp
                @foreach($levels as $key => $level)
                    <a href="{{ route('admin.logs.index', array_filter(['level' => $key ?: null, 'search' => $search ?: null])) }}"
                       class="px-3 py-1.5 text-xs font-medium rounded-full transition {{ $currentLevel === $key ? $level['class'] : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                        {{ $level['label'] }}
                    </a>
                @endforeach
            </div>

            {{-- Search --}}
            <form action="{{ route('admin.logs.index') }}" method="GET" class="flex-1 max-w-md">
                @if($currentLevel)
                    <input type="hidden" name="level" value="{{ $currentLevel }}">
                @endif
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari error message..."
                           class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" />
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
            </form>

            {{-- Copy All Visible --}}
            <button onclick="copyAllVisible()" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition shrink-0" title="Copy semua error yang terlihat ke clipboard">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                Copy All ({{ count($entries) }})
            </button>
        </div>

        {{-- Toast Notification --}}
        <div x-data="{ show: false, message: '' }" x-show="show" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             class="fixed bottom-6 right-6 z-50 bg-gray-900 text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-2"
             x-init="$watch('$store.clipboardToast', val => { if(val) { message = val; show = true; setTimeout(() => { show = false; $store.clipboardToast = ''; }, 2500); } })">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span x-text="message" class="text-sm font-medium"></span>
        </div>

        {{-- Log Entries --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if(count($entries) > 0)
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase w-16">#</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase w-44">Waktu</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase w-24">Level</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Pesan Error</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entries as $entry)
                            <tr class="border-b border-50 hover:bg-gray-50 transition" x-data="{ copied: false }">
                                <td class="px-5 py-3 text-xs text-gray-400 font-mono">{{ $entry['id'] }}</td>
                                <td class="px-5 py-3 text-sm text-gray-600 font-mono whitespace-nowrap">{{ $entry['timestamp'] }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-bold uppercase rounded-full
                                        {{ match($entry['level']) {
                                            'emergency' => 'bg-red-900 text-white',
                                            'alert' => 'bg-pink-100 text-pink-700',
                                            'critical' => 'bg-red-800 text-white',
                                            'error' => 'bg-red-100 text-red-700',
                                            'warning' => 'bg-yellow-100 text-yellow-700',
                                            'notice' => 'bg-blue-100 text-blue-700',
                                            'info' => 'bg-green-100 text-green-700',
                                            'debug' => 'bg-gray-100 text-gray-500',
                                            default => 'bg-gray-100 text-gray-500',
                                        } }}">
                                        {{ $entry['level'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <a href="{{ route('admin.logs.show', $entry['id']) }}" class="text-sm text-gray-900 hover:text-orange-600 transition line-clamp-2 font-mono">
                                        {{ Str::limit($entry['message'], 120) }}
                                    </a>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            onclick="copySingleEntry({{ Js::from($entry) }})"
                                            class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-orange-600 hover:bg-orange-50 rounded-lg transition"
                                            title="Copy error ini ke clipboard">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                            Copy
                                        </button>
                                        <a href="{{ route('admin.logs.show', $entry['id']) }}"
                                           class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-gray-500 hover:bg-gray-100 rounded-lg transition"
                                           title="Lihat detail">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-16">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-gray-500 text-lg font-medium">Tidak ada error log ditemukan</p>
                    <p class="text-gray-400 text-sm mt-1">
                        @if($currentLevel || $search)
                            <a href="{{ route('admin.logs.index') }}" class="text-orange-600 hover:underline">Hapus filter</a> untuk melihat semua log.
                        @else
                            Aplikasi berjalan tanpa error saat ini.
                        @endif
                    </p>
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="mt-4 text-xs text-gray-400 text-center">
            Menampilkan {{ count($entries) }} entry terbaru. File: <code class="bg-gray-100 px-1.5 py-0.5 rounded">storage/logs/laravel.log</code>
        </div>
    </div>

    @push('scripts')
    <script>
        // Store all entries for copyAllVisible
        const allEntries = @js($entries);

        function formatEntryForAI(entry) {
            let text = `=== APPLICATION ERROR LOG ===\n`;
            text += `Timestamp: ${entry.timestamp}\n`;
            text += `Level: ${entry.level.toUpperCase()}\n`;
            text += `Channel: ${entry.channel}\n`;
            text += `\n--- Error Message ---\n`;
            text += `${entry.message}\n`;

            if (entry.context) {
                text += `\n--- Context ---\n`;
                text += `${entry.context}\n`;
            }

            if (entry.stackTrace) {
                text += `\n--- Stack Trace ---\n`;
                text += `${entry.stackTrace}\n`;
            }

            text += `\n===============================\n`;

            return text;
        }

        function copyToClipboard(text, successMessage) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast(successMessage);
                }).catch(() => {
                    fallbackCopy(text, successMessage);
                });
            } else {
                fallbackCopy(text, successMessage);
            }
        }

        function fallbackCopy(text, successMessage) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.left = '-9999px';
            textarea.style.top = '-9999px';
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            try {
                document.execCommand('copy');
                showToast(successMessage);
            } catch (err) {
                showToast('Gagal copy. Silakan select dan copy manual.');
            }
            document.body.removeChild(textarea);
        }

        function copySingleEntry(entry) {
            const text = formatEntryForAI(entry);
            copyToClipboard(text, 'Error berhasil di-copy ke clipboard!');
        }

        function copyAllVisible() {
            if (allEntries.length === 0) {
                showToast('Tidak ada error untuk di-copy.');
                return;
            }

            let text = `========================================\n`;
            text += `  APPLICATION ERROR LOGS EXPORT\n`;
            text += `  Exported: ${new Date().toISOString()}\n`;
            text += `  Total Entries: ${allEntries.length}\n`;
            text += `========================================\n\n`;

            allEntries.forEach((entry, index) => {
                text += formatEntryForAI(entry);
                if (index < allEntries.length - 1) {
                    text += `\n`;
                }
            });

            copyToClipboard(text, `${allEntries.length} error berhasil di-copy!`);
        }

        function showToast(message) {
            // Dispatch custom event for Alpine toast
            window.dispatchEvent(new CustomEvent('clipboard-toast', { detail: { message } }));
        }

        // Alpine toast listener
        document.addEventListener('alpine:init', () => {
            Alpine.store('clipboardToast', '');
            window.addEventListener('clipboard-toast', (e) => {
                Alpine.store('clipboardToast', e.detail.message);
            });
        });
    </script>
    @endpush
</x-app-layout>
