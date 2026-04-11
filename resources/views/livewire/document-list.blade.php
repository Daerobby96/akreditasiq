<div class="space-y-6">
    <div class="flex items-center justify-between mb-2">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Dokumen Terunggah & Penilaian AI</h3>
        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Real-time Analysis Active</span>
    </div>

    @forelse($dokumens as $dokumen)
        <div class="glass-card overflow-hidden group" x-data="{ expanded: false }">
            <div class="flex flex-col">
                <!-- Header / Info Bar -->
                <div class="p-4 sm:px-6 flex flex-wrap items-center justify-between gap-4 border-b border-slate-200/60 dark:border-slate-800/60 bg-white/40 dark:bg-slate-900/40">
                    <div class="flex items-center space-x-4 flex-1 min-w-[200px]">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold shadow-sm border border-indigo-100 dark:border-indigo-800/30">
                            {{ $dokumen->kriteria->kode }}
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center space-x-2">
                                <h4 class="text-base font-bold text-slate-900 dark:text-white whitespace-normal break-words">{{ $dokumen->nama_file }}</h4>
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" 
                                        class="flex items-center space-x-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 text-[10px] font-bold uppercase tracking-wider hover:bg-emerald-200 transition-colors">
                                        <span>{{ $dokumen->status }}</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" 
                                        class="absolute left-0 mt-2 w-32 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-100 dark:border-slate-700 z-50 overflow-hidden animate-fade-in-up">
                                        @foreach(['draft', 'submitted', 'review', 'approved', 'revision'] as $st)
                                            <button wire:click="updateStatus({{ $dokumen->id }}, '{{ $st }}')" @click="open = false"
                                                class="w-full text-left px-4 py-2 text-[10px] font-bold uppercase tracking-tight hover:bg-slate-50 dark:hover:bg-slate-700 {{ $dokumen->status === $st ? 'text-indigo-600 bg-indigo-50/50' : 'text-slate-600 dark:text-slate-400' }}">
                                                {{ $st }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-500">{{ $dokumen->created_at->diffForHumans() }} • v{{ $dokumen->versi }}</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        @if($dokumen->file_path === 'PENDING' || $dokumen->status === 'draft')
                            <div x-data="{ uploading: false }" 
                                x-on:livewire-upload-start="uploading = true" 
                                x-on:livewire-upload-finish="uploading = false" 
                                x-on:livewire-upload-error="uploading = false"
                                class="relative">
                                <input type="file" wire:model="tempFile" class="hidden" id="file-{{ $dokumen->id }}" x-on:change="$wire.finishDraft({{ $dokumen->id }})">
                                <label for="file-{{ $dokumen->id }}" 
                                    class="flex items-center space-x-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black rounded-xl cursor-pointer transition-all shadow-lg shadow-indigo-500/20 active:scale-95">
                                    <span x-show="!uploading" class="flex items-center space-x-2">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        <span>UNGGAH DOKUMEN</span>
                                    </span>
                                    <span x-show="uploading" class="flex items-center space-x-2">
                                        <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span>UPLOADING...</span>
                                    </span>
                                </label>
                            </div>
                        @endif

                        @if($dokumen->penilaian_ai->isNotEmpty())
                            @php $ai = $dokumen->penilaian_ai->first(); @endphp
                            <div class="flex items-center bg-slate-100 dark:bg-slate-800 rounded-full pl-3 pr-1 py-1 border border-slate-200 dark:border-slate-700 shadow-sm">
                                <span class="text-[10px] font-bold text-slate-500 mr-2 uppercase tracking-tight">AI Score</span>
                                <div class="w-8 h-8 rounded-full smart-gradient flex items-center justify-center text-white text-xs font-black shadow-lg shadow-indigo-500/20">
                                    {{ number_format($ai->skor, 1) }}
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-1">
                            <button wire:click="preview({{ $dokumen->id }})" class="p-2 text-slate-400 hover:text-indigo-500 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-md transition-all" title="Pratinjau AI">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                            <a href="{{ asset('storage/' . $dokumen->file_path) }}" target="_blank" class="p-2 text-slate-400 hover:text-emerald-500 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-md transition-all" title="Unduh">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </a>
                            <button wire:click="delete({{ $dokumen->id }})" wire:confirm="Hapus dokumen?" class="p-2 text-slate-400 hover:text-rose-500 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-md transition-all" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Analysis Content (Always Compact, Expandable) -->
                <div class="px-6 py-4 bg-slate-50/20 dark:bg-slate-900/5">
                    @if($dokumen->penilaian_ai->isNotEmpty())
                        @php $ai = $dokumen->penilaian_ai->first(); @endphp
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-1">
                                <h5 class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest mb-2 flex items-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mr-2"></span>
                                    Analisis Ringkas
                                </h5>
                                <p class="text-[13px] text-slate-600 dark:text-slate-400 leading-relaxed line-clamp-4" :class="expanded ? 'line-clamp-none' : 'line-clamp-4'">
                                    {{ $ai->analisis_teks }}
                                </p>
                                <button @click="expanded = !expanded" class="mt-2 text-[10px] font-bold text-indigo-500 hover:text-indigo-600 transition-colors uppercase tracking-tight">
                                    <span x-text="expanded ? 'Tampilkan Lebih Sedikit' : 'Lihat Analisis Lengkap'"></span>
                                </button>
                            </div>
                            
                            <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="p-4 bg-rose-50/30 dark:bg-rose-900/10 border border-rose-200/50 dark:border-rose-900/30 rounded-2xl relative overflow-hidden group/box">
                                    <h6 class="text-[10px] font-bold text-rose-500 uppercase mb-2 flex items-center">
                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        Gap Terdeteksi
                                    </h6>
                                    <p class="text-[12px] text-slate-600 dark:text-slate-400 leading-snug line-clamp-5" :class="expanded ? 'line-clamp-none' : 'line-clamp-5'">{{ $ai->gap_analysis }}</p>
                                </div>
                                <div class="p-4 bg-emerald-50/30 dark:bg-emerald-900/10 border border-emerald-200/50 dark:border-emerald-900/30 rounded-2xl relative overflow-hidden group/box">
                                    <h6 class="text-[10px] font-bold text-emerald-500 uppercase mb-2 flex items-center">
                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        Rekomendasi
                                    </h6>
                                    <p class="text-[12px] text-slate-600 dark:text-slate-400 leading-snug line-clamp-5" :class="expanded ? 'line-clamp-none' : 'line-clamp-5'">{{ $ai->rekomendasi }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Progress Bar Section when Analyzing -->
                        <div class="py-4">
                            @if(in_array($dokumen->id, $analyzingIds))
                                <div class="flex flex-col items-center space-y-4 max-w-md mx-auto">
                                    <div class="flex items-center justify-between w-full text-[10px] font-bold text-indigo-500 uppercase tracking-widest">
                                        <span class="flex items-center animate-pulse">
                                            <svg class="w-3 h-3 mr-2 animate-spin-fast" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            AI ANALYZING...
                                        </span>
                                        <span class="text-slate-400" x-text="Math.floor(Math.random() * 20) + 70 + '%'"></span>
                                    </div>
                                    <div class="h-1.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden border border-slate-200/50 dark:border-slate-800/50">
                                        <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 animate-progress-fill rounded-full" style="width: 10%"></div>
                                    </div>
                                    <p class="text-[10px] text-slate-400 italic">Menganalisis indikator pemenuhan standar {{ $dokumen->kriteria->lam_type }}...</p>
                                </div>
                            @else
                                <div class="flex items-center justify-center space-x-4 py-2 opacity-60 hover:opacity-100 transition-opacity">
                                    <p class="text-xs text-slate-400 italic font-medium">Belum ada evaluasi cerdas untuk dokumen ini.</p>
                                    <button wire:click="runAiAnalysis({{ $dokumen->id }})" class="text-[10px] px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-full font-bold transition-all border border-indigo-100 uppercase tracking-wider">
                                        Analisis Sekarang
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="flex flex-col items-center justify-center py-20 glass-card">
            <svg class="w-16 h-16 text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            <p class="text-slate-400 italic">Belum ada dokumen yang diunggah untuk kriteria akreditasi ini.</p>
        </div>
    @endforelse

    <!-- Quick Preview Modal -->
    @if($previewingUrl)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm animate-fade-in">
        <div class="bg-white dark:bg-slate-900 w-full max-w-6xl h-[90vh] rounded-3xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-950">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Smart Preview <span class="text-xs font-medium text-slate-500 ml-2">(PDF Viewer)</span></h3>
                <button wire:click="closePreview" class="p-2 hover:bg-rose-50 hover:text-rose-600 rounded-xl transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="flex-1 bg-slate-100 dark:bg-slate-950">
                <iframe src="{{ $previewingUrl }}" class="w-full h-full border-none"></iframe>
            </div>
        </div>
    </div>
    @endif
</div>
