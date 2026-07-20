<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Avatar --}}
        <div x-data="{ preview: '{{ $user->avatar ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar) : '' }}' }">
            <x-input-label for="avatar" :value="__('Foto Profil')" />

            <div class="mt-2 flex items-center gap-4">
                {{-- Preview --}}
                <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 border border-gray-200 flex-shrink-0">
                    <template x-if="preview">
                        <img :src="preview" alt="Preview" class="w-full h-full object-cover" />
                    </template>
                    <template x-if="!preview">
                        @if($user->avatar)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar) }}" alt="" class="w-full h-full object-cover" />
                        @else
                            <div class="w-full h-full flex items-center justify-center text-2xl font-semibold text-gray-400">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </template>
                </div>

                {{-- Upload --}}
                <div class="flex-1">
                    <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/webp"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        onchange="const reader = new FileReader(); reader.onload = e => { $dispatch('avatar-preview', e.target.result); }; reader.readAsDataURL(this.files[0]);"
                    />
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, atau WebP. Maks 2MB.</p>

                    @if($user->avatar)
                        <label class="inline-flex items-center gap-1 mt-2 text-xs text-red-600 hover:text-red-700 cursor-pointer">
                            <input type="checkbox" name="remove_avatar" value="1" class="rounded border-gray-300 text-red-600" />
                            Hapus foto profil
                        </label>
                    @endif
                </div>
            </div>

            <script>
                document.addEventListener('alpine:init', () => {
                    window.addEventListener('avatar-preview', (e) => {
                        Alpine.store('avatarPreview') = e.detail;
                    });
                });
            </script>
            <div x-data x-effect="if($event && $event.detail) preview = $event.detail"
                @avatar-preview.window="preview = $event.detail"></div>

            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        {{-- Name --}}
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- Bio --}}
        <div>
            <x-input-label for="bio" :value="__('Bio')" />
            <textarea id="bio" name="bio" rows="3"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                placeholder="Ceritakan sedikit tentang diri Anda...">{{ old('bio', $user->bio) }}</textarea>
            <p class="text-xs text-gray-400 mt-1">Maks 500 karakter.</p>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
