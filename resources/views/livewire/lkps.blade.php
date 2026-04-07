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
            <div class="flex items-center bg-white dark:bg-slate-900 p-2 rounded-[1.5rem] shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-slate-800">
                @foreach($availableLams as $key => $label)
                    <button wire:click="setLam('{{ $key }}')" 
                        class="px-5 py-2.5 rounded-2xl text-[10px] font-black tracking-widest transition-all {{ $selectedLam === $key ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-400 hover:text-slate-600' }}">
                        {{ $label }}
                    </button>
                @endforeach
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
                                    <button wire:click="addRow" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all flex items-center shadow-xl shadow-indigo-500/20">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Tambah Baris
                                    </button>
                                </div>
                            </div>

                            <div class="p-0">
                                <div class="overflow-x-auto scrollbar-fancy pb-6">
                                    <table class="w-full text-left border-separate border-spacing-y-3">
                                        <thead>
                                            @php
                                                $hasChildren = $rootColumns->some(fn($c) => $c->children->isNotEmpty());
                                            @endphp
                                            <tr class="bg-indigo-600 dark:bg-indigo-800">
                                                @foreach($rootColumns as $column)
                                                    <th rowspan="{{ $column->children->isEmpty() && $hasChildren ? 2 : 1 }}" 
                                                        colspan="{{ $column->colspan }}"
                                                        class="py-5 px-6 text-[10px] font-black text-white uppercase tracking-[0.2em] border-r border-white/10 last:border-r-0 first:rounded-tl-3xl last:rounded-tr-3xl text-center shadow-lg">
                                                        {{ $column->header_name }}
                                                    </th>
                                                @endforeach
                                                <th rowspan="{{ $hasChildren ? 2 : 1 }}" class="py-5 px-6 bg-indigo-700/50 dark:bg-indigo-900/50 rounded-tr-3xl"></th>
                                            </tr>
                                            @if($hasChildren)
                                                <tr class="bg-indigo-50 dark:bg-indigo-900/20">
                                                    @foreach($rootColumns as $column)
                                                        @foreach($column->children as $child)
                                                            <th class="py-3 px-6 text-[9px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest border-b border-indigo-100 dark:border-indigo-800/50 border-r border-indigo-100 dark:border-indigo-800/20 last:border-r-0 text-center">
                                                                {{ $child->header_name }}
                                                            </th>
                                                        @endforeach
                                                    @endforeach
                                                </tr>
                                            @endif
                                        </thead>
                                        <tbody class="divide-y divide-transparent">
                                            @forelse($tableData as $row)
                                                @php $idx = $row->id; @endphp
                                                <tr class="group hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-all">
                                                    @foreach($leafColumns as $column)
                                                        <td class="py-5 px-6 text-sm font-bold text-slate-700 dark:text-slate-300 {{ $editingId === $idx ? 'bg-indigo-50/30 shadow-inner' : '' }}">
                                                            @if($editingId === $idx)
                                                                <input type="text" 
                                                                       wire:model.defer="editBuffer.{{ $column->field_name }}" 
                                                                       class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border-2 border-indigo-200 dark:border-indigo-800 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                                                            @else
                                                                @php $val = $row->data_values[$column->field_name] ?? ''; @endphp
                                                                @if($column->data_type === 'currency')
                                                                    <span class="text-slate-400 mr-1 font-medium italic">Rp</span>{{ number_format((float)$val, 0, ',', '.') }}
                                                                @elseif($column->data_type === 'number')
                                                                    {{ number_format((float)$val, 0, ',', '.') }}
                                                                @else
                                                                    {{ $val ?: '-' }}
                                                                @endif
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                    <td class="py-5 px-6 text-right {{ $editingId === $idx ? 'bg-indigo-50/30' : '' }} rounded-r-3xl border-y border-r border-slate-100 dark:border-slate-800 group-hover:border-slate-200 transition-all">
                                                        @if($editingId === $idx)
                                                            <div class="flex items-center justify-end space-x-2">
                                                                <button wire:click="saveEntry" class="p-2.5 bg-emerald-500 text-white rounded-xl shadow-lg shadow-emerald-500/20 hover:scale-110 active:scale-95 transition-all">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                                </button>
                                                                <button wire:click="cancelEdit" class="p-2.5 bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl hover:scale-110 active:scale-95 transition-all">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                </button>
                                                            </div>
                                                        @else
                                                            <div class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-all transform group-hover:translate-x-0 translate-x-2">
                                                                <button wire:click="editEntry({{ $idx }})" class="p-2.5 text-slate-400 hover:text-indigo-600 hover:bg-white dark:hover:bg-slate-800 rounded-xl transition-all shadow-sm">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                                </button>
                                                                <button wire:click="deleteRow({{ $idx }})" wire:confirm="Hapus baris data ini?" class="p-2.5 text-slate-400 hover:text-red-600 hover:bg-white dark:hover:bg-slate-800 rounded-xl transition-all shadow-sm">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
