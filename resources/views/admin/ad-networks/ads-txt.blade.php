<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Kelola ads.txt
            </h2>
            <a href="{{ route('admin.ad-networks.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    {{-- Info --}}
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            <div class="text-sm text-yellow-700 dark:text-yellow-300">
                                <p class="font-medium">Tentang ads.txt</p>
                                <p class="mt-1">File ads.txt membantu mencegah inventaris iklan palsu. Setiap ad network yang diaktifkan akan otomatis menambahkan entry-nya di sini. Anda juga bisa edit manual di bawah ini.</p>
                                <p class="mt-1">File ini disimpan di: <code class="bg-yellow-100 dark:bg-yellow-800 px-1 rounded">public/ads.txt</code></p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.ad-networks.ads-txt.save') }}" method="POST">
                        @csrf
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Isi ads.txt</label>
                            <textarea name="content" id="content" rows="20"
                                      class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm font-mono">{{ $entries }}</textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 mt-4">
                            <button type="button" onclick="document.getElementById('content').value = generateAuto()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                                Generate Otomatis
                            </button>
                            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                                Simpan ads.txt
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function generateAuto() {
            return `# ads.txt — Noteds Interactive Experience Platform
# Generated automatically

# Google AdSense
google.com, pub-2771325503977360, DIRECT, f08c47fec0942fa0`;
        }
    </script>
    @endpush
</x-app-layout>
