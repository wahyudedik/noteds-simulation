<x-studio-layout :pageTitle="'Followers Saya'">
    <div class="max-w-4xl mx-auto">
        {{-- Summary --}}
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $followers->total() }}</p>
                    <p class="text-sm text-gray-500">Total Followers</p>
                </div>
            </div>
        </div>

        {{-- Followers List --}}
        @if($followers->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($followers as $follow)
                    @php
                        $followerUser = $follow->follower;
                    @endphp
                    <a href="{{ route('creators.show', $followerUser->id) }}" class="bg-white border border-gray-100 rounded-xl shadow-sm p-4 hover:shadow-md transition flex items-center gap-4">
                        <img src="{{ $followerUser->avatar ? Storage::url($followerUser->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($followerUser->name) . '&background=0D8ABC&color=fff&size=48' }}"
                             alt="{{ $followerUser->name }}"
                             class="w-12 h-12 rounded-full object-cover shrink-0" />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $followerUser->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $followerUser->bio ?? 'Tidak ada bio' }}</p>
                            <p class="text-xs text-gray-400 mt-1">Followed {{ $follow->created_at->diffForHumans() }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $followers->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-white border border-gray-100 rounded-xl shadow-sm">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada followers</h3>
                <p class="text-sm text-gray-500">Bagikan profil Anda untuk mendapatkan followers.</p>
            </div>
        @endif
    </div>
</x-studio-layout>
