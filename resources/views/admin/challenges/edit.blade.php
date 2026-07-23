<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.challenges.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit: {{ $challenge->title }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <form method="POST" action="{{ route('admin.challenges.update', $challenge) }}">
                    @csrf
                    @method('PUT')
                    <div class="space-y-5">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Challenge</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $challenge->title) }}" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="description" id="description" rows="4" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('description', $challenge->description) }}</textarea>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                                <select name="type" id="type" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="weekly" {{ old('type', $challenge->type) === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                                    <option value="monthly" {{ old('type', $challenge->type) === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                    <option value="annual" {{ old('type', $challenge->type) === 'annual' ? 'selected' : '' }}>Tahunan</option>
                                </select>
                            </div>
                            <div>
                                <label for="theme" class="block text-sm font-medium text-gray-700 mb-1">Tema</label>
                                <input type="text" name="theme" id="theme" value="{{ old('theme', $challenge->theme) }}" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="status" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="upcoming" {{ old('status', $challenge->status) === 'upcoming' ? 'selected' : '' }}>Mendatang</option>
                                    <option value="active" {{ old('status', $challenge->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="judging" {{ old('status', $challenge->status) === 'judging' ? 'selected' : '' }}>Penilaian</option>
                                    <option value="completed" {{ old('status', $challenge->status) === 'completed' ? 'selected' : '' }}>Selesai</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                <input type="datetime-local" name="start_date" id="start_date"
                                    value="{{ old('start_date', $challenge->start_date->format('Y-m-d\TH:i')) }}" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                                <input type="datetime-local" name="end_date" id="end_date"
                                    value="{{ old('end_date', $challenge->end_date->format('Y-m-d\TH:i')) }}" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                            </div>
                        </div>

                        <div>
                            <label for="prize_description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Hadiah</label>
                            <textarea name="prize_description" id="prize_description" rows="2"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('prize_description', $challenge->prize_description) }}</textarea>
                        </div>

                        <div class="flex justify-between pt-4 border-t border-gray-100">
                            <form method="POST" action="{{ route('admin.challenges.destroy', $challenge) }}" onsubmit="return confirm('Yakin hapus challenge ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">Hapus</button>
                            </form>
                            <div class="flex gap-3">
                                <a href="{{ route('admin.challenges.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Batal</a>
                                <button type="submit" form class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
