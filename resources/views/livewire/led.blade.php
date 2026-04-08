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
                @php 
                    $narasiModel = \App\Models\Narasi::where('prodi_id', $prodiId)->where('kriteria_id', $k->id)->first();
                    $status = $narasiModel->status ?? 'todo';
                    $lastAudit = $narasiModel->metadata['last_audit'] ?? null;
                    $statusColors = [
                        'todo' => 'bg-slate-300',
                        'in_progress' => 'bg-amber-400',
                        'review' => 'bg-indigo-400',
                        'done' => 'bg-emerald-500'
                    ];
                @endphp
                <button wire:click="selectKriteria({{ $k->id }})"
                    class="w-full text-left p-4 rounded-2xl transition-all relative overflow-hidden {{ $activeKriteria == $k->id ? 'smart-gradient text-white shadow-lg shadow-indigo-500/20' : 'glass-card hover:border-indigo-500/30' }}">
                    <div class="flex items-center justify-between">
                        <div class="text-[10px] font-black uppercase tracking-widest {{ $activeKriteria == $k->id ? 'text-white/70' : 'text-slate-400' }}">{{ $k->kode }}</div>
                        <div class="flex items-center space-x-2">
                            @if($lastAudit)
                            <div class="px-1.5 py-0.5 rounded bg-white/20 text-[9px] font-black">{{ number_format($lastAudit['predicted_score'], 1) }}</div>
                            @endif
                            <div class="w-2 h-2 rounded-full {{ $statusColors[$status] }} ring-2 ring-white/20"></div>
                        </div>
                    </div>
                    <div class="text-sm font-bold leading-tight line-clamp-2 break-words {{ $activeKriteria == $k->id ? 'text-white' : 'text-slate-700 dark:text-slate-300' }}">{{ $k->nama }}</div>
                </button>
                @endforeach
            </div>

            <!-- Konten Narasi -->
            <div class="lg:col-span-3 space-y-6">
                <div class="glass-card p-6 border-b-0 rounded-b-none mb-0">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <!-- Judul & Deskripsi -->
                        <div class="flex-shrink-0">
                            <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Narasi Kriteria</h2>
                            <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-1">Kolaborasi & Workflow Tim</p>
                        </div>

                        <!-- Grup Kontrol (Status, PJ, Aksi) -->
                        <div class="flex flex-wrap items-center gap-3 lg:justify-end">
                            <!-- Group 1: Workflow Meta -->
                            <div class="flex items-center bg-slate-100 dark:bg-slate-900/50 p-1 rounded-2xl border border-slate-200 dark:border-slate-800">
                                <!-- Status -->
                                <div class="flex items-center space-x-2 px-3 py-1.5 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
                                    <div class="w-2 h-2 rounded-full {{ ['todo' => 'bg-slate-400', 'in_progress' => 'bg-amber-400', 'review' => 'bg-indigo-400', 'done' => 'bg-emerald-500'][$currentNarasiModel->status ?? 'todo'] }}"></div>
                                    <select wire:change="updateStatus($event.target.value)" class="bg-transparent border-none text-[10px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-400 focus:ring-0 p-0">
                                        @foreach($statusOptions as $key => $label)
                                            <option value="{{ $key }}" {{ ($currentNarasiModel->status ?? 'todo') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- PJ -->
                                <div class="flex items-center space-x-2 px-3 py-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <select wire:change="updateAssignee($event.target.value)" class="bg-transparent border-none text-[10px] font-bold text-slate-600 dark:text-slate-400 focus:ring-0 p-0 max-w-[100px]">
                                        <option value="">Pilih PJ...</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ ($currentNarasiModel->assignee_id ?? null) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Group 2: Document Actions -->
                            @if(!$isEditing)
                                <div class="flex items-center space-x-2">
                                    <button @click="$wire.set('showPreview', true)" class="group flex items-center justify-center w-10 h-10 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-xl hover:bg-indigo-600 hover:text-white transition-all border border-indigo-100 dark:border-indigo-900/50" title="Pratinjau PDF">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                    <a href="{{ route('report.download-led') }}" target="_blank" class="group flex items-center justify-center w-10 h-10 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl hover:bg-slate-200 transition-all border border-slate-200 dark:border-slate-700" title="Download PDF">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </a>
                                </div>
                            @endif

                            <!-- Group 3: Process Actions -->
                            <div class="flex items-center space-x-2">
                                <button wire:click="checkConsistency" wire:loading.attr="disabled" class="group flex items-center space-x-2 px-4 py-2.5 bg-amber-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/20">
                                    <svg wire:loading.remove wire:target="checkConsistency" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    <svg wire:loading wire:target="checkConsistency" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    <span wire:loading.remove wire:target="checkConsistency">Cek Linieritas</span>
                                    <span wire:loading wire:target="checkConsistency">Checking...</span>
                                </button>

                                <button wire:click="auditCompliance" wire:loading.attr="disabled" class="group flex items-center space-x-2 px-4 py-2.5 bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20">
                                    <svg wire:loading.remove wire:target="auditCompliance" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <svg wire:loading wire:target="auditCompliance" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    <span wire:loading.remove wire:target="auditCompliance">AI Audit</span>
                                    <span wire:loading wire:target="auditCompliance">Auditing...</span>
                                </button>
                                
                                @if(!$isEditing)
                                    <button wire:click="edit" class="flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        <span>Edit Narasi</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($isEditing)
                <div class="glass-card p-8 space-y-6 animate-fade-in rounded-t-none border-t-0 mt-0" x-data="{ 
                    update(field, value) { $wire.set('editData.' + field, value) } 
                }">
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

                    @foreach($sections as $key => $label)
                    <div wire:ignore wire:key="edit-{{ $activeKriteria }}-{{ $key }}">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">{{ $label }}</label>
                        <input id="{{ $key }}" type="hidden" value="{{ $editData[$key] ?? '' }}">
                        <trix-editor input="{{ $key }}" x-on:trix-change="update('{{ $key }}', $event.target.value)" class="trix-content"></trix-editor>
                    </div>
                    @endforeach
                </div>
                @else
                    @php 
                        $data = $narasi[$activeKriteria] ?? null; 
                    @endphp

                        <!-- Consistency Results Panel -->
                        @if(isset($consistencyResults[$activeKriteria]))
                            @php $res = $consistencyResults[$activeKriteria]; @endphp
                            <div class="glass-card p-6 border-l-4 border-amber-500 bg-amber-50/30 dark:bg-amber-900/10">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-black uppercase tracking-widest text-slate-900 dark:text-white">Hasil Audit Linieritas (Data Consistency)</h4>
                                            <p class="text-[10px] text-slate-500">Skor Konsistensi: <span class="font-bold text-amber-600">{{ $res['consistency_score'] }}%</span></p>
                                        </div>
                                    </div>
                                    <button @click="$wire.set('consistencyResults.{{ $activeKriteria }}', null)" class="text-slate-400 hover:text-slate-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                @if(!empty($res['discrepancies']))
                                    <div class="space-y-3">
                                        @foreach($res['discrepancies'] as $disc)
                                            <div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-amber-200 dark:border-amber-900/50 shadow-sm transition-all hover:border-amber-400">
                                                <div class="flex items-start space-x-3">
                                                    <div class="mt-1 w-2 h-2 rounded-full {{ $disc['severity'] == 'high' ? 'bg-rose-500 animate-pulse' : 'bg-amber-400' }}"></div>
                                                    <div class="flex-1">
                                                        <div class="text-xs font-bold text-slate-700 dark:text-slate-300">Klaim Narasi: <span class="font-normal italic">"{{ $disc['claim'] }}"</span></div>
                                                        <div class="text-xs font-bold text-emerald-600 mt-1">Fakta LKPS: <span class="font-normal">{{ $disc['fact'] }}</span></div>
                                                        <div class="mt-2 p-2 bg-slate-50 dark:bg-slate-900/50 rounded-lg text-[10px] text-slate-500 border border-slate-100 dark:border-slate-800">
                                                            <b>Saran Perbaikan:</b> {{ $disc['fix_suggestion'] }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center p-8 bg-emerald-50/30 dark:bg-emerald-900/10 rounded-2xl border border-emerald-100 dark:border-emerald-900/30">
                                        <svg class="w-12 h-12 text-emerald-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p class="text-xs font-bold text-emerald-700">Luar Biasa! Tidak ditemukan ketidaksesuaian data.</p>
                                    </div>
                                @endif
                                
                                <div class="mt-4 pt-4 border-t border-amber-200/50 dark:border-amber-900/30">
                                    <p class="text-[9px] text-slate-400 italic">{{ $res['audit_summary'] }}</p>
                                </div>
                            </div>
                        @endif

                    @php 
                        $audit = $auditResults[$activeKriteria] ?? ($currentNarasiModel->metadata['last_audit'] ?? null);
                    @endphp

                    <!-- AI Compliance Review Panel (If Audit Results Exist) -->
                    @if($audit)
                    <div class="bg-white dark:bg-slate-900 border-2 border-emerald-500/30 rounded-[2.5rem] p-8 mb-8 relative overflow-hidden shadow-2xl">
                        <div class="absolute top-0 right-0 p-8 opacity-[0.03]">
                            <svg class="w-40 h-40 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        
                        <div class="relative flex flex-col md:flex-row gap-8 items-start">
                            <!-- Predicted Score Circle -->
                            <div class="shrink-0 flex flex-col items-center justify-center p-6 bg-emerald-50 dark:bg-emerald-950 rounded-3xl border border-emerald-100 dark:border-emerald-900 shadow-inner">
                                <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-2">Simulasi Skor</span>
                                <div class="text-5xl font-black text-emerald-600 tracking-tighter">{{ number_format($audit['predicted_score'], 1) }}</div>
                                <div class="mt-2 px-3 py-1 bg-emerald-500 text-white text-[9px] font-black rounded-full uppercase tracking-widest">{{ $audit['compliance_status'] }}</div>
                            </div>
                            
                            <div class="flex-1 space-y-6">
                                <div>
                                    <h3 class="text-xl font-black text-slate-800 dark:text-white">AI Compliance Review</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-2xl mt-2 italic">" {{ $audit['analytical_summary'] }} "</p>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-3">
                                        <h4 class="text-[10px] font-black text-rose-500 uppercase tracking-widest flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            Kesenjangan (Detection Gaps)
                                        </h4>
                                        <ul class="space-y-2">
                                            @foreach($audit['detected_gaps'] ?? [] as $gap)
                                            <li class="flex items-start text-xs text-slate-600 dark:text-slate-400">
                                                <span class="mr-2 text-rose-400">•</span> {{ $gap }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="space-y-3">
                                        <h4 class="text-[10px] font-black text-indigo-500 uppercase tracking-widest flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            Rekomendasi Strategis
                                        </h4>
                                        <ul class="space-y-2">
                                            @foreach($audit['recommendations'] ?? [] as $rec)
                                            <li class="flex items-start text-xs text-slate-600 dark:text-slate-400">
                                                <span class="mr-2 text-indigo-400">→</span> {{ $rec }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="space-y-6">
                        @if($data)
                            @foreach($sections as $key => $label)
                            <div class="glass-card p-10 {{ $loop->first ? 'rounded-t-none border-t-0 mt-0' : '' }}">
                                <div class="flex items-center mb-6">
                                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950 flex items-center justify-center mr-5 border border-indigo-100 dark:border-indigo-900">
                                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-slate-800 dark:text-white">{{ $label }}</h3>
                                        <p class="text-[10px] text-slate-400 uppercase tracking-[0.2em]">Narasi Evaluasi Dir & Analisis Kualitatif</p>
                                    </div>
                                </div>
                                <div class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed pl-1 prose-led">
                                    {!! $data[$key] ?? '<i class="text-slate-300">Belum ada narasi.</i>' !!}
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="glass-card p-16 text-center rounded-t-none border-t-0 mt-0">
                                <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-slate-400 italic font-medium">Narasi LED untuk kriteria ini belum tersedia.</p>
                                <button wire:click="edit" class="mt-6 px-8 py-3 text-xs font-black bg-indigo-600 text-white rounded-xl shadow-xl shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all">MULAI MENULIS SEKARANG</button>
                            </div>
                        @endif

                        <!-- Workflow History -->
                        <div class="glass-card p-8">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Riwayat Aktivitas & Workflow
                            </h4>
                            <div class="space-y-4">
                                @forelse($currentNarasiModel->workflows()->orderBy('created_at', 'desc')->take(5)->get() as $log)
                                <div class="flex items-start space-x-4 text-xs">
                                    <div class="mt-1 w-2 h-2 rounded-full bg-indigo-400"></div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $log->user->name ?? 'System' }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-slate-500 mt-0.5">{{ $log->comment }}</p>
                                    </div>
                                </div>
                                @empty
                                <p class="text-xs text-slate-300 italic">Belum ada riwayat aktivitas.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
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
    <!-- PDF Preview Modal -->
    <div x-data="{ isPreviewOpen: @entangle('showPreview') }"
         x-show="isPreviewOpen" 
         class="fixed inset-0 z-[60] overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center" 
         x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm" @click="isPreviewOpen = false"></div>

        <div x-show="isPreviewOpen"
             class="bg-white dark:bg-slate-900 rounded-[2rem] overflow-hidden shadow-2xl transform transition-all sm:w-full sm:max-w-5xl h-[90vh] flex flex-col relative"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <div class="p-6 bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Pratinjau Dokumen {{ ($lamType === 'lam-emba' ? 'DED' : 'LED') }}</h3>
                    <p class="text-xs text-slate-500">Hasil draf kualitatif untuk instrument akreditasi aktif.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('report.download-led') }}" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20">Download PDF</a>
                    <button @click="isPreviewOpen = false" class="p-2.5 bg-white dark:bg-slate-700 text-slate-400 hover:text-rose-500 rounded-xl transition-all border border-slate-200 dark:border-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
            
            <div class="flex-1 bg-slate-100 dark:bg-slate-950 p-4">
                <iframe src="{{ route('report.preview-led') }}" class="w-full h-full rounded-2xl border border-slate-200 dark:border-slate-800 shadow-inner" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>
