<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('simulations.show', $simulation->slug) }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800">Kode Embed — {{ Str::limit($simulation->title, 40) }}</h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6">
        {{-- Preview --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Preview</h3>
            <div class="rounded-xl overflow-hidden border border-gray-200 bg-gray-900" style="aspect-ratio: 16/10;">
                <iframe
                    src="{{ $embedUrl }}"
                    sandbox="allow-scripts allow-same-origin allow-popups allow-forms"
                    class="w-full h-full border-0"
                    loading="lazy"
                    title="{{ $simulation->title }}"
                ></iframe>
            </div>
            <div class="mt-3 flex items-center justify-between">
                <a href="{{ route('embed.show', $simulation->slug) }}" target="_blank" rel="noopener noreferrer" class="text-sm text-blue-600 hover:underline flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Buka di tab baru
                </a>
            </div>
        </div>

        {{-- Embed Code --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6" x-data="{ copied: false }">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-700">Kode Embed HTML</h3>
                <button
                    @click="navigator.clipboard.writeText(document.getElementById('embed-code').textContent); copied = true; setTimeout(() => copied = false, 2000)"
                    class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1 transition"
                >
                    <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                    <svg x-show="copied" x-cloak class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="copied ? 'Disalin!' : 'Salin'"></span>
                </button>
            </div>
            <pre id="embed-code" class="bg-gray-900 text-green-400 text-xs p-4 rounded-xl overflow-x-auto leading-relaxed"><code>{{ $embedCode }}</code></pre>
        </div>

        {{-- Instructions --}}
        <div class="bg-blue-50 rounded-2xl border border-blue-100 p-6 mt-6">
            <h3 class="text-sm font-semibold text-blue-900 mb-2">Cara Menggunakan</h3>
            <ol class="text-sm text-blue-800 space-y-1.5 list-decimal list-inside">
                <li>Salin kode embed di atas</li>
                <li>Tempelkan ke HTML halaman website Anda</li>
                <li>S simulasi akan ditampilkan dalam iframe yang aman (sandboxed)</li>
                <li>Ukuran default: 800×600px, dapat diatur dengan atribut <code class="bg-blue-100 px-1 rounded">width</code> dan <code class="bg-blue-100 px-1 rounded">height</code></li>
            </ol>
        </div>
    </div>
</x-app-layout>
