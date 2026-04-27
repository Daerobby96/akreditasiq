<div class="px-4 py-8 mx-auto max-w-5xl">
    <!-- Header -->
    <header class="mb-12 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-4">
            Professional <span class="text-indigo-600">Export Engine</span>
        </h1>
        <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
            Generate dokumen akreditasi siap submit dalam format standar Microsoft Word (.docx).
        </p>
    </header>

    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
        <!-- LED Export Card -->
        <div class="p-10 glass-card bg-white dark:bg-slate-900 overflow-hidden relative group border-2 border-indigo-500/20">
            <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            
            <div class="relative z-10">
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-2">Dokumen LED</h3>
                <p class="text-slate-500 dark:text-slate-400 mb-8 text-sm leading-relaxed">
                    Eksport seluruh narasi kualitatif (Kriteria 1-9) ke dalam satu file Word yang sudah tertata rapi.
                </p>

                <button 
                    wire:click="downloadLed"
                    wire:loading.attr="disabled"
                    class="w-full py-4 bg-indigo-600 text-white font-black rounded-xl shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 transition-all flex items-center justify-center space-x-2 transform active:scale-95">
                    <span wire:loading.remove wire:target="downloadLed">UNDUH LED (.DOCX)</span>
                    <span wire:loading wire:target="downloadLed">GENERATING...</span>
                </button>
            </div>
        </div>

        <!-- LKPS Export Card -->
        <div class="p-10 glass-card bg-white dark:bg-slate-900 overflow-hidden relative group border-2 border-emerald-500/20">
            <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
            </div>
            
            <div class="relative z-10">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-2">Dokumen LKPS</h3>
                <p class="text-slate-500 dark:text-slate-400 mb-8 text-sm leading-relaxed">
                    Eksport seluruh data tabel kuantitatif ke dalam format Word dengan layout selaras standar LAM.
                </p>

                <button 
                    wire:click="downloadLkps"
                    wire:loading.attr="disabled"
                    class="w-full py-4 bg-emerald-600 text-white font-black rounded-xl shadow-lg shadow-emerald-500/30 hover:bg-emerald-700 transition-all flex items-center justify-center space-x-2 transform active:scale-95">
                    <span wire:loading.remove wire:target="downloadLkps">UNDUH LKPS (.DOCX)</span>
                    <span wire:loading wire:target="downloadLkps">GENERATING...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Notice -->
    <div class="mt-8 text-center">
        <p class="text-xs text-slate-400 font-medium italic">
            * Pastikan popup blocker Anda dinonaktifkan untuk mengunduh file secara otomatis.
        </p>
    </div>
</div>
