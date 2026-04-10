<div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                    Pengaturan <span class="text-transparent bg-clip-text smart-gradient">Instrumen</span>
                </h1>
                <p class="mt-2 text-slate-500 font-medium">Konfigurasi tabel dan kolom untuk setiap standar akreditasi.</p>
            </div>
            
            <div class="flex items-center space-x-4">
                <select wire:model.live="selectedLam" class="bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 rounded-2xl text-xs font-black uppercase tracking-widest px-6 py-3 shadow-sm focus:ring-indigo-500">
                    @foreach($lamTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                
                <button wire:click="openCreateTable" class="px-6 py-3 smart-gradient text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all active:scale-95 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    TABEL BARU
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8">
            @foreach($tables as $table)
                <div class="glass-card overflow-hidden group">
                    <div class="p-6 md:p-8 bg-white/50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-600/10 flex items-center justify-center text-indigo-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 00-2-2H5a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2zm0-5V7a2 2 0 012-2h2a2 2 0 012 2v5m-6 0a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2M9 5a2 2 0 012-2h2a2 2 0 012 2v2"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900 dark:text-white">{{ $table->label }}</h3>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">SLUG: {{ $table->slug }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $table->columns->count() }} Kolom Terdeteksi</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <button wire:click="editTable({{ $table->id }})" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-white dark:hover:bg-slate-800 rounded-xl transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                            <button wire:click="deleteTable({{ $table->id }})" wire:confirm="Hapus tabel ini beserta semua kolomnya?" class="p-2 text-slate-400 hover:text-red-600 hover:bg-white dark:hover:bg-slate-800 rounded-xl transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                        </div>
                    </div>

                    <div class="p-6 md:p-8">
                        <div class="flex flex-wrap gap-3">
                            @php
                                $rootCols = $table->columns->whereNull('parent_id');
                                $allCols = $table->columns;
                            @endphp
                            
                            @foreach($rootCols as $column)
                                <div class="flex flex-col gap-2">
                                    {{-- Root Column --}}
                                    <div class="px-4 py-3 bg-indigo-50/50 dark:bg-indigo-900/10 rounded-2xl border border-indigo-100 dark:border-indigo-800/50 flex items-center space-x-3 group/col transition-all hover:border-indigo-400">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-indigo-700 dark:text-indigo-300 uppercase tracking-tighter">{{ $column->header_name }}</span>
                                            <span class="text-[9px] font-medium text-slate-400 uppercase tracking-tighter">{{ $column->field_name }} • {{ $column->data_type }}</span>
                                        </div>
                                        <div class="flex items-center opacity-0 group-hover/col:opacity-100 transition-opacity">
                                            <button wire:click="editColumn({{ $column->id }})" class="p-1 text-slate-400 hover:text-indigo-600"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                                            <button wire:click="deleteColumn({{ $column->id }})" wire:confirm="Hapus kolom ini?" class="p-1 text-slate-400 hover:text-red-600"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                        </div>
                                    </div>

                                    {{-- Child Columns --}}
                                    <div class="flex flex-wrap gap-2 pl-4 border-l-2 border-indigo-100 dark:border-indigo-900/30 ml-2 mt-1">
                                        @foreach($allCols->where('parent_id', $column->id) as $child)
                                            <div class="px-3 py-2 bg-white dark:bg-slate-800/30 rounded-xl border border-slate-100 dark:border-slate-800 flex items-center space-x-2 group/child transition-all hover:border-indigo-200">
                                                <div class="flex flex-col">
                                                    <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300">{{ $child->header_name }}</span>
                                                </div>
                                                <div class="flex items-center opacity-0 group-hover/child:opacity-100 transition-opacity">
                                                    <button wire:click="editColumn({{ $child->id }})" class="p-1 text-slate-400 hover:text-indigo-600"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                                                    <button wire:click="deleteColumn({{ $child->id }})" wire:confirm="Hapus kolom ini?" class="p-1 text-slate-400 hover:text-red-600"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            
                            <button wire:click="openCreateColumn({{ $table->id }})" class="px-4 py-3 bg-white dark:bg-slate-900 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl flex items-center space-x-2 text-slate-400 hover:text-indigo-600 hover:border-indigo-500/50 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span class="text-xs font-bold uppercase tracking-widest">Tambah Kolom</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Table Modal -->
    <div x-data="{ show: @entangle('showTableModal') }" x-show="show" class="fixed inset-0 z-[60] overflow-y-auto" style="display:none">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="show = false"></div>
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl w-full max-w-md relative overflow-hidden">
                <div class="p-8">
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-6">
                        {{ $editingTable ? 'Edit Tabel' : 'Tambah Tabel Baru' }}
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Label Tabel</label>
                            <input type="text" wire:model="tableLabel" class="w-full bg-slate-50 dark:bg-slate-950 border-none rounded-2xl px-5 py-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500/20" placeholder="Contoh: Tabel 1.A Budaya Mutu">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Slug (Internal Key)</label>
                            <input type="text" wire:model="tableSlug" class="w-full bg-slate-50 dark:bg-slate-950 border-none rounded-2xl px-5 py-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500/20" placeholder="Contoh: budaya_mutu">
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 p-6 flex flex-row-reverse gap-3">
                    <button wire:click="saveTable" class="px-6 py-3 smart-gradient text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/20">SIMPAN</button>
                    <button @click="show = false" class="px-6 py-3 text-slate-500 text-xs font-black uppercase tracking-widest">BATAL</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Column Modal -->
    <div x-data="{ show: @entangle('showColumnModal') }" x-show="show" class="fixed inset-0 z-[60] overflow-y-auto" style="display:none">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="show = false"></div>
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl w-full max-w-md relative overflow-hidden">
                <div class="p-8">
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-6">
                        {{ $editingColumn ? 'Edit Kolom' : 'Tambah Kolom Baru' }}
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Header</label>
                            <input type="text" wire:model="columnHeader" class="w-full bg-slate-50 dark:bg-slate-950 border-none rounded-2xl px-5 py-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500/20" placeholder="Contoh: Nama Lengkap Dosen">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Field Database</label>
                            <input type="text" wire:model="columnField" class="w-full bg-slate-50 dark:bg-slate-950 border-none rounded-2xl px-5 py-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500/20" placeholder="Contoh: nama_dosen">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tipe Data</label>
                            <select wire:model="columnType" class="w-full bg-slate-50 dark:bg-slate-950 border-none rounded-2xl px-5 py-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500/20">
                                <option value="text">Text / String</option>
                                <option value="number">Numeric / Angka</option>
                                <option value="currency">Currency / Mata Uang (Rp)</option>
                                <option value="date">Date / Tanggal</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Induk Kolom (Optional)</label>
                            <select wire:model="parentColumnId" class="w-full bg-slate-50 dark:bg-slate-950 border-none rounded-2xl px-5 py-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500/20">
                                <option value="">Tanpa Induk (Root)</option>
                                @if($selectedTableId)
                                    @php 
                                        $potentialParents = \App\Models\LamTableColumn::where('lam_table_id', $this->selectedTableId)
                                            ->whereNull('parent_id')
                                            ->when($editingColumn, fn($q) => $q->where('id', '!=', $editingColumn->id))
                                            ->get();
                                    @endphp
                                    @foreach($potentialParents as $p)
                                        <option value="{{ $p->id }}">{{ $p->header_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 p-6 flex flex-row-reverse gap-3">
                    <button wire:click="saveColumn" class="px-6 py-3 smart-gradient text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/20">SIMPAN</button>
                    <button @click="show = false" class="px-6 py-3 text-slate-500 text-xs font-black uppercase tracking-widest">BATAL</button>
                </div>
            </div>
        </div>
    </div>
</div>
