<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Sponsor: {{ $sponsor->company_name }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <form method="POST" action="{{ route('admin.sponsors.update', $sponsor) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="space-y-5">
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan <span class="text-red-500">*</span></label>
                            <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $sponsor->company_name) }}" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                            @error('company_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Kontak <span class="text-red-500">*</span></label>
                                <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name', $sponsor->contact_name) }}" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                                @error('contact_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">Email Kontak <span class="text-red-500">*</span></label>
                                <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $sponsor->contact_email) }}" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                                @error('contact_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                                <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $sponsor->contact_phone) }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                                @error('contact_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="industry" class="block text-sm font-medium text-gray-700 mb-1">Industri</label>
                                <input type="text" name="industry" id="industry" value="{{ old('industry', $sponsor->industry) }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                                @error('industry') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="website_url" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                            <input type="url" name="website_url" id="website_url" value="{{ old('website_url', $sponsor->website_url) }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                            @error('website_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Logo Sponsor</label>
                            @if($sponsor->logo_url)
                                <div class="mb-2">
                                    <img src="{{ $sponsor->logo_url }}" alt="Logo saat ini" class="w-16 h-16 rounded-lg object-cover">
                                    <p class="text-xs text-gray-500 mt-1">Logo saat ini. Upload baru untuk mengganti.</p>
                                </div>
                            @endif
                            <input type="file" name="logo" id="logo" accept="image/*"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                            @error('logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('notes', $sponsor->notes) }}</textarea>
                            @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $sponsor->is_active) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <label for="is_active" class="text-sm font-medium text-gray-700">Aktif</label>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Batal</a>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">Perbarui Sponsor</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
