<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                {{ $collection->title }}
            </h2>
            <div class="flex items-center gap-3">
                @if($collection->user_id === auth()->id())
                    <a href="{{ route('collections.edit', $collection) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[['label' => 'Collections', 'url' => route('collections.index')], ['label' => $collection->title]]" />

            {{-- Collection Header --}}
            <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 mb-8">
                <div class="flex flex-col sm:flex-row items-start gap-6">
                    {{-- Thumbnail --}}
                    <div class="w-full sm:w-48 aspect-video sm:aspect-square bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl overflow-hidden flex-shrink-0">
                        @if($collection->thumbnail)
                            <img src="{{ Storage::disk('public')->url($collection->thumbnail) }}" alt="{{ $collection->title }}" class="w-full h-full object-cover" />
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $collection->title }}</h1>
                        @if($collection->description)
                            <p class="text-gray-500 text-sm mt-2">{{ $collection->description }}</p>
                        @endif

                        <div class="flex items-center gap-4 mt-4">
                            <a href="{{ route('creators.show', $collection->user->id) }}" class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-semibold overflow-hidden">
                                    @if($collection->user->avatar)
                                        <img src="{{ Storage::disk('public')->url($collection->user->avatar) }}" alt="" class="w-full h-full object-cover" />
                                    @else
                                        {{ strtoupper(substr($collection->user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <span class="text-sm font-medium text-gray-700 hover:text-blue-600 transition">{{ $collection->user->name }}</span>
                            </a>
                        </div>

                        <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
                            <span>{{ $collection->simulations->count() }} simulasi</span>
                            <span>&middot;</span>
                            <span>{{ $collection->formatted_view_count }} dilihat</span>
                            <span>&middot;</span>
                            <span>{{ $saveCount }} tersimpan</span>
                            <span>&middot;</span>
                            <span>{{ $collection->time_ago }}</span>
                        </div>

                        {{-- Save Button (for non-owners) --}}
                        @auth
                            @if(auth()->id() !== $collection->user_id)
                                <button
                                    id="save-collection-btn"
                                    onclick="toggleSaveCollection()"
                                    class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-full transition {{ $isSaved ? 'bg-blue-100 text-blue-700 hover:bg-blue-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                                >
                                    <svg id="save-collection-icon" class="w-4 h-4" fill="{{ $isSaved ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                                    <span id="save-collection-text">{{ $isSaved ? 'Tersimpan' : 'Simpan Collection' }}</span>
                                </button>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>

            {{-- Simulations --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Daftar Simulasi</h2>

                @if($collection->simulations->count() > 0)
                    <div class="space-y-3">
                        @foreach($collection->simulations as $index => $sim)
                            <a href="{{ route('simulations.show', $sim->slug) }}" class="flex items-center gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition group">
                                <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-sm font-semibold flex-shrink-0">
                                    {{ $index + 1 }}
                                </div>
                                <div class="w-32 aspect-video bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                    @if($sim->thumbnail)
                                        <img src="{{ Storage::disk('public')->url($sim->thumbnail) }}" alt="{{ $sim->title }}" class="w-full h-full object-cover" />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600">
                                            <svg class="w-6 h-6 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition line-clamp-1">{{ $sim->title }}</h3>
                                    <p class="text-xs text-gray-500 mt-1">{{ $sim->user->name }} &middot; {{ $sim->formatted_play_count }} dimainkan</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <h3 class="text-gray-500 text-lg font-medium">Belum ada simulasi</h3>
                        <p class="text-gray-400 text-sm mt-2">Collection ini belum memiliki simulasi.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function toggleSaveCollection() {
            ajaxPost('{{ route("saved-collections.toggle", $collection->id) }}', {}, function(result) {
                if (!result) return;
                var btn = document.getElementById('save-collection-btn');
                var icon = document.getElementById('save-collection-icon');
                var text = document.getElementById('save-collection-text');
                if (result.saved) {
                    btn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    btn.classList.add('bg-blue-100', 'text-blue-700', 'hover:bg-blue-200');
                    icon.setAttribute('fill', 'currentColor');
                    text.textContent = 'Tersimpan';
                } else {
                    btn.classList.remove('bg-blue-100', 'text-blue-700', 'hover:bg-blue-200');
                    btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    icon.setAttribute('fill', 'none');
                    text.textContent = 'Simpan Collection';
                }
            });
        }
    </script>
</x-app-layout>
