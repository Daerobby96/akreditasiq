<div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-12">
        <!-- Header & LAM Switcher -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <div>
                <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                    Data Kuantitatif <span class="text-transparent bg-clip-text smart-gradient">LKPS</span> 
                </h1>
                <p class="mt-2 text-slate-500 font-medium">Laporan Kinerja Program Studi yang dinamis sesuai standar LAM.</p>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-10">
            <!-- Sidebar Navigation (Tabs) -->
            <div class="w-full lg:w-[380px] flex-shrink-0">
                <div class="sticky top-28 space-y-2">
                    <div class="bg-indigo-600/5 dark:bg-indigo-900/10 p-6 rounded-[2rem] mb-6 border border-indigo-100/50 dark:border-indigo-800/30">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest leading-none">Status Kriteria</p>
                            <span class="px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 text-[8px] font-black rounded-lg uppercase">Active</span>
                        </div>
                        <h2 class="text-xl font-black text-slate-900 dark:text-white leading-tight">Instrumen {{ strtoupper($selectedLam) }}</h2>
                    </div>

                    <div class="space-y-4">
                        @foreach($groupedTables as $criterion => $tables)
                            @if($tables->isNotEmpty())
                                <div x-data="{ open: {{ collect($tables)->pluck('slug')->contains($activeTab) ? 'true' : 'false' }} }">
                                    <button @click="open = !open" 
                                        class="w-full flex items-center justify-between p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm hover:border-indigo-300 transition-all">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center">
                                                <span class="text-[10px] font-black text-indigo-600">{{ substr($criterion, 0, 2) }}</span>
                                            </div>
                                            <span class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-tight">{{ $criterion }}</span>
                                        </div>
                                        <svg class="w-4 h-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>

                                    <div x-show="open" x-collapse class="mt-2 space-y-1 ml-4 border-l-2 border-slate-100 dark:border-slate-800 pl-4 transition-all">
                                        @foreach($tables as $table)
                                            <button wire:click="setTab('{{ $table->slug }}')" 
                                                class="w-full text-left py-2 px-3 rounded-xl transition-all text-[11px] font-bold flex items-center space-x-2
                                                {{ $activeTab === $table->slug 
                                                    ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' 
                                                    : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                                                <div class="w-1.5 h-1.5 rounded-full {{ $activeTab === $table->slug ? 'bg-white' : 'bg-slate-300' }}"></div>
                                                <span class="line-clamp-1">{{ $table->label }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Content Area (Table) -->
            <div class="flex-1 space-y-6">
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="p-8 sm:p-10">
                        @if($currentTable)
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="px-3 py-1 bg-indigo-600 text-white text-[10px] font-black rounded-full uppercase tracking-widest">
                                            Langkah {{ ($currentIndex ?? 0) + 1 }} dari {{ $totalTables ?? 0 }}
                                        </span>
                                        <div class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden max-w-[200px]">
                                            <div class="h-full bg-indigo-600 transition-all duration-500" style="width: {{ (($currentIndex ?? 0) + 1) / ($totalTables ?? 1) * 100 }}%"></div>
                                        </div>
                                    </div>
                                    <h3 class="text-3xl font-black text-slate-900 dark:text-white leading-tight">{{ $currentTable->label }}</h3>
                                    <p class="text-sm text-slate-400 font-medium mt-1 italic">{{ $currentTable->description ?? 'Lengkapi data kuantitatif di bawah ini sesuai instrumen.' }}</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <button @click="$wire.set('showPreview', true)" class="px-5 py-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all flex items-center hover:bg-indigo-600 hover:text-white border border-slate-200 dark:border-slate-800">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Pratinjau PDF
                                    </button>
                                    <button wire:click="downloadTemplate" class="px-4 py-3 mr-2 bg-slate-100 hover:bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-300 text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all flex items-center shadow-lg shadow-slate-500/10">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Template
                                    </button>

                                    <button onclick="document.getElementById('excel_input').click()" class="px-6 py-3 mr-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all flex items-center shadow-xl shadow-emerald-500/20">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Import Excel
                                    </button>
                                    <input type="file" id="excel_input" wire:model="excelFile" class="hidden" onchange="@this.importExcel()" accept=".xlsx,.xls,.csv">

                                    <div wire:loading wire:target="excelFile, importExcel" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[9999] flex items-center justify-center">
                                        <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-2xl flex flex-col items-center">
                                            <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                                            <p class="text-indigo-600 dark:text-indigo-400 font-black uppercase tracking-widest animate-pulse">Sedang Mengimpor Data...</p>
                                        </div>
                                    </div>

                                    <button wire:click="addRow" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all flex items-center shadow-xl shadow-indigo-500/20">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Tambah Baris
                                    </button>
                                </div>
                            </div>

                            <div class="p-0">
                                <div class="overflow-x-auto scrollbar-fancy pb-6">
                                    <table class="w-full min-w-[1000px] text-left border-separate border-spacing-0">
                                        <thead>
                                            @php
                                                $allCols = $currentTable->columns;
                                                // Pre-calculate depth for each column
                                                $maxDepth = 0;
                                                foreach($allCols as $col) {
                                                    $d = 1;
                                                    $temp = $col;
                                                    while($temp && $temp->parent_id) { 
                                                        $d++; 
                                                        $temp = $allCols->where('id', $temp->parent_id)->first(); 
                                                    }
                                                    $col->depth = $d;
                                                    $maxDepth = max($maxDepth, $d);
                                                }
                                            @endphp

                                            @for($step = 1; $step <= $maxDepth; $step++)
                                                <tr class="{{ $step === 1 ? 'bg-indigo-600 dark:bg-indigo-800' : ($step === 2 ? 'bg-indigo-500 dark:bg-indigo-900/80' : 'bg-indigo-400 dark:bg-indigo-950/50') }}">
                                                    @foreach($allCols->where('depth', $step)->sortBy('sort_order') as $column)
                                                        @php
                                                            $childrenCnt = $allCols->where('parent_id', $column->id)->count();
                                                            $rowspan = ($childrenCnt === 0) ? ($maxDepth - $step + 1) : 1;
                                                            $colspan = $column->colspan;
                                                        @endphp
                                                        <th rowspan="{{ $rowspan }}" 
                                                            colspan="{{ $colspan }}"
                                                            class="py-4 px-3 text-[10px] font-black uppercase tracking-widest text-center text-white border border-white/10">
                                                            {{ $column->label ?: $column->header_name }}
                                                        </th>
                                                    @endforeach
                                                    @if($step === 1)
                                                        <th rowspan="{{ $maxDepth }}" class="py-4 px-3 bg-indigo-700/50 dark:bg-indigo-900/50 border border-white/10 text-white text-[10px] font-black uppercase tracking-widest text-center">
                                                            Aksi
                                                        </th>
                                                    @endif
                                                </tr>
                                            @endfor
                                        </thead>
                                        <tbody class="divide-y divide-transparent">
                                            @forelse($tableData as $row)
                                                @php 
                                                    $idx = $row->id; 
                                                    // Detect if this is a total/jumlah row
                                                    $isTotalRow = false;
                                                    foreach($row->data_values as $val) {
                                                        if (is_string($val) && (stripos($val, 'jumlah') !== false || stripos($val, 'total') !== false)) {
                                                            $isTotalRow = true; break;
                                                        }
                                                    }
                                                @endphp
                                                <tr wire:key="row-{{ $idx }}" 
                                                    class="group hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-all border-b-[12px] border-transparent {{ $isTotalRow ? 'font-black bg-slate-100/50 dark:bg-slate-800/40 text-indigo-900 dark:text-indigo-300' : 'text-slate-700 dark:text-slate-300' }}">
                                                    @foreach($leafColumns as $column)
                                                        @php $val = $row->data_values[$column->field_name] ?? ''; @endphp
                                                        <td wire:key="cell-{{ $idx }}-{{ $column->id }}" class="py-4 px-3 text-sm {{ $editingId === $idx ? 'bg-indigo-50/30' : 'bg-white dark:bg-slate-900' }} border border-slate-100 dark:border-slate-800 {{ $loop->first ? 'pl-6' : '' }}">
                                                            @if($editingId === $idx)
                                                                @if($currentTable->slug === 'tabel_1_a_4_ewmp' && $column->field_name === 'total')
                                                                    @php
                                                                        $rowTotal = ((float)($editBuffer['sks_ajar_ps'] ?? 0) + (float)($editBuffer['sks_ajar_luar_ps'] ?? 0) + (float)($editBuffer['sks_ajar_luar_pt'] ?? 0) + (float)($editBuffer['sks_riset'] ?? 0) + (float)($editBuffer['sks_pkm'] ?? 0) + (float)($editBuffer['sks_man_pt'] ?? 0) + (float)($editBuffer['sks_man_luar_pt'] ?? 0)) / 2;
                                                                    @endphp
                                                                    <div class="px-3 py-2 bg-slate-100 dark:bg-slate-800 rounded-xl text-indigo-600 font-black">{{ str_replace(',00', '', number_format($rowTotal, 2, ',', '.')) }}</div>
                                                                @elseif(in_array($currentTable->slug, ['tabel_4_e_publikasi', 'tabel_6_e_1_publikasi', 'tabel_6_e_2_pagelaran']) && $column->field_name === 'total')
                                                                    @php
                                                                        $rowTotalPub = (float)($editBuffer['ts2'] ?? 0) + (float)($editBuffer['ts1'] ?? 0) + (float)($editBuffer['ts'] ?? 0);
                                                                    @endphp
                                                                    <div class="px-3 py-2 bg-slate-100 dark:bg-slate-800 rounded-xl text-indigo-600 font-black">
                                                                        {{ str_replace(',00', '', number_format($rowTotalPub, 2, ',', '.')) }}
                                                                    </div>
                                                                @elseif($currentTable->slug === 'tabel_4_c_bk' && in_array($column->field_name, ['total_thn', 'total_sem']))
                                                                    @php
                                                                        $rowTotalYear = (float)($editBuffer['ps_akre'] ?? 0) + (float)($editBuffer['ps_lain_pt'] ?? 0) + (float)($editBuffer['ps_luar_pt'] ?? 0) + (float)($editBuffer['pen'] ?? 0) + (float)($editBuffer['pkm'] ?? 0) + (float)($editBuffer['tugas'] ?? 0);
                                                                    @endphp
                                                                    <div class="px-3 py-2 bg-slate-100 dark:bg-slate-800 rounded-xl text-indigo-600 font-black">
                                                                        {{ $column->field_name === 'total_thn' ? str_replace(',00', '', number_format($rowTotalYear, 2, ',', '.')) : str_replace(',00', '', number_format($rowTotalYear / 2, 2, ',', '.')) }}
                                                                    </div>
                                                                @elseif($column->data_type === 'boolean' || (str_starts_with($column->field_name, 'pl') && in_array($currentTable->slug, ['tabel_2_b_1_pembelajaran', 'tabel_2_b_2_pemetaan_cpl'])) || (in_array($column->field_name, ['ts', 'ts1', 'ts2']) && in_array($currentTable->slug, ['tabel_3_c_2_publikasi_riset', 'tabel_3_c_3_hki_riset', 'tabel_4_c_3_hki_pkm'])))
                                                                    <div class="flex justify-center">
                                                                        <input type="checkbox" 
                                                                               wire:model="editBuffer.{{ $column->field_name }}" 
                                                                               class="w-6 h-6 rounded-lg border-2 border-indigo-200 dark:border-indigo-800 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                                                                    </div>
                                                                @else
                                                                    <input type="text" 
                                                                           wire:model="editBuffer.{{ $column->field_name }}" 
                                                                           class="w-full px-3 py-2 bg-white dark:bg-slate-950 border-2 border-indigo-200 dark:border-indigo-800 rounded-xl text-sm font-bold text-slate-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                                                                @endif
                                                            @else
                                                                @if($currentTable->slug === 'tabel_1_a_4_ewmp' && $column->field_name === 'total')
                                                                    @php
                                                                        $val = ((float)($row->data_values['sks_ajar_ps'] ?? 0) + (float)($row->data_values['sks_ajar_luar_ps'] ?? 0) + (float)($row->data_values['sks_ajar_luar_pt'] ?? 0) + (float)($row->data_values['sks_riset'] ?? 0) + (float)($row->data_values['sks_pkm'] ?? 0) + (float)($row->data_values['sks_man_pt'] ?? 0) + (float)($row->data_values['sks_man_luar_pt'] ?? 0)) / 2;
                                                                    @endphp
                                                                    <span class="text-indigo-600 font-black">{{ str_replace(',00', '', number_format($val, 2, ',', '.')) }}</span>
                                                                @elseif(($val && $val != '0') && ($column->data_type === 'boolean' || (str_starts_with($column->field_name, 'pl') && in_array($currentTable->slug, ['tabel_2_b_1_pembelajaran', 'tabel_2_b_2_pemetaan_cpl'])) || (in_array($column->field_name, ['ts', 'ts1', 'ts2']) && in_array($currentTable->slug, ['tabel_3_c_2_publikasi_riset', 'tabel_3_c_3_hki_riset', 'tabel_4_c_3_hki_pkm']))))
                                                                    <div class="flex justify-center">
                                                                        <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                        </div>
                                                                    </div>
                                                                @elseif($column->data_type === 'currency')
                                                                    @php $cleanVal = (float)$val; @endphp
                                                                    @if($cleanVal != 0)
                                                                        {{ number_format($cleanVal, 0, ',', '.') }}
                                                                    @endif
                                                                @elseif($column->data_type === 'number')
                                                                    {{ is_numeric($val) && $val !== '' ? str_replace(',00', '', number_format((float)$val, 2, ',', '.')) : $val }}
                                                                @else
                                                                    {{ $val }}
                                                                @endif
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                    <td class="py-4 px-6 text-right {{ $editingId === $idx ? 'bg-emerald-50/30' : 'bg-white dark:bg-slate-900' }} border border-slate-100 dark:border-slate-800">
                                                        @if($editingId === $idx)
                                                            <div class="flex items-center justify-end space-x-2">
                                                                <button wire:click="saveEntry" class="p-2.5 bg-emerald-500 text-white rounded-xl shadow-lg shadow-emerald-500/20 hover:scale-110 active:scale-95 transition-all" title="Simpan">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                                </button>
                                                                <button wire:click="cancelEdit" class="p-2.5 bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl hover:scale-110 active:scale-95 transition-all" title="Batal">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                </button>
                                                            </div>
                                                        @else
                                                            <div class="flex items-center justify-end space-x-2">
                                                                <button wire:click="editEntry({{ $idx }})" class="p-2.5 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all shadow-sm border border-slate-100 dark:border-slate-800" title="Edit Baris">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                                </button>
                                                                <button wire:click="deleteRow({{ $idx }})" wire:confirm="Hapus baris data ini?" class="p-2.5 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition-all shadow-sm border border-slate-100 dark:border-slate-800" title="Hapus Baris">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="{{ $leafColumns->count() + 1 }}" class="py-24 text-center">
                                                        <div class="flex flex-col items-center justify-center space-y-4 opacity-30">
                                                            <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center">
                                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                                                            </div>
                                                            <span class="text-xs font-black uppercase tracking-[0.3em]">Belum Ada Data</span>
                                                            <button wire:click="addRow" class="mt-4 px-6 py-3 bg-white dark:bg-slate-900 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:border-indigo-400 hover:text-indigo-500 transition-all">Mulai Input Data Pertama</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse

                                            @if($tableData->isNotEmpty() && $currentTable->slug !== 'tabel_2_a_3_kondisi_mhs')
                                                @php
                                                    $sumableTypes = ['number', 'currency', 'boolean'];
                                                    $skipFields = ['no', 'tahun', 'semester', 'periode', 'smt'];
                                                    $labelFields = ['jenis', 'sumber', 'penggunaan', 'keterangan', 'urutan', 'unit', 'unit_kerja', 'kategori', 'nama', 'nama_pendukung', 'nama_capstone', 'tahun'];
                                                    
                                                    $hasSumable = $leafColumns->some(fn($c) => in_array($c->data_type, $sumableTypes) && !in_array($c->field_name, $skipFields));
                                                    $totalLabelShown = false;
                                                @endphp

                                                @if($hasSumable)
                                                    <!-- Row Total/Jumlah -->
                                                    <tr class="bg-slate-100/80 dark:bg-slate-800/80 border-t-2 border-slate-200 dark:border-slate-700 font-black">
                                                        @foreach($leafColumns as $column)
                                                            <td class="py-4 px-3 text-sm text-slate-900 dark:text-white">
                                                                @if(in_array($column->field_name, $labelFields) && !$totalLabelShown)
                                                                    <span class="text-indigo-600 dark:text-indigo-400">
                                                                        {{ $currentTable->slug === 'tabel_1_a_4_ewmp' ? 'Jumlah *' : 'Total' }}
                                                                    </span>
                                                                    @php $totalLabelShown = true; @endphp
                                                                @elseif(in_array($column->data_type, $sumableTypes) && !in_array($column->field_name, $skipFields))
                                                                    @php
                                                                        $sum = $tableData->sum(fn($row) => 
                                                                            ($currentTable->slug === 'tabel_1_a_4_ewmp' && $column->field_name === 'total')
                                                                            ? (((float)($row->data_values['sks_ajar_ps'] ?? 0) + (float)($row->data_values['sks_ajar_luar_ps'] ?? 0) + (float)($row->data_values['sks_ajar_luar_pt'] ?? 0) + (float)($row->data_values['sks_riset'] ?? 0) + (float)($row->data_values['sks_pkm'] ?? 0) + (float)($row->data_values['sks_man_pt'] ?? 0) + (float)($row->data_values['sks_man_luar_pt'] ?? 0)) / 2)
                                                                            : (float)($row->data_values[$column->field_name] ?? 0)
                                                                        );
                                                                    @endphp
                                                                    @if($column->data_type === 'currency')
                                                                        {{ number_format($sum, 0, ',', '.') }}
                                                                    @else
                                                                        {{ str_replace(',00', '', number_format($sum, 2, ',', '.')) }}
                                                                    @endif
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                        <td class="rounded-br-3xl"></td>
                                                    </tr>

                                                    <!-- Row Rata-rata (Specific for EWMP) -->
                                                    @if($currentTable->slug === 'tabel_1_a_4_ewmp')
                                                        @php $totalLabelShown = false; @endphp
                                                        <tr class="bg-indigo-50/30 dark:bg-indigo-900/10 border-t border-indigo-100 dark:border-indigo-800 font-black italic">
                                                            @foreach($leafColumns as $column)
                                                                <td class="py-4 px-3 text-sm text-indigo-700 dark:text-indigo-300">
                                                                    @if(in_array($column->field_name, $labelFields) && !$totalLabelShown)
                                                                        <span class="">Rata-rata **</span>
                                                                        @php $totalLabelShown = true; @endphp
                                                                    @elseif(in_array($column->data_type, $sumableTypes) && !in_array($column->field_name, $skipFields))
                                                                        @php
                                                                            $avg = $tableData->avg(fn($row) => 
                                                                                ($column->field_name === 'total')
                                                                                ? (((float)($row->data_values['sks_ajar_ps'] ?? 0) + (float)($row->data_values['sks_ajar_luar_ps'] ?? 0) + (float)($row->data_values['sks_ajar_luar_pt'] ?? 0) + (float)($row->data_values['sks_riset'] ?? 0) + (float)($row->data_values['sks_pkm'] ?? 0) + (float)($row->data_values['sks_man_pt'] ?? 0) + (float)($row->data_values['sks_man_luar_pt'] ?? 0)) / 2)
                                                                                : (float)($row->data_values[$column->field_name] ?? 0)
                                                                            );
                                                                        @endphp
                                                                        {{ str_replace(',00', '', number_format($avg, 2, ',', '.')) }}
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                            <td></td>
                                                        </tr>
                                                    @endif
                                                    @if($currentTable->slug === 'tabel_4_a_dosen')
                                                        @php
                                                            $ndt = $tableData->count(); 
                                                            $ndtpis = $tableData->filter(fn($r) => ($r->data_values['fit'] ?? false) == true)->count();
                                                        @endphp
                                                        <!-- Custom Footer for Table 4.a -->
                                                        <tr class="bg-slate-100/80 dark:bg-slate-800/80 border-t-4 border-white dark:border-slate-900 font-black text-xs uppercase tracking-wider">
                                                            <td colspan="2" class="py-3 px-3 text-right">NDT =</td>
                                                            <td class="bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 px-3">{{ $ndt }}</td>
                                                            <td colspan="4"></td>
                                                            <td class="text-right">NDTPS =</td>
                                                            <td class="bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 px-3">{{ $ndtpis }}</td>
                                                            <td colspan="9"></td>
                                                        </tr>
                                                        <tr class="bg-slate-50/50 dark:bg-slate-800/40 font-bold text-[10px] italic">
                                                            <td colspan="2" class="py-2 px-3 text-right text-slate-400">NDTT =</td>
                                                            <td class="px-3 text-slate-400 italic">Isi di Tabel 4.b</td>
                                                            <td colspan="15"></td>
                                                        </tr>
                                                    @elseif($currentTable->slug === 'tabel_4_c_bk')
                                                        @php
                                                            $allTotals = $tableData->map(fn($r) => 
                                                                (float)($r->data_values['ps_akre'] ?? 0) + (float)($r->data_values['ps_lain_pt'] ?? 0) + (float)($r->data_values['ps_luar_pt'] ?? 0) + (float)($r->data_values['pen'] ?? 0) + (float)($r->data_values['pkm'] ?? 0) + (float)($r->data_values['tugas'] ?? 0)
                                                            );
                                                            $avgDT = $allTotals->isEmpty() ? 0 : $allTotals->avg() / 2;
                                                            $dtpsTotals = $tableData->filter(fn($r) => ($r->data_values['dtps'] ?? false) == true)->map(fn($r) => 
                                                                (float)($r->data_values['ps_akre'] ?? 0) + (float)($r->data_values['ps_lain_pt'] ?? 0) + (float)($r->data_values['ps_luar_pt'] ?? 0) + (float)($r->data_values['pen'] ?? 0) + (float)($r->data_values['pkm'] ?? 0) + (float)($r->data_values['tugas'] ?? 0)
                                                            );
                                                            $avgDTPS = $dtpsTotals->isEmpty() ? 0 : $dtpsTotals->avg() / 2;
                                                        @endphp
                                                        <!-- Custom Footer for Table 4.c -->
                                                        <tr class="bg-slate-100/50 dark:bg-slate-900/40 border-t-2 border-slate-200 dark:border-slate-800 font-bold italic">
                                                            <td colspan="9" class="py-3 px-3 text-right">Rata-rata DT</td>
                                                            <td></td>
                                                            <td class="bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 px-3">{{ str_replace(',00', '', number_format($avgDT, 2, ',', '.')) }}</td>
                                                        </tr>
                                                        <tr class="bg-slate-100/50 dark:bg-slate-900/40 font-bold italic">
                                                            <td colspan="9" class="py-3 px-3 text-right">Rata-rata DTPS</td>
                                                            <td></td>
                                                            <td class="bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 px-3">{{ str_replace(',00', '', number_format($avgDTPS, 2, ',', '.')) }}</td>
                                                        </tr>
                                                    @endif
                                                @endif
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Floating Navigation Bar (Step-by-Step) -->
                            <div class="sticky bottom-8 left-0 right-0 mt-12 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200 dark:border-slate-800 p-4 rounded-[2rem] shadow-2xl flex items-center justify-between">
                                <button wire:click="previousTable" {{ $currentIndex === 0 ? 'disabled' : '' }} class="flex items-center px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all {{ $currentIndex === 0 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    Kembali
                                </button>
                                
                                <div class="hidden sm:flex flex-col items-center">
                                    <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Progress Instrumen</div>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-32 h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-indigo-600" style="width: {{ (($currentIndex ?? 0) + 1) / ($totalTables ?? 1) * 100 }}%"></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-slate-600">{{ round((($currentIndex ?? 0) + 1) / ($totalTables ?? 1) * 100) }}%</span>
                                    </div>
                                </div>

                                <button wire:click="nextTable" {{ $currentIndex === ($totalTables - 1) ? 'disabled' : '' }} class="flex items-center px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-indigo-500/20 active:scale-95">
                                    {{ $currentIndex === ($totalTables - 1) ? 'Selesai' : 'Lanjut ke Tabel Berikutnya' }}
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                        @else
                            <div class="py-40 text-center flex flex-col items-center">
                                <div class="w-24 h-24 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-6">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                </div>
                                <h3 class="text-xl font-bold text-slate-400">Tabel Belum Terdata</h3>
                                <p class="text-sm text-slate-400 mt-2 max-w-xs">Konfigurasi tabel untuk standar {{ strtoupper($selectedLam) }} belum tersedia di database.</p>
                            </div>
                        @endif
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
             class="bg-white dark:bg-slate-900 rounded-[2rem] overflow-hidden shadow-2xl transform transition-all sm:w-full sm:max-w-6xl h-[90vh] flex flex-col relative"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <div class="p-6 bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Pratinjau Dokumen LKPS</h3>
                    <p class="text-xs text-slate-500">Laporan data kuantitatif lengkap untuk prodi aktif.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="flex items-center bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-1">
                        <a href="{{ route('report.download-lkps') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20">PDF</a>
                        <a href="{{ route('report.download-lkps-docx') }}" class="px-4 py-2 text-indigo-600 dark:text-indigo-400 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-all">Docx</a>
                    </div>
                    <button @click="isPreviewOpen = false" class="p-2.5 bg-white dark:bg-slate-700 text-slate-400 hover:text-rose-500 rounded-xl transition-all border border-slate-200 dark:border-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
            <div class="flex-1 bg-slate-100 dark:bg-slate-950 p-4">
                <iframe src="{{ route('report.preview-lkps') }}" class="w-full h-full rounded-2xl border border-slate-200 dark:border-slate-800 shadow-inner" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>
