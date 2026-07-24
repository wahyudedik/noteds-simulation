<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.logs.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800">Error Log #{{ $entryId }}</h2>
            </div>
            <div class="flex items-center gap-3">
                <button
                    onclick="copyEntry()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                    Copy untuk AI Debug
                </button>
                <a href="{{ route('admin.logs.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali ke Logs</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6">

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

        {{-- Entry Info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="font-semibold text-gray-900 mb-4">Detail Error</h3>
            <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500">Timestamp</dt>
                    <dd class="font-mono font-medium text-gray-900">{{ $entry['timestamp'] }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Level</dt>
                    <dd>
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
                            {{ strtoupper($entry['level']) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Channel</dt>
                    <dd class="font-medium text-gray-900">{{ $entry['channel'] }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Log File</dt>
                    <dd class="font-mono text-xs text-gray-600">laravel.log</dd>
                </div>
            </dl>
        </div>

        {{-- Error Message --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Pesan Error</h3>
                <button onclick="copySection('message')" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-orange-600 hover:bg-orange-50 rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                    Copy
                </button>
            </div>
            <div id="message" class="bg-red-50 border border-red-200 rounded-xl p-4 font-mono text-sm text-red-800 whitespace-pre-wrap">{{ $entry['message'] }}</div>
        </div>

        {{-- Context --}}
        @if($entry['context'])
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">Context</h3>
                    <button onclick="copySection('context')" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-orange-600 hover:bg-orange-50 rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        Copy
                    </button>
                </div>
                <pre id="context" class="bg-gray-50 border border-gray-200 rounded-xl p-4 font-mono text-sm text-gray-800 whitespace-pre-wrap overflow-x-auto">{{ $entry['context'] }}</pre>
            </div>
        @endif

        {{-- Stack Trace --}}
        @if($entry['stackTrace'])
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">Stack Trace</h3>
                    <button onclick="copySection('stackTrace')" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-orange-600 hover:bg-orange-50 rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        Copy
                    </button>
                </div>
                <div class="bg-gray-900 rounded-xl p-4 overflow-x-auto">
                    <pre id="stackTrace" class="font-mono text-sm text-green-400 whitespace-pre-wrap">{{ $entry['stackTrace'] }}</pre>
                </div>
            </div>
        @endif

        {{-- Raw Entry --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Raw Entry (untuk AI)</h3>
                <button onclick="copyEntry()" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-orange-600 hover:bg-orange-50 rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                    Copy Semua
                </button>
            </div>
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
                <p class="text-sm text-orange-700 mb-2">Copy raw entry ini dan paste ke AI (ChatGPT, Claude, dll) untuk debugging:</p>
                <pre class="font-mono text-xs text-gray-800 whitespace-pre-wrap max-h-64 overflow-y-auto">{{ $rawFormatted }}</pre>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const entry = @js($entry);

        function formatEntryForAI(e) {
            let text = `=== APPLICATION ERROR LOG ===\n`;
            text += `Timestamp: ${e.timestamp}\n`;
            text += `Level: ${e.level.toUpperCase()}\n`;
            text += `Channel: ${e.channel}\n`;
            text += `\n--- Error Message ---\n`;
            text += `${e.message}\n`;

            if (e.context) {
                text += `\n--- Context ---\n`;
                text += `${e.context}\n`;
            }

            if (e.stackTrace) {
                text += `\n--- Stack Trace ---\n`;
                text += `${e.stackTrace}\n`;
            }

            text += `\n===============================\n`;

            return text;
        }

        function copyEntry() {
            const text = formatEntryForAI(entry);
            copyToClipboard(text, 'Error berhasil di-copy ke clipboard!');
        }

        function copySection(sectionId) {
            const el = document.getElementById(sectionId);
            if (el) {
                copyToClipboard(el.textContent.trim(), `${sectionId} berhasil di-copy!`);
            }
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

        function showToast(message) {
            window.dispatchEvent(new CustomEvent('clipboard-toast', { detail: { message } }));
        }

        document.addEventListener('alpine:init', () => {
            Alpine.store('clipboardToast', '');
            window.addEventListener('clipboard-toast', (e) => {
                Alpine.store('clipboardToast', e.detail.message);
            });
        });
    </script>
    @endpush
</x-app-layout>
