@php $pageTitle = 'Pilih Template'; @endphp
<x-studio-layout>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('studio.dashboard')],
                ['label' => 'Experience Builder', 'url' => route('studio.builder.index')],
                ['label' => 'Pilih Template'],
            ]" />

            <p class="text-gray-600 dark:text-gray-400 mt-4 mb-8">Pilih template untuk memulai, atau buat dari nol.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Blank Project --}}
                <div x-data="{ showCreate: false }">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-dashed border-gray-300 dark:border-gray-600 p-6 text-center hover:border-purple-400 dark:hover:border-purple-500 transition cursor-pointer h-full flex flex-col items-center justify-center min-h-[280px]"
                         x-on:click="showCreate = true">
                        <div class="w-16 h-16 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Blank Project</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Mulai dari nol tanpa template.</p>
                    </div>

                    {{-- Create Modal --}}
                    <div x-show="showCreate" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-on:click.self="showCreate = false" style="display:none">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full mx-4 p-6" x-on:click.stop>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Project Baru</h3>
                            <form action="{{ route('studio.builder.projects.create') }}" method="POST">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label for="blank-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Project</label>
                                        <input type="text" name="title" id="blank-title" required placeholder="e.g. Newton's Law Simulator"
                                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                                    </div>
                                    <div>
                                        <label for="blank-desc" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi (opsional)</label>
                                        <textarea name="description" id="blank-desc" rows="2" placeholder="Deskripsi singkat..."
                                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"></textarea>
                                    </div>
                                </div>
                                <div class="flex items-center justify-end gap-3 mt-6">
                                    <button type="button" x-on:click="showCreate = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">Batal</button>
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-lg transition">Buat Project</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Template Cards --}}
                @foreach($templates as $template)
                    <div x-data="{ showCreate: false }">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition cursor-pointer h-full flex flex-col"
                             x-on:click="showCreate = true">
                            {{-- Thumbnail --}}
                            <div class="h-40 bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
                                @if($template->thumbnail_url)
                                    <img src="{{ $template->thumbnail_url }}" alt="{{ $template->name }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-12 h-12 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z" /></svg>
                                @endif
                            </div>
                            <div class="p-4 flex-1 flex flex-col">
                                <div class="flex items-start justify-between">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm">{{ $template->name }}</h4>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                        {{ $template->category }}
                                    </span>
                                </div>
                                @if($template->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex-1">{{ $template->description }}</p>
                                @endif
                                <div class="mt-3 text-xs text-gray-400 dark:text-gray-500">
                                    {{ count($template->getComponents()) }} komponen
                                </div>
                            </div>
                        </div>

                        {{-- Create from Template Modal --}}
                        <div x-show="showCreate" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-on:click.self="showCreate = false" style="display:none">
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full mx-4 p-6" x-on:click.stop>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Buat dari Template</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Template: <strong>{{ $template->name }}</strong></p>
                                <form action="{{ route('studio.builder.projects.create') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="template_slug" value="{{ $template->slug }}">
                                    <div class="space-y-4">
                                        <div>
                                            <label for="tpl-title-{{ $template->slug }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Project</label>
                                            <input type="text" name="title" id="tpl-title-{{ $template->slug }}" required placeholder="e.g. My Physics Simulator"
                                                   value="{{ $template->name }}"
                                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                                        </div>
                                        <div>
                                            <label for="tpl-desc-{{ $template->slug }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi (opsional)</label>
                                            <textarea name="description" id="tpl-desc-{{ $template->slug }}" rows="2" placeholder="Deskripsi singkat..."
                                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"></textarea>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-end gap-3 mt-6">
                                        <button type="button" x-on:click="showCreate = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">Batal</button>
                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-lg transition">Buat Project</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-studio-layout>
