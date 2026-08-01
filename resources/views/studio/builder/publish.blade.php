@php
    $pageTitle = 'Publish: ' . $project->title;
@endphp

<x-studio-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <x-breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('studio.dashboard')],
                ['label' => 'Builder', 'url' => route('studio.builder.index')],
                ['label' => $project->title, 'url' => route('studio.builder.projects.edit', $project->slug)],
                ['label' => 'Publish'],
            ]" />

            {{-- Header --}}
            <div class="mt-6 mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Publish Experience
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Make your experience available on the platform for everyone to discover and play.
                </p>
            </div>

            {{-- Status Banner --}}
            @if($project->hasSimulation())
                <div class="mb-6 p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">
                                This experience is published on the platform.
                            </p>
                            <a href="{{ $project->getSimulationUrl() }}" target="_blank" class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">
                                View on platform →
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Publish Form --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <form
                    x-data="publishForm()"
                    x-on:submit.prevent="submit()"
                    action="{{ route('studio.builder.projects.publish-confirm', $project->slug) }}"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    @csrf

                    {{-- Thumbnail Preview --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Thumbnail
                        </label>
                        <div class="flex items-start gap-4">
                            <div class="w-48 h-27 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                                <template x-if="thumbnailPreview">
                                    <img :src="thumbnailPreview" class="w-full h-full object-cover" alt="Thumbnail preview" />
                                </template>
                                <template x-if="!thumbnailPreview">
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </template>
                            </div>
                            <div class="flex-1">
                                <input
                                    type="file"
                                    name="thumbnail"
                                    x-on:change="handleThumbnail($event)"
                                    accept="image/*"
                                    class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 dark:file:bg-purple-900/30 dark:file:text-purple-400 dark:hover:file:bg-purple-900/50"
                                />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    PNG, JPG, WEBP (max 2MB). Recommended: 1280×720
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="mb-4">
                        <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="category"
                            name="category"
                            x-model="category"
                            required
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"
                        >
                            <option value="">Select category...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Subcategory --}}
                    <div class="mb-4">
                        <label for="subcategory" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Subcategory
                        </label>
                        <input
                            type="text"
                            id="subcategory"
                            name="subcategory"
                            x-model="subcategory"
                            placeholder="Optional subcategory"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"
                        />
                    </div>

                    {{-- Tags --}}
                    <div class="mb-4">
                        <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Tags
                        </label>
                        <input
                            type="text"
                            id="tags"
                            name="tags"
                            x-model="tags"
                            placeholder="Comma-separated tags (e.g. education, math, interactive)"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"
                        />
                    </div>

                    {{-- Description --}}
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Description
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            x-model="description"
                            rows="3"
                            placeholder="Describe your experience..."
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"
                        ></textarea>
                    </div>

                    {{-- Error Message --}}
                    <div x-show="errorMessage" x-cloak class="mb-4 p-3 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-sm">
                        <span x-text="errorMessage"></span>
                    </div>

                    {{-- Success Message --}}
                    <div x-show="successMessage" x-cloak class="mb-4 p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-sm">
                        <span x-text="successMessage"></span>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-between">
                        <a
                            href="{{ route('studio.builder.projects.edit', $project->slug) }}"
                            class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
                        >
                            ← Back to editor
                        </a>
                        <div class="flex items-center gap-3">
                            @if($project->hasSimulation())
                                <form
                                    id="unpublish-form"
                                    action="{{ route('studio.builder.projects.unpublish', $project->slug) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method('POST')
                                    <button
                                        type="button"
                                        onclick="confirmSubmit(document.getElementById('unpublish-form'), 'Unpublish this experience from the platform?', { title: 'Unpublish', confirmText: 'Ya, Unpublish' })"
                                        class="px-4 py-2 text-sm font-medium text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition"
                                    >
                                        Unpublish
                                    </button>
                                </form>
                            @endif
                            <button
                                type="submit"
                                :disabled="submitting"
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition"
                            >
                                <template x-if="submitting">
                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </template>
                                <span x-text="submitting ? 'Publishing...' : 'Publish to Platform'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-studio-layout>

<script>
function publishForm() {
    return {
        category: '',
        subcategory: '',
        tags: '',
        description: @js($project->description ?? ''),
        thumbnailPreview: null,
        thumbnailFile: null,
        submitting: false,
        errorMessage: '',
        successMessage: '',

        handleThumbnail(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                this.errorMessage = 'Thumbnail must be less than 2MB';
                return;
            }

            this.thumbnailFile = file;
            this.thumbnailPreview = URL.createObjectURL(file);
            this.errorMessage = '';
        },

        async submit() {
            if (!this.category) {
                this.errorMessage = 'Category is required';
                return;
            }

            this.submitting = true;
            this.errorMessage = '';
            this.successMessage = '';

            try {
                const formData = new FormData();
                formData.append('category', this.category);
                formData.append('subcategory', this.subcategory);
                formData.append('tags', this.tags);
                formData.append('description', this.description);
                if (this.thumbnailFile) {
                    formData.append('thumbnail', this.thumbnailFile);
                }

                const response = await fetch(this.$el.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.successMessage = data.message;
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                } else {
                    this.errorMessage = data.message || 'Failed to publish. Please try again.';
                }
            } catch (error) {
                this.errorMessage = 'An error occurred. Please try again.';
            } finally {
                this.submitting = false;
            }
        }
    };
}
</script>
