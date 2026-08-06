@php $pageTitle = $project->title . ' — Builder'; @endphp
<x-studio-layout><div x-data="builder()" x-init="init()">
    {{-- Toolbar --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2 min-w-0">
            <a href="{{ route('studio.builder.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition flex-shrink-0" aria-label="Kembali ke Builder">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $project->status_badge_class }} flex-shrink-0">
                {{ ucfirst($project->status) }}
            </span>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <button x-on:click="previewProject()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                Preview
            </button>
            <form action="{{ route('studio.builder.projects.export', $project->slug) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Export ZIP
                </button>
            </form>
            <button x-on:click="saveProject()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-lg transition" :class="{ 'opacity-50 pointer-events-none': saving }">
                <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span x-text="saving ? 'Saving...' : 'Save'"></span>
            </button>
            <a href="{{ route('studio.builder.projects.publish', $project->slug) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Publish
            </a>
        </div>
    </div>

    {{-- Editor Container --}}
    <div class="h-[calc(100vh-12rem)] flex">

        {{-- Left Panel: Component Tree --}}
        <div class="w-64 flex-shrink-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col overflow-hidden"
             x-show="leftPanelOpen" x-transition>
            <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Components</h3>
            </div>

            {{-- Available Components --}}
            <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-2">Drag to add:</p>
                <div class="space-y-1">
                    @foreach($availableComponents as $type => $comp)
                        <button x-on:click="addComponent('{{ $type }}')"
                                class="w-full flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition text-left">
                            <span class="w-6 h-6 rounded bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 text-xs font-bold">
                                {{ strtoupper(substr($comp['label'], 0, 1)) }}
                            </span>
                            {{ $comp['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Project Components --}}
            <div class="flex-1 overflow-y-auto p-3">
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-2">In this project:</p>
                <template x-if="components.length === 0">
                    <p class="text-xs text-gray-400 dark:text-gray-500 italic">No components yet. Add one above.</p>
                </template>
                <div class="space-y-1">
                    <template x-for="(comp, index) in components" :key="comp.id">
                        <div class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg cursor-pointer transition"
                             x-on:click="selectComponent(index)"
                             :class="selectedIndex === index ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
                            <span class="w-5 h-5 rounded bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 text-[10px] font-bold flex-shrink-0"
                                  x-text="comp.type.substring(0,2).toUpperCase()"></span>
                            <span class="flex-1 truncate text-xs" x-text="comp.label"></span>
                            <button x-on:click.stop="removeComponent(index)" class="text-gray-400 hover:text-red-500 transition flex-shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Center Panel: Preview --}}
        <div class="flex-1 bg-gray-50 dark:bg-gray-900 overflow-y-auto">
            {{-- Toggle Left Panel --}}
            <div class="sticky top-0 z-10 bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-3 py-1.5 flex items-center gap-2">
                <button x-on:click="leftPanelOpen = !leftPanelOpen" class="min-w-[44px] min-h-[44px] flex items-center justify-center text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition" title="Toggle component panel">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <span class="text-xs text-gray-500 dark:text-gray-400">Preview</span>
                <span class="text-xs text-gray-400 dark:text-gray-500" x-text="components.length + ' components'"></span>
            </div>

            <div class="p-6 max-w-3xl mx-auto min-h-[calc(100%-2.5rem)]">
                <template x-if="components.length === 0">
                    <div class="text-center py-20">
                        <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        <p class="text-gray-400 dark:text-gray-500 mt-4">Add components from the left panel to start building.</p>
                    </div>
                </template>

                <div class="space-y-4">
                    <template x-for="(comp, index) in components" :key="comp.id">
                        <div class="bg-white dark:bg-gray-800 rounded-lg border-2 p-4 cursor-pointer transition hover:border-purple-300 dark:hover:border-purple-600"
                             x-on:click="selectComponent(index)"
                             :class="selectedIndex === index ? 'border-purple-500 dark:border-purple-500 shadow-md' : 'border-transparent'">
                            <div class="text-xs text-gray-400 dark:text-gray-500 mb-2 uppercase tracking-wider" x-text="comp.type"></div>
                            <div x-html="renderPreview(comp)"></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Right Panel: Property Inspector --}}
        <div class="w-72 flex-shrink-0 bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 flex flex-col overflow-hidden"
             x-show="selectedIndex !== null" x-transition>
            <div class="p-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Properties</h3>
                <button x-on:click="selectedIndex = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <template x-if="selectedIndex !== null && components[selectedIndex]">
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                {{-- Component Label --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Label</label>
                    <input type="text" x-model="components[selectedIndex].label"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm text-sm focus:border-purple-500 focus:ring-purple-500"
                           x-on:input="debounceSave()">
                </div>

                {{-- Dynamic Properties --}}
                <template x-for="(propDef, propName) in getComponentSchema(selectedIndex)" :key="propName">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1" x-text="propDef.label"></label>

                        {{-- Text / Number --}}
                        <template x-if="propDef.type === 'text' || propDef.type === 'number' || propDef.type === 'textarea'">
                            <div>
                                <template x-if="propDef.type === 'textarea'">
                                    <textarea x-model="components[selectedIndex].properties[propName]"
                                              rows="3"
                                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm text-sm focus:border-purple-500 focus:ring-purple-500"
                                              x-on:input="debounceSave()"></textarea>
                                </template>
                                <template x-if="propDef.type !== 'textarea'">
                                    <input :type="propDef.type" x-model="components[selectedIndex].properties[propName]"
                                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm text-sm focus:border-purple-500 focus:ring-purple-500"
                                           x-on:input="debounceSave()">
                                </template>
                            </div>
                        </template>

                        {{-- Color --}}
                        <template x-if="propDef.type === 'color'">
                            <div class="flex items-center gap-2">
                                <input type="color" x-model="components[selectedIndex].properties[propName]"
                                       class="w-10 h-8 rounded border-0 cursor-pointer"
                                       x-on:input="debounceSave()">
                                <input type="text" x-model="components[selectedIndex].properties[propName]"
                                       class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm text-sm font-mono focus:border-purple-500 focus:ring-purple-500"
                                       x-on:input="debounceSave()">
                            </div>
                        </template>

                        {{-- Select --}}
                        <template x-if="propDef.type === 'select'">
                            <select x-model="components[selectedIndex].properties[propName]"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm text-sm focus:border-purple-500 focus:ring-purple-500"
                                    x-on:change="debounceSave()">
                                <template x-for="opt in propDef.options" :key="opt">
                                    <option :value="opt" x-text="opt"></option>
                                </template>
                            </select>
                        </template>
                    </div>
                </template>
            </div>
            </template>
        </div>
    </div>

    {{-- Preview Modal --}}
    <div x-show="showPreview" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" x-on:click.self="showPreview = false" style="display:none">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden mx-4" x-on:click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Preview: {{ $project->title }}</h3>
                <button x-on:click="showPreview = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-4rem)]" x-html="previewHtml">
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function builder() {
            return {
                components: @json($project->getComponents()),
                selectedIndex: null,
                leftPanelOpen: true,
                showPreview: false,
                previewHtml: '',
                saving: false,
                saveTimer: null,
                availableSchemas: @json($availableComponents),

                init() {
                    // Nothing extra needed
                },

                addComponent(type) {
                    const schema = this.availableSchemas[type];
                    if (!schema) return;

                    const properties = {};
                    for (const [key, def] of Object.entries(schema.schema)) {
                        properties[key] = def.default ?? '';
                    }

                    this.components.push({
                        id: this.uid(),
                        type: type,
                        label: schema.label,
                        properties: properties,
                    });

                    this.selectedIndex = this.components.length - 1;
                    this.debounceSave();
                },

                removeComponent(index) {
                    this.components.splice(index, 1);
                    if (this.selectedIndex === index) {
                        this.selectedIndex = null;
                    } else if (this.selectedIndex > index) {
                        this.selectedIndex--;
                    }
                    this.debounceSave();
                },

                selectComponent(index) {
                    this.selectedIndex = this.selectedIndex === index ? null : index;
                },

                getComponentSchema(index) {
                    if (!this.components[index]) return {};
                    const schema = this.availableSchemas[this.components[index].type];
                    return schema ? schema.schema : {};
                },

                renderPreview(comp) {
                    const props = comp.properties;
                    switch (comp.type) {
                        case 'text':
                            return `<div style="color: ${props.color || '#1f2937'}" class="${props.fontSize || 'text-base'}">${props.content || 'Text'}</div>`;
                        case 'slider':
                            return `<div><div class="flex justify-between text-sm"><span>${props.label || 'Parameter'}</span><span class="font-bold text-blue-600">${props.defaultValue || 50}${props.unit || ''}</span></div><input type="range" min="${props.min || 0}" max="${props.max || 100}" value="${props.defaultValue || 50}" class="w-full mt-2" disabled></div>`;
                        case 'chart':
                            return `<div class="text-sm font-semibold mb-2">${props.title || 'Chart'}</div><div class="h-24 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center text-gray-400 text-xs">[${props.type || 'line'} chart preview]</div>`;
                        case 'image':
                            return props.imageUrl ? `<img src="${props.imageUrl}" alt="${props.alt || ''}" class="max-w-full rounded">` : `<div class="h-24 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center text-gray-400 text-xs">No image</div>`;
                        case 'quiz':
                            const opts = (props.options || 'A, B, C, D').split(',').map(s => s.trim());
                            return `<div class="font-medium mb-2">${props.question || 'Question?'}</div><div class="space-y-1">${opts.map((o, i) => `<div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><span class="w-6 h-6 rounded-full border flex items-center justify-center text-xs">${i}</span>${o}</div>`).join('')}</div>`;
                        default:
                            return `<div class="text-gray-400 text-sm">[${comp.type}]</div>`;
                    }
                },

                debounceSave() {
                    clearTimeout(this.saveTimer);
                    this.saveTimer = setTimeout(() => this.saveProject(false), 1500);
                },

                async saveProject(showMsg = true) {
                    this.saving = true;
                    try {
                        const resp = await fetch('{{ route("studio.builder.projects.update", $project->slug) }}', {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                config: { components: this.components },
                            }),
                        });
                        if (showMsg && resp.ok) {
                            // Simple visual feedback
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Saved!', type: 'success' } }));
                        }
                    } catch (e) {
                        console.error('Save failed', e);
                    }
                    this.saving = false;
                },

                async previewProject() {
                    try {
                        const resp = await fetch('{{ route("studio.builder.projects.preview", $project->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                config: { components: this.components },
                            }),
                        });
                        const data = await resp.json();
                        this.previewHtml = data.html || '<p class="text-gray-400">No preview available.</p>';
                        this.showPreview = true;
                    } catch (e) {
                        console.error('Preview failed', e);
                    }
                },

                uid() {
                    return Math.random().toString(36).substring(2, 10);
                },
            };
        }
    </script>
    @endpush
</div></x-studio-layout>
