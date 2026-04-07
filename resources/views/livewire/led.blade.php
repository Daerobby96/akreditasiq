<div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen">
    <link rel="stylesheet" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    <style>
        /* Global Justify for Professional Look */
        trix-editor, .prose-led, .trix-content {
            text-align: justify !important;
            text-justify: inter-word !important;
            hyphens: auto !important;
        }
        
        trix-editor p, .prose-led p, .trix-content p {
            margin-bottom: 1.25rem !important;
        }

        trix-editor { min-height: 20rem !important; border-radius: 1.5rem !important; border-color: #e2e8f0 !important; background: #fff !important; padding: 1.5rem !important; line-height: 1.8 !important; font-size: 0.95rem !important; }
        .dark trix-editor { background: #020617 !important; border-color: #1e293b !important; color: #cbd5e1 !important; }
        trix-toolbar .trix-button { background: #f8fafc !important; border-radius: 0.5rem !important; margin: 2px !important; }
        .dark trix-toolbar .trix-button { background: #1e293b !important; }
        
        .prose-led h2 { font-size: 1.25rem; font-weight: 800; color: #1e293b; margin-top: 2rem; margin-bottom: 1rem; border-left: 4px solid #4f46e5; padding-left: 1rem; text-align: left !important; }
        .prose-led h3 { font-size: 1.1rem; font-weight: 700; color: #4338ca; margin-top: 1.5rem; margin-bottom: 0.75rem; text-align: left !important; }
        .dark .prose-led h2, .dark .prose-led h3 { color: #f1f5f9; }
        
        .prose-led table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; border-radius: 0.75rem; overflow: hidden; }
        .prose-led th, .prose-led td { border: 1px solid #e2e8f0; padding: 0.75rem; text-align: left; }
        .dark .prose-led th, .dark .prose-led td { border-color: #1e293b; }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                Narasi <span class="text-transparent bg-clip-text smart-gradient">LED</span>
            </h1>
            <p class="mt-2 text-slate-500">Laporan Evaluasi Diri — Dokumen Kualitatif per Kriteria {{ strtoupper($lamType) }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Daftar Kriteria -->
            <div class="space-y-2">
                @foreach($kriterias as $k)
                <button wire:click="selectKriteria({{ $k->id }})"
                    class="w-full text-left p-4 rounded-2xl transition-all {{ $activeKriteria == $k->id ? 'smart-gradient text-white shadow-lg shadow-indigo-500/20' : 'glass-card hover:border-indigo-500/30' }}">
                    <div class="text-[10px] font-black uppercase tracking-widest {{ $activeKriteria == $k->id ? 'text-white/70' : 'text-slate-400' }}">{{ $k->kode }}</div>
                    <div class="text-sm font-bold leading-tight line-clamp-2 break-words {{ $activeKriteria == $k->id ? 'text-white' : 'text-slate-700 dark:text-slate-300' }}">{{ $k->nama }}</div>
                </button>
                @endforeach
            </div>

            <!-- Konten Narasi -->
            <div class="lg:col-span-3 space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Detail Analisis</h2>
                    @if(!$isEditing)
                    <button wire:click="edit" class="px-4 py-2 text-xs font-bold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20">PERBARUI NARASI</button>
                    @endif
                </div>

                @if($isEditing)
                <div class="glass-card p-8 space-y-6 animate-fade-in" x-data="{ 
                    update(field, value) { $wire.set('editData.' + field, value) } 
                }">
                    @foreach($sections as $key => $label)
                    <div wire:ignore wire:key="edit-{{ $activeKriteria }}-{{ $key }}">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">{{ $label }}</label>
                        <input id="{{ $key }}" type="hidden" value="{{ $editData[$key] ?? '' }}">
                        <trix-editor input="{{ $key }}" x-on:trix-change="update('{{ $key }}', $event.target.value)" class="trix-content"></trix-editor>
                    </div>
                    @endforeach

                    <div class="flex items-center space-x-3">
                        <button wire:click="save" class="px-6 py-2.5 text-sm font-bold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20">SIMPAN PERUBAHAN</button>
                        <button wire:click="cancelEdit" class="px-6 py-2.5 text-sm font-bold bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-all border border-slate-200">BATAL</button>
                        
                        <div class="h-8 w-px bg-slate-200 dark:bg-slate-800 mx-2"></div>
                        
                        <button @click="$wire.set('showAiPanel', !@js($showAiPanel))" 
                                class="flex items-center space-x-2 px-4 py-2.5 bg-gradient-to-tr from-purple-600 to-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all shadow-xl shadow-purple-500/20 active:scale-95">
                            <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <span>AI Writing Assistant</span>
                        </button>
                    </div>

                    <!-- AI Request Mini Panel -->
                    <div x-show="@entangle('showAiPanel')" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="bg-indigo-50 dark:bg-indigo-900/10 border-2 border-indigo-200 dark:border-indigo-800 rounded-3xl p-6 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10">
                            <svg class="w-20 h-20 text-indigo-500" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div class="relative">
                            <h4 class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-[0.2em] mb-4">Request AI Narrative Generation</h4>
                            <div class="space-y-4">
                                <textarea wire:model="aiPrompt" 
                                          placeholder="Contoh: 'Tuliskan narasi tentang keberlanjutan pendanaan prodi dengan fokus pada hibah penelitian nasional dan kerjasama industri...'"
                                          class="w-full bg-white dark:bg-slate-900 border-none rounded-2xl p-4 text-sm focus:ring-2 focus:ring-indigo-500/20 placeholder-slate-300 min-h-[100px] shadow-inner"></textarea>
                                
                                <div class="grid grid-cols-3 gap-3">
                                    @foreach($sections as $fKey => $fLabel)
                                    <button wire:click="generateAiNarrative('{{ $fKey }}')" wire:loading.attr="disabled" class="group flex flex-col items-center justify-center p-3 bg-white dark:bg-slate-800 rounded-2xl hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-all border border-slate-100 dark:border-slate-800 text-center disabled:opacity-50">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 group-hover:text-indigo-500">Draft Ke</span>
                                        <span class="text-[10px] font-bold text-slate-700 dark:text-slate-200">{{ $fLabel }}</span>
                                    </button>
                                    @endforeach
                                </div>

                                <div wire:loading wire:target="generateAiNarrative" class="w-full">
                                    <div class="flex items-center justify-center space-x-3 py-4">
                                        <div class="w-3 h-3 bg-indigo-500 rounded-full animate-bounce"></div>
                                        <div class="w-3 h-3 bg-purple-500 rounded-full animate-bounce [animation-delay:-.3s]"></div>
                                        <div class="w-3 h-3 bg-pink-500 rounded-full animate-bounce [animation-delay:-.5s]"></div>
                                        <span class="text-xs font-black text-indigo-500 uppercase tracking-widest ml-2">AI is thinking & drafting...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                    @php $data = $narasi[$activeKriteria] ?? null; @endphp

                    @if($data)
                        @foreach($sections as $key => $label)
                        <div class="glass-card p-8">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">{{ $label }}</h3>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-widest">Deskripsi analisis kuantitatif & kualitatif</p>
                                </div>
                            </div>
                            <div class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed pl-14 prose-led">
                                {!! $data[$key] ?? '<i class="text-slate-300">Belum ada narasi.</i>' !!}
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="glass-card p-16 text-center">
                            <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-slate-400 italic">Narasi LED untuk kriteria ini belum tersedia. Silakan buat narasi evaluasi diri.</p>
                            <button wire:click="edit" class="mt-4 px-6 py-2 text-sm font-bold bg-indigo-600 text-white rounded-xl">MULAI MENULIS</button>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
    <script>
        // Custom Trix Toolbar for Justify
        (function() {
            var justifyButtonHTML = '<button type="button" class="trix-button" data-trix-attribute="justify" title="Justify" tabindex="-1"><svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg></button>';
            
            document.addEventListener("trix-initialize", function(event) {
                var toolbarElement = event.target.toolbarElement;
                if (!toolbarElement.querySelector('[data-trix-attribute="justify"]')) {
                    // Find the group for alignment or block tools
                    var groupElement = toolbarElement.querySelector(".trix-button-group--block-tools");
                    if (groupElement) {
                        groupElement.insertAdjacentHTML("beforeend", justifyButtonHTML);
                    }
                }
            });

            // Register the attribute properly
            Trix.config.blockAttributes.justify = {
                tagName: "div",
                className: "text-justify",
                parse: false
            };
        })();

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('ai-content-generated', (event) => {
                const field = event.field;
                const content = event.content;
                
                const editorElement = document.querySelector(`trix-editor[input="${field}"]`);
                if (editorElement && editorElement.editor) {
                    editorElement.editor.loadHTML(content);
                }
            });
        });
    </script>
    <style>
        /* Essential Trix Styling for Justify & Lists */
        .trix-content div.text-justify, 
        .trix-content p.text-justify,
        .text-justify { 
            text-align: justify !important; 
            text-justify: inter-word !important;
        }

        /* Evidence Link Styling */
        .evidence-link {
            display: inline-flex;
            align-items: center;
            background: #eef2ff;
            color: #4f46e5;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid #c7d2fe;
            cursor: pointer;
            margin: 0 4px;
            transition: all 0.2s;
            text-decoration: none !important;
        }
        .evidence-link:hover {
            background: #4f46e5;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        .dark .evidence-link {
            background: #312e81/30;
            border-color: #4338ca/50;
            color: #818cf8;
        }
        
        trix-editor ol { list-style-type: decimal !important; padding-left: 1.5rem !important; margin-bottom: 1rem !important; }
        trix-editor ul { list-style-type: disc !important; padding-left: 1.5rem !important; margin-bottom: 1rem !important; }
        
        /* Ensure the numbering icon is visible if using default trix css */
        trix-toolbar .trix-button--icon-number-list::before {
            display: inline-block !important;
            opacity: 1 !important;
        }
    </style>
    
    <script>
        // Chart.js Renderer for AI-Generated Infographics
        function renderAiCharts() {
            document.querySelectorAll('.ai-chart').forEach(div => {
                // If already initialized, skip
                if (div.querySelector('canvas')) return;

                const canvas = document.createElement('canvas');
                div.appendChild(canvas);

                try {
                    // Trix/AI might encode quotes, so we clean them
                    const rawLabels = div.getAttribute('data-labels').replace(/&quot;/g, '"');
                    const rawValues = div.getAttribute('data-values').replace(/&quot;/g, '"');
                    
                    const labels = JSON.parse(rawLabels);
                    const values = JSON.parse(rawValues);
                    const type = div.getAttribute('data-type') || 'bar';
                    const title = div.getAttribute('data-label') || 'Data Visual';

                    new Chart(canvas, {
                        type: type,
                        data: {
                            labels: labels,
                            datasets: [{
                                label: title,
                                data: values,
                                backgroundColor: [
                                    'rgba(79, 70, 229, 0.2)',
                                    'rgba(147, 51, 234, 0.2)',
                                    'rgba(236, 72, 153, 0.2)',
                                    'rgba(59, 130, 246, 0.2)'
                                ],
                                borderColor: [
                                    'rgb(79, 70, 229)',
                                    'rgb(147, 51, 234)',
                                    'rgb(236, 72, 153)',
                                    'rgb(59, 130, 246)'
                                ],
                                borderWidth: 2,
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: { position: 'bottom', labels: { font: { weight: 'bold' } } }
                            },
                            scales: (type === 'bar' || type === 'line') ? {
                                y: { beginAtZero: true, grid: { display: false } }
                            } : {}
                        }
                    });
                } catch (e) {
                    console.error('Failed to render AI Chart:', e);
                }
            });
        }

        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('evidence-link')) {
                const docId = e.target.getAttribute('data-id');
                if (docId) {
                    window.open(`/documents/${docId}/view`, '_blank');
                }
            }
        });

        document.addEventListener('livewire:initialized', () => {
            renderAiCharts();

            Livewire.on('ai-content-generated', (event) => {
                const field = event.field;
                const content = event.content;
                
                const editorElement = document.querySelector(`trix-editor[input="${field}"]`);
                if (editorElement && editorElement.editor) {
                    editorElement.editor.loadHTML(content);
                    // Small delay to allow trix to render HTML
                    setTimeout(renderAiCharts, 300);
                }
            });
        });
    </script>
</div>
