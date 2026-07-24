<x-studio-layout :pageTitle="'Pengaturan Studio'">
    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('studio.settings.update') }}" enctype="multipart/form-data" x-data="{ saving: false, avatarSrc: '{{ $user->avatar ? Storage::url($user->avatar) : '' }}' }" @submit="saving = true">
            @csrf
            @method('PUT')

            {{-- Profile Info --}}
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Profil Kreator</h3>

                {{-- Avatar --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
                    <div class="flex items-center gap-4">
                        <div class="shrink-0">
                            <div class="w-20 h-20 rounded-full overflow-hidden bg-blue-100 text-blue-600 flex items-center justify-center text-2xl font-bold">
                                <span x-show="!avatarSrc">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                <img x-show="avatarSrc" x-cloak :src="avatarSrc" alt="{{ $user->name }}" class="w-20 h-20 rounded-full object-cover" />
                            </div>
                        </div>
                        <div>
                            <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="hidden" x-ref="avatarInput"
                                   @change="const file = $refs.avatarInput.files[0]; if(file) { const reader = new FileReader(); reader.onload = e => avatarSrc = e.target.result; reader.readAsDataURL(file); }" />
                            <button type="button" @click="$refs.avatarInput.click()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                                Ubah Foto
                            </button>
                            <p class="text-xs text-gray-400 mt-1">JPEG/PNG/WebP, maks 2MB</p>
                        </div>
                    </div>
                    @error('avatar')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Name --}}
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required maxlength="255"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm @error('name') border-red-500 @enderror" />
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Bio --}}
                <div x-data="{ bioText: '{{ Str::replace("'", "\\'", old('bio', $user->bio ?? '')) }}' }">
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                    <textarea name="bio" id="bio" rows="4" maxlength="1000" x-model="bioText"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm @error('bio') border-red-500 @enderror"
                              placeholder="Ceritakan tentang diri Anda sebagai kreator...">{{ old('bio', $user->bio) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1 text-right"><span x-text="bioText.length"></span>/1000 karakter</p>
                    @error('bio')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Account Info (Read-only) --}}
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Akun</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">Email</span>
                        <span class="text-sm font-medium text-gray-900">{{ $user->email }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">Role</span>
                        <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-700 rounded-full capitalize">{{ $user->role }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">Total Simulasi</span>
                        <span class="text-sm font-medium text-gray-900">{{ $user->simulations_count }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-500">Bergabung</span>
                        <span class="text-sm font-medium text-gray-900">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('studio.dashboard') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition"
                        :disabled="saving">
                    <span x-show="!saving">Simpan Perubahan</span>
                    <span x-show="saving" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" class="opacity-75"></path></svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>
</x-studio-layout>
