<x-studio-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Experience Builder</h2>
            </div>
            <a href="{{ route('studio.builder.templates') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                New Project
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('studio.dashboard')],
                ['label' => 'Experience Builder'],
            ]" />

            {{-- Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Projects</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $projects->count() }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Published</div>
                    <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $projects->where('status', 'published')->count() }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Drafts</div>
                    <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $projects->where('status', 'draft')->count() }}</div>
                </div>
            </div>

            {{-- Projects List --}}
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">My Projects</h3>

                @if($projects->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($projects as $project)
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition">
                                {{-- Thumbnail --}}
                                <div class="h-40 bg-gradient-to-br from-purple-500 to-blue-600 flex items-center justify-center">
                                    @if($project->thumbnail_path)
                                        <img src="{{ $project->thumbnail_url }}" alt="{{ $project->title }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-12 h-12 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="min-w-0 flex-1">
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm truncate">{{ $project->title }}</h4>
                                            @if($project->template)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Template: {{ $project->template->name }}</p>
                                            @endif
                                        </div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $project->status_badge_class }}">
                                            {{ ucfirst($project->status) }}
                                        </span>
                                    </div>

                                    @if($project->description)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 line-clamp-2">{{ $project->description }}</p>
                                    @endif

                                    <div class="flex items-center gap-2 mt-3 text-xs text-gray-400 dark:text-gray-500">
                                        <span>{{ $project->created_at->diffForHumans() }}</span>
                                        <span>&middot;</span>
                                        <span>v{{ $project->version }}</span>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                        <a href="{{ route('studio.builder.projects.edit', $project->slug) }}"
                                           class="flex-1 inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            Edit
                                        </a>
                                        @if($project->isDraft())
                                            <form action="{{ route('studio.builder.projects.publish', $project->slug) }}" method="POST" class="flex-1">
                                                @csrf
                                                <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                    Publish
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('studio.builder.projects.destroy', $project->slug) }}" method="POST" class="flex-shrink-0"
                                              x-data x-on:submit.prevent="confirmSubmit(this.closest('form'), 'Yakin ingin menghapus project ini?', { title: 'Hapus Project', confirmText: 'Ya, Hapus' })">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
                        <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mt-4 mb-2">Belum ada project</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">Mulai buat experience interaktif dari template atau dari nol.</p>
                        <a href="{{ route('studio.builder.templates') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Buat Project Baru
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-studio-layout>
