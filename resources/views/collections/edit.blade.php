<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Collection - {{ config('app.name') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">

    @include('components.app-header')

    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="mb-6">
            <a href="{{ route('collections.index') }}" class="text-sm text-blue-600 hover:text-blue-700 transition">
                &larr; Kembali ke Collection
            </a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Collection</h1>
        </div>

        {{-- Edit Form --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <form action="{{ route('collections.update', $collection) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="space-y-5">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Judul Collection</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $collection->title) }}" required
                            class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm" />
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" id="description" rows="3"
                            class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">{{ old('description', $collection->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_public" value="1" {{ old('is_public', $collection->is_public) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                            <div>
                                <span class="text-sm font-medium text-gray-700">Publik</span>
                                <p class="text-xs text-gray-500">Collection ini bisa dilihat oleh semua orang</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('collections.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        {{-- Add Simulation Search --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6" x-data="collectionSearch()">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Tambah Simulasi</h2>
            <div class="relative">
                <input
                    type="text"
                    x-model="searchQuery"
                    @input.debounce.300ms="searchSimulations()"
                    placeholder="Cari simulasi berjudul..."
                    class="w-full pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                />
                <svg class="absolute right-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>

            {{-- Search Results --}}
            <div x-show="results.length > 0" class="mt-3 space-y-2">
                <template x-for="sim in results" :key="sim.id">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <div class="w-16 aspect-video bg-gray-200 rounded overflow-hidden flex-shrink-0">
                            <template x-if="sim.thumbnail">
                                <img :src="'/storage/' + sim.thumbnail" :alt="sim.title" class="w-full h-full object-cover" />
                            </template>
                            <template x-if="!sim.thumbnail">
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600">
                                    <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                </div>
                            </template>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 line-clamp-1" x-text="sim.title"></p>
                            <p class="text-xs text-gray-500" x-text="sim.category"></p>
                        </div>
                        <button
                            @click="addToCollection(sim.id, $el)"
                            :disabled="adding === sim.id"
                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-300 text-white text-xs font-medium rounded-lg transition shrink-0"
                        >
                            <span x-show="adding !== sim.id">Tambah</span>
                            <span x-show="adding === sim.id">...</span>
                        </button>
                    </div>
                </template>
            </div>

            <p x-show="searchQuery.length >= 2 && results.length === 0 && !loading" class="mt-3 text-sm text-gray-400 text-center">
                Tidak ada simulasi ditemukan
            </p>
        </div>

        {{-- Simulations in Collection --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-data="{ removing: null }">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Simulasi dalam Collection ({{ $collection->simulations->count() }})</h2>

            @if($collection->simulations->count() > 0)
                <div class="space-y-3">
                    @foreach($collection->simulations as $sim)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-20 aspect-video bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                @if($sim->thumbnail)
                                    <img src="{{ Storage::disk('public')->url($sim->thumbnail) }}" alt="{{ $sim->title }}" class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600">
                                        <svg class="w-5 h-5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('simulations.show', $sim->slug) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600 transition line-clamp-1">
                                    {{ $sim->title }}
                                </a>
                                <p class="text-xs text-gray-500">{{ $sim->user->name }} &middot; {{ $sim->formatted_play_count }} dimainkan</p>
                            </div>
                            <button
                                onclick="removeFromCollection({{ $collection->id }}, {{ $sim->id }})"
                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                title="Hapus dari collection">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    <p class="text-gray-500 text-sm">Belum ada simulasi dalam collection ini.</p>
                    <p class="text-gray-400 text-xs mt-1">Gunakan kolom pencarian di atas untuk menambahkan simulasi.</p>
                </div>
            @endif
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('logo.jpeg') }}" alt="Noteds" class="w-6 h-6 rounded object-cover" />
                    <span class="font-semibold text-gray-900">Noteds</span>
                </div>
                <p class="text-sm text-gray-500">
                    Interactive Simulations &copy; {{ date('Y') }}
                </p>
            </div>
        </div>
    </footer>

    <script>
        function collectionSearch() {
            return {
                searchQuery: '',
                results: [],
                loading: false,
                adding: null,
                searchSimulations: function() {
                    var self = this;
                    if (self.searchQuery.length < 2) {
                        self.results = [];
                        return;
                    }
                    self.loading = true;
                    fetch('{{ route("collections.search-simulations") }}?q=' + encodeURIComponent(self.searchQuery), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(function(r) {
                        if (!r.ok || !(r.headers.get('content-type') || '').includes('application/json')) {
                            self.loading = false;
                            return null;
                        }
                        return r.json();
                    })
                    .then(function(data) {
                        self.results = (data && data.simulations) || [];
                        self.loading = false;
                    })
                    .catch(function() { self.loading = false; });
                },
                addToCollection: function(simulationId, btnEl) {
                    var self = this;
                    self.adding = simulationId;
                    fetch('{{ route("collections.add-simulation") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            collection_id: {{ $collection->id }},
                            simulation_id: simulationId
                        })
                    })
                    .then(function(r) {
                        if (!r.ok || !(r.headers.get('content-type') || '').includes('application/json')) {
                            self.adding = null;
                            window.showToast('Sesi Anda telah berakhir. Silakan login kembali.', 'error');
                            return null;
                        }
                        return r.json();
                    })
                    .then(function(result) {
                        self.adding = null;
                        if (!result) return;
                        if (result.success) {
                            window.location.reload();
                        } else {
                            window.showToast(result.message || 'Gagal menambahkan simulasi', 'error');
                        }
                    })
                    .catch(function() { self.adding = null; });
                }
            };
        }

        function removeFromCollection(collectionId, simulationId) {
            showConfirm('Hapus simulasi dari collection ini?').then(function(confirmed) {
                if (!confirmed) return;

                fetch('{{ route("collections.remove-simulation") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        collection_id: collectionId,
                        simulation_id: simulationId
                    })
                })
                .then(function(r) {
                    if (!r.ok || !(r.headers.get('content-type') || '').includes('application/json')) {
                        window.showToast('Sesi Anda telah berakhir. Silakan login kembali.', 'error');
                        return null;
                    }
                    return r.json();
                })
                .then(function(result) {
                    if (!result) return;
                    if (result.success) {
                        window.location.reload();
                    } else {
                        window.showToast(result.message || 'Gagal menghapus simulasi', 'error');
                    }
                })
                .catch(function() {});
            });
        }
    </script>

    <x-toast />
</body>
</html>
