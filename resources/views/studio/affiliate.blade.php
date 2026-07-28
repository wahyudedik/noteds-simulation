<x-studio-layout :pageTitle="'Link Afiliasi'">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Link Afiliasi</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Bagikan link afiliasi simulasi Anda dan dapatkan komisi {{ ($commissionRate * 100) }}% dari setiap pembelian</p>
        </div>
        <a href="{{ route('studio.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">← Kembali</a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Link</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_links'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Klik</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ number_format($stats['total_clicks']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500 dark:text-gray-400">Konversi</p>
            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ $stats['total_conversions'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Komisi</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">Rp {{ number_format($stats['total_commission'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ ($commissionRate * 100) }}% per konversi</p>
        </div>
    </div>

    {{-- Generate New Link --}}
    @if($simulations->count() > 0)
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-6 mb-6 shadow-sm">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Buat Link Afiliasi Baru</h3>
            <form method="POST" action="{{ route('studio.affiliate.generate') }}" class="flex items-end gap-4">
                @csrf
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Simulasi</label>
                    <select name="simulation_id" required
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">— Pilih Simulasi —</option>
                        @foreach($simulations as $sim)
                            <option value="{{ $sim->id }}">{{ $sim->title }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    Buat Link
                </button>
            </form>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-8 mb-6 shadow-sm text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada simulasi yang dipublikasikan.</p>
            <a href="{{ route('studio.simulations.create') }}" class="mt-3 inline-block text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">Upload Simulasi →</a>
        </div>
    @endif

    {{-- Affiliate Links Table --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Link Afiliasi Aktif</h3>
        </div>

        @if($links->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Simulasi</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Link</th>
                            <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Klik</th>
                            <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Konversi</th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Komisi</th>
                            <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($links as $link)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4">
                                    <a href="{{ route('simulations.show', $link->simulation->slug) }}" class="text-gray-900 dark:text-white font-medium hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $link->simulation->title }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <code class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs font-mono">{{ $link->code }}</code>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <input type="text" readonly value="{{ $link->url }}"
                                            class="w-48 px-2 py-1 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded text-xs font-mono bg-gray-50"
                                            id="link-{{ $link->id }}">
                                        <button type="button" onclick="copyToClipboard(this, 'link-{{ $link->id }}')"
                                            class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition" title="Salin link">
                                            📋
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-700 dark:text-gray-300">{{ number_format($link->clicks) }}</td>
                                <td class="px-6 py-4 text-center text-gray-700 dark:text-gray-300">{{ $link->conversions }}</td>
                                <td class="px-6 py-4 text-right text-green-600 dark:text-green-400 font-medium">Rp {{ number_format($link->total_commission, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="{{ route('studio.affiliate.destroy', $link) }}" class="inline"
                                        onsubmit="return confirm('Yakin ingin menghapus link afiliasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs font-medium transition">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada link afiliasi.</p>
                <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">Buat link afiliasi untuk simulasi Anda di atas.</p>
            </div>
        @endif
    </div>

    {{-- Info Box --}}
    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-5">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300">Cara Kerja Afiliasi</h4>
                <ul class="mt-2 text-sm text-blue-700 dark:text-blue-400 space-y-1">
                    <li>1. Buat link afiliasi untuk simulasi yang ingin Anda promosikan.</li>
                    <li>2. Bagikan link tersebut ke media sosial, blog, atau platform lainnya.</li>
                    <li>3. Ketika seseorang mengklik link dan melakukan pembelian, Anda mendapat komisi {{ ($commissionRate * 100) }}%.</li>
                    <li>4. Komisi akan terakumulasi dan dapat dicairkan melalui halaman Payouts.</li>
                </ul>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function copyToClipboard(button, inputId) {
            var text = document.getElementById(inputId).value;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    showCopied(button);
                }).catch(function() {
                    fallbackCopy(text, button);
                });
            } else {
                fallbackCopy(text, button);
            }
        }

        function fallbackCopy(text, button) {
            var textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                showCopied(button);
            } catch (e) {
                // Silently fail — clipboard not available
            }
            document.body.removeChild(textarea);
        }

        function showCopied(button) {
            var original = button.textContent;
            button.textContent = '✓';
            setTimeout(function() { button.textContent = original; }, 1500);
        }
    </script>
    @endpush
</x-studio-layout>
