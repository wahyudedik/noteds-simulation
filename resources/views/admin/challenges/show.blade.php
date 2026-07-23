<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.challenges.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $challenge->title }}</h2>
            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $challenge->status_badge_class }}">{{ $challenge->status }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Detail</h3>
                        <dl class="space-y-1 text-sm">
                            <div class="flex justify-between"><dt class="text-gray-500">Tipe</dt><dd class="font-medium">{{ $challenge->type_label }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Tema</dt><dd class="font-medium">{{ $challenge->theme }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Peserta</dt><dd class="font-medium">{{ $entries->count() }}</dd></div>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Periode</h3>
                        <dl class="space-y-1 text-sm">
                            <div class="flex justify-between"><dt class="text-gray-500">Mulai</dt><dd class="font-medium">{{ $challenge->start_date->format('d M Y H:i') }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Selesai</dt><dd class="font-medium">{{ $challenge->end_date->format('d M Y H:i') }}</dd></div>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Hadiah</h3>
                        <p class="text-sm text-gray-700">{{ $challenge->prize_description ?? 'Tidak ada deskripsi hadiah.' }}</p>
                    </div>
                </div>

                @if ($challenge->criteria)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kriteria Penilaian</h3>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($challenge->criteria as $criterion)
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-50 text-blue-700">
                                    {{ $criterion['name'] }} ({{ $criterion['weight'] }}%)
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Entries --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Peserta ({{ $entries->count() }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Simulasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kreator</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Skor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($entries as $index => $entry)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900">#{{ $entry->rank ?? ($index + 1) }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $entry->simulation->title ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $entry->user->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-blue-600">{{ $entry->total_score }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $entry->status_badge_class }}">{{ $entry->status }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @if ($challenge->status === 'judging' || $challenge->status === 'active')
                                            <button onclick="document.getElementById('score-modal-{{ $entry->id }}').classList.remove('hidden')" class="text-blue-600 hover:text-blue-800">Beri Skor</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">Belum ada peserta.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Score Modals --}}
    @foreach ($entries as $entry)
        <div id="score-modal-{{ $entry->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Skor: {{ $entry->simulation->title ?? 'Simulasi' }}</h4>
                <form method="POST" action="{{ route('admin.challenges.score-entry', ['challenge' => $challenge, 'entry' => $entry]) }}">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Akurasi Ilmiah (0-30)</label>
                            <input type="number" name="scientific_accuracy" step="0.5" min="0" max="30" value="{{ $entry->scores['scientific_accuracy'] ?? 0 }}" required class="w-full rounded-lg border-gray-300 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Interaktivitas & UX (0-25)</label>
                            <input type="number" name="interactivity" step="0.5" min="0" max="25" value="{{ $entry->scores['interactivity'] ?? 0 }}" required class="w-full rounded-lg border-gray-300 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Visual & Desain (0-20)</label>
                            <input type="number" name="visual_design" step="0.5" min="0" max="20" value="{{ $entry->scores['visual_design'] ?? 0 }}" required class="w-full rounded-lg border-gray-300 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Kreativitas (0-15)</label>
                            <input type="number" name="creativity" step="0.5" min="0" max="15" value="{{ $entry->scores['creativity'] ?? 0 }}" required class="w-full rounded-lg border-gray-300 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Popularitas (0-10)</label>
                            <input type="number" name="popularity" step="0.5" min="0" max="10" value="{{ $entry->scores['popularity'] ?? 0 }}" required class="w-full rounded-lg border-gray-300 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Catatan (opsional)</label>
                            <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 text-sm">{{ $entry->notes }}</textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-4">
                        <button type="button" onclick="document.getElementById('score-modal-{{ $entry->id }}').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg">Simpan Skor</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
</x-app-layout>
