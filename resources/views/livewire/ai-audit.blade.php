<div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                Audit <span class="text-transparent bg-clip-text smart-gradient">AI & Scoring</span>
            </h1>
            <p class="mt-2 text-slate-500">Jalankan penilaian otomatis berbasis LLM untuk mengevaluasi kesiapan akreditasi.</p>
        </div>

        @if(!$analysisResult)
        <!-- Audit Panel -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Per Kriteria -->
            <div class="glass-card p-8">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">Audit Per Kriteria</h3>
                        <p class="text-xs text-slate-400">Analisis kritis untuk satu bagian instrumen</p>
                    </div>
                </div>

                <select wire:model="selectedKriteria" class="w-full px-4 py-3 bg-white/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-slate-900 dark:text-white mb-6">
                    <option value="">Pilih Kriteria...</option>
                    @foreach($kriterias as $k)
                        <option value="{{ $k->id }}">{{ $k->kode }}: {{ $k->nama }}</option>
                    @endforeach
                </select>

                <button wire:click="runAudit" wire:loading.attr="disabled" wire:target="runAudit"
                    class="w-full py-4 smart-gradient text-white font-extrabold rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transform hover:-translate-y-0.5 transition-all disabled:opacity-50 flex items-center justify-center">
                    <span wire:loading.remove wire:target="runAudit">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        MULAI AUDIT KRITERIA
                    </span>
                    <span wire:loading wire:target="runAudit" class="flex items-center">
                        <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        AI SEDANG MENGANALISIS...
                    </span>
                </button>
            </div>

            <!-- Full Audit -->
            <div class="glass-card p-8 smart-gradient relative overflow-hidden group">
                <div class="absolute inset-0 bg-white/5 group-hover:bg-white/10 transition-all pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6.012 6.012 0 01-.597.933l2.006.401a.5.5 0 01.255.137l1.713 1.713a.5.5 0 010 .708l-1.414 1.414a.5.5 0 01-.708 0l-1.713-1.713a.5.5 0 01-.137-.255l-.401-2.006a6.012 6.012 0 01-.933.597l.477 2.387a2 2 0 00.547 1.022l1.713 1.713a2 2 0 002.828 0l1.414-1.414a2 2 0 000-2.828l-1.713-1.713z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-extrabold text-white">Full System Audit</h3>
                            <p class="text-xs text-white/60">Simulasi penilaian seluruh instrumen</p>
                        </div>
                    </div>

                    <p class="text-white/80 text-sm leading-relaxed mb-8">
                        Jalankan audit komprehensif untuk mengevaluasi seluruh dokumen berdasarkan standar <b>{{ $lamLabel }}</b> secara bersamaan. AI akan memberikan prediksi skor akhir.
                    </p>

                    <button wire:click="runFullAudit" wire:loading.attr="disabled" wire:target="runFullAudit"
                        class="w-full py-4 bg-white text-indigo-600 font-extrabold rounded-2xl shadow-lg hover:bg-indigo-50 transition-all disabled:opacity-50 flex items-center justify-center">
                        <span wire:loading.remove wire:target="runFullAudit uppercase">SIMULASI AUDIT {{ strtoupper($prodi->lam_type) }}</span>
                        <span wire:loading wire:target="runFullAudit" class="flex items-center">
                            <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Menganalisis Instrumen...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        @else
        <!-- Results -->
        <div class="space-y-6">
            <button wire:click="resetResult" class="inline-flex items-center px-4 py-2 glass-card text-sm font-bold text-slate-600 hover:text-indigo-600 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Panel Audit
            </button>

            @if($analysisResult['status'] === 'empty')
                <div class="glass-card p-12 text-center">
                    <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <p class="text-lg font-bold text-slate-400">{{ $analysisResult['message'] }}</p>
                    <p class="text-sm text-slate-400 mt-2">Unggah dokumen terlebih dahulu melalui halaman Dashboard.</p>
                </div>

            @elseif($analysisResult['status'] === 'success')
                <!-- Single Criteria Result -->
                <div class="glass-card p-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $analysisResult['kriteria'] }}</h2>
                            <p class="text-sm text-slate-500">{{ $analysisResult['total_docs'] }} dokumen dianalisis</p>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-black text-transparent bg-clip-text smart-gradient">{{ $analysisResult['avg_score'] }}</div>
                            <div class="text-[10px] text-slate-400 uppercase font-bold">Skor Rata-rata</div>
                        </div>
                    </div>

                    @foreach($analysisResult['results'] as $i => $r)
                    <div class="mb-6 p-6 bg-slate-50/50 dark:bg-slate-900/20 rounded-2xl border border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between mb-4">
                            <span class="badge-smart bg-indigo-100 text-indigo-700">Dokumen #{{ $i + 1 }}</span>
                            <span class="text-2xl font-black text-indigo-600">{{ $r['skor'] }}</span>
                        </div>
                        <div class="space-y-3 text-sm">
                            <div><span class="font-bold text-slate-700 dark:text-slate-300">Analisis:</span> <span class="text-slate-500">{{ $r['analisis'] }}</span></div>
                            <div class="p-3 bg-rose-50 dark:bg-rose-900/10 rounded-xl border border-rose-100 dark:border-rose-900/20">
                                <span class="font-bold text-rose-600 text-xs uppercase">Gap:</span> <span class="text-slate-600 dark:text-slate-400 text-xs">{{ $r['gap'] }}</span>
                            </div>
                            <div class="p-3 bg-emerald-50 dark:bg-emerald-900/10 rounded-xl border border-emerald-100 dark:border-emerald-900/20">
                                <span class="font-bold text-emerald-600 text-xs uppercase">Rekomendasi:</span> <span class="text-slate-600 dark:text-slate-400 text-xs">{{ $r['rekomendasi'] }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            @elseif($analysisResult['status'] === 'full')
                <!-- Full Audit Summary -->
                <div class="glass-card p-8">
                    <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white mb-8 flex items-center">
                        <span class="w-2 h-8 smart-gradient rounded-full mr-3"></span>
                        Ringkasan Audit: {{ $lamLabel }}
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b-2 border-slate-200 dark:border-slate-800">
                                    <th class="pb-3 text-left text-xs font-bold text-slate-500 uppercase">Kode</th>
                                    <th class="pb-3 text-left text-xs font-bold text-slate-500 uppercase">Kriteria</th>
                                    <th class="pb-3 text-center text-xs font-bold text-slate-500 uppercase">Dokumen</th>
                                    <th class="pb-3 text-center text-xs font-bold text-slate-500 uppercase">Skor AI</th>
                                    <th class="pb-3 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($analysisResult['summary'] as $row)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-4">
                                        <span class="px-2.5 py-1 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 font-bold text-xs">{{ $row['kode'] }}</span>
                                    </td>
                                    <td class="py-4 font-bold text-slate-700 dark:text-slate-300">{{ $row['nama'] }}</td>
                                    <td class="py-4 text-center text-slate-600">{{ $row['docs'] }}</td>
                                    <td class="py-4 text-center font-black text-lg {{ $row['avg'] !== '-' ? 'text-transparent bg-clip-text smart-gradient' : 'text-slate-300' }}">{{ $row['avg'] }}</td>
                                    <td class="py-4 text-center">
                                        @if($row['avg'] !== '-')
                                            @if((float)$row['avg'] >= 3.5)
                                                <span class="badge-smart bg-emerald-100 text-emerald-700">Unggul</span>
                                            @elseif((float)$row['avg'] >= 3.0)
                                                <span class="badge-smart bg-indigo-100 text-indigo-700">Baik Sekali</span>
                                            @elseif((float)$row['avg'] >= 2.5)
                                                <span class="badge-smart bg-amber-100 text-amber-700">Baik</span>
                                            @else
                                                <span class="badge-smart bg-rose-100 text-rose-700">Perlu Perbaikan</span>
                                            @endif
                                        @else
                                            <span class="badge-smart bg-slate-100 text-slate-400">Belum Ada Data</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
        @endif
    </div>
</div>
