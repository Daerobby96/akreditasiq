<div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                Folder <span class="text-transparent bg-clip-text smart-gradient">Data Dukung</span>
            </h1>
            <p class="mt-2 text-slate-500">Koleksi bukti fisik akreditasi untuk <b>{{ $prodi->nama }}</b>.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar: Navigation Kriteria -->
            <div class="lg:col-span-1 space-y-2">
                @foreach($kriterias as $k)
                <button wire:click="selectKriteria({{ $k->id }})" 
                    class="w-full text-left px-5 py-4 rounded-2xl transition-all border {{ $selectedKriteriaId == $k->id ? 'bg-indigo-600 border-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:border-indigo-500' }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-[10px] font-black uppercase {{ $selectedKriteriaId == $k->id ? 'text-indigo-200' : 'text-slate-400' }}">Standar {{ $k->kode }}</div>
                            <div class="text-sm font-bold leading-tight line-clamp-2 break-words">{{ $k->nama }}</div>
                        </div>
                        <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </button>
                @endforeach
            </div>

            <!-- Content Area -->
            <div class="lg:col-span-3 space-y-8">
                @if($selectedKriteriaId)
                    <!-- UI Components for Upload & List -->
                    <div class="animate-fade-in space-y-8">
                        <!-- List Progress & Documents -->
                        @livewire('document-list', ['kriteriaId' => $selectedKriteriaId, 'prodiId' => $prodi->id])
                        
                        <!-- Upload Section -->
                        @livewire('document-upload', ['kriteriaId' => $selectedKriteriaId, 'prodiId' => $prodi->id])
                    </div>
                @else
                    <div class="glass-card p-20 flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 bg-indigo-50 dark:bg-indigo-900/20 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Pilih Kriteria Akreditasi</h3>
                        <p class="text-slate-500 max-w-xs mx-auto text-sm">Silakan pilih salah satu kriteria standar di sebelah kiri untuk mengelola dokumen pendukungnya.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
