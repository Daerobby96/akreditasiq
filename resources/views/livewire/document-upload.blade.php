<div class="p-8 glass-card">
    <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6 flex items-center">
        <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        Unggah Dokumen Akreditasi
    </h2>

    @if($kriteriaId)
    <div class="mb-4 p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-200 dark:border-indigo-800">
        <p class="text-sm text-indigo-700 dark:text-indigo-300">
            <strong>Kriteria Terpilih:</strong> {{ optional(\App\Models\Kriteria::find($kriteriaId))->kode }} - {{ optional(\App\Models\Kriteria::find($kriteriaId))->nama }}
        </p>
    </div>
    @endif

    @if ($message)
        <div class="p-4 mb-6 text-sm text-emerald-700 bg-emerald-100/50 backdrop-blur rounded-xl border border-emerald-500/20 animate-bounce">
            {{ $message }}
        </div>
    @endif

    <form wire:submit.prevent="submitFile" class="space-y-6">
        @if(!$kriteriaId)
        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Kriteria Akreditasi</label>
            <select wire:model="kriteriaId" class="w-full px-4 py-3 bg-white/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-slate-900 dark:text-white">
                <option value="">Pilih Kriteria...</option>
                @foreach($kriterias as $kriteria)
                    <option value="{{ $kriteria->id }}">{{ $kriteria->kode }}: {{ $kriteria->nama }}</option>
                @endforeach
            </select>
            @error('kriteriaId') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
        </div>
        @endif

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nama Dokumen</label>
            <input type="text" wire:model="nama_file" placeholder="Contoh: Laporan Kinerja Program Studi" class="w-full px-4 py-3 bg-white/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-slate-900 dark:text-white">
            @error('nama_file') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <div 
            x-data="{ isDragging: false }" 
            x-on:dragover.prevent="isDragging = true" 
            x-on:dragleave.prevent="isDragging = false" 
            x-on:drop.prevent="isDragging = false"
            class="relative"
        >
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">File Dokumen (PDF, DOCX)</label>
            <div 
                :class="{ 'border-indigo-500 bg-indigo-50/10': isDragging }"
                class="flex flex-col items-center justify-center border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl p-10 transition-all hover:border-indigo-500/50 group overflow-hidden"
            >
                @if ($file)
                    <!-- UI when file is selected -->
                    <div class="flex flex-col items-center animate-fade-in text-center">
                        <div class="mb-4 p-4 rounded-3xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-500">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white line-clamp-1 max-w-[300px]">
                            {{ $file->getClientOriginalName() }}
                        </p>
                        <p class="text-[10px] text-slate-400 mt-1 uppercase font-black">
                            {{ number_format($file->getSize() / 1024, 0) }} KB — SIAP DIUNGGAH
                        </p>
                        <button type="button" wire:click="$set('file', null)" class="mt-4 px-3 py-1 text-[10px] font-bold bg-rose-50 text-rose-500 rounded-lg hover:bg-rose-100 transition-all">GANTI FILE</button>
                    </div>
                @else
                    <!-- UI when no file is selected -->
                    <div class="mb-4 p-4 rounded-full bg-slate-50 dark:bg-slate-900 group-hover:scale-110 group-hover:bg-indigo-100/20 transition-all text-slate-400 group-hover:text-indigo-500">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Seret file ke sini atau klik untuk mengunggah</p>
                    <p class="text-xs text-slate-400">PDF, DOCX, XLSX (Maks. 10MB)</p>
                @endif

                <input type="file" wire:model="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
            </div>
            @error('file') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            @error('kriteriaId') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            
            <div wire:loading wire:target="file" class="mt-4 text-xs text-indigo-500 font-bold animate-pulse">
                Proses mengunggah file ke server...
            </div>
        </div>

        <button 
            type="submit" 
            wire:loading.attr="disabled"
            class="w-full py-4 smart-gradient text-white font-extrabold rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transform hover:-translate-y-1 transition-all disabled:opacity-50 flex items-center justify-center space-x-2"
        >
            <span wire:loading.remove wire:target="submitFile">UNGGAH DOKUMEN</span>
            <span wire:loading wire:target="submitFile" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                SEDANG MENGUNGGAH...
            </span>
        </button>
    </form>
</div>
