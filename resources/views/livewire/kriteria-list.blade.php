<div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                    Manajemen <span class="text-transparent bg-clip-text smart-gradient">Kriteria / Standar</span>
                </h1>
                <p class="mt-2 text-slate-500 text-sm">Sesuaikan butir instrumen akreditasi untuk setiap tipe LAM.</p>
            </div>
            <button wire:click="create" class="px-6 py-3 smart-gradient text-white font-bold rounded-2xl shadow-lg">
                TAMBAH KRITERIA
            </button>
        </div>

        <!-- LAM Filter Tabs -->
        <div class="flex items-center space-x-2 mb-8 bg-white dark:bg-slate-900 p-1.5 rounded-2xl border border-slate-200 dark:border-slate-800 w-fit">
            @foreach($lamOptions as $opt)
                <button wire:click="setLamFilter('{{ $opt }}')" 
                    class="px-5 py-2 rounded-xl text-xs font-black transition-all {{ $selectedLamType === $opt ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-400 hover:text-slate-600' }}">
                    {{ strtoupper(str_replace('lam-', '', $opt)) }}
                </button>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Card -->
            @if($isEditing)
            <div class="lg:col-span-1">
                <div class="glass-card p-6 sticky top-24 animate-fade-in border-indigo-500/30">
                    <h3 class="text-lg font-black text-slate-800 dark:text-white mb-6 uppercase tracking-wider">
                        {{ $editingId ? 'Edit Kriteria' : 'Butir Kriteria Baru' }}
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Kode</label>
                                <input type="text" wire:model="kode" class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/50" placeholder="e.g. C1">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Bobot (%)</label>
                                <input type="number" wire:model="bobot" class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/50">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Nama Kriteria</label>
                            <input type="text" wire:model="nama" class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Berlaku Untuk Standard</label>
                            <select wire:model="lam_type" class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/50">
                                @foreach($lamOptions as $opt)
                                    <option value="{{ $opt }}">{{ strtoupper(str_replace('lam-', '', $opt)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-8 flex items-center space-x-3">
                        <button wire:click="save" class="flex-1 py-3 smart-gradient text-white text-xs font-black rounded-xl shadow-lg">SIMPAN</button>
                        <button wire:click="resetFields" class="px-4 py-3 bg-slate-100 dark:bg-slate-800 text-slate-500 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700">BATAL</button>
                    </div>
                </div>
            </div>
            @endif

            <!-- List Card -->
            <div class="{{ $isEditing ? 'lg:col-span-2' : 'lg:col-span-3' }}">
                <div class="glass-card overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800">
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Kriteria</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Bobot</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($kriterias as $k)
                            <tr class="hover:bg-indigo-50/10 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center mr-4 text-indigo-600 font-black text-xs">
                                            {{ $k->kode }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $k->nama }}</div>
                                            <div class="text-[10px] text-slate-400 font-medium uppercase tracking-tight">Dokumen: {{ $k->approved_count }}/{{ $k->submitted_count + $k->approved_count }} Disetujui</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="text-sm font-black text-slate-700 dark:text-slate-300">{{ $k->bobot }}%</div>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-all">
                                        <button wire:click="edit({{ $k->id }})" class="p-2 text-slate-400 hover:text-indigo-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <button wire:click="delete({{ $k->id }})" wire:confirm="Hapus kriteria ini?" class="p-2 text-slate-400 hover:text-rose-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
