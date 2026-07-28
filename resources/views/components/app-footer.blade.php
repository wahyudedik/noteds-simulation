{{-- Global Footer Component --}}
<footer class="bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            {{-- Brand --}}
            <div class="sm:col-span-2 lg:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2 mb-3">
                    <img src="{{ asset('logo.jpeg') }}" alt="Noteds" class="w-8 h-8 rounded-lg object-cover" />
                    <span class="text-lg font-bold text-gray-900 dark:text-white">Noteds</span>
                </a>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                    Platform simulasi interaktif untuk belajar sains, teknik, dan teknologi dengan cara yang menyenangkan.
                </p>
            </div>

            {{-- Explore --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Jelajahi</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('simulations.explore') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
                            Semua Experience
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('forum.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
                            Komunitas
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('leaderboard.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
                            Papan Skor
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Creator --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Creator</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('studio.dashboard') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
                            Simulation Studio
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('become-creator') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
                            Jadi Creator
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Legal --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Legal</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('privacy-policy') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
                            Kebijakan Privasi
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('terms-of-service') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
                            Syarat & Ketentuan
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-gray-400 dark:text-gray-500">
                &copy; {{ date('Y') }} {{ config('app.name', 'Noteds') }}. Hak cipta dilindungi.
            </p>
            <div class="flex items-center gap-4">
                <a href="https://github.com" target="_blank" rel="noopener noreferrer" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition" aria-label="GitHub">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                </a>
            </div>
        </div>
    </div>
</footer>
