<div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                    Manajemen <span class="text-transparent bg-clip-text smart-gradient">Program Studi</span>
                </h1>
                <p class="mt-2 text-slate-500 text-sm">Kelola daftar prodi dan tentukan standar akreditasi (LAM) masing-masing.</p>
            </div>
            <button wire:click="create" class="px-6 py-3 smart-gradient text-white font-bold rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transform hover:-translate-y-0.5 transition-all flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                TAMBAH PRODI
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Card -->
            @if($isEditing)
            <div class="lg:col-span-1">
                <div class="glass-card p-6 sticky top-24 animate-fade-in border-indigo-500/30">
                    <h3 class="text-lg font-black text-slate-800 dark:text-white mb-6 uppercase tracking-wider">
                        {{ $editingProdiId ? 'Edit Program Studi' : 'Tambah Prodi Baru' }}
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Nama Prodi</label>
                            <input type="text" wire:model="nama" class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Kode Prodi</label>
                            <input type="text" wire:model="kode" class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/50" placeholder="e.g. TI-S1">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Jenjang</label>
                                <select wire:model="jenjang" class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/50">
                                    <option value="D3">D3</option>
                                    <option value="D4">D4</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Tipe LAM</label>
                                <select wire:model="lam_type" class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/50">
                                    @foreach($lamOptions as $opt)
                                        <option value="{{ $opt }}">{{ strtoupper(str_replace('lam-', '', $opt)) }}</option>
                                    @endforeach
                                </select>
                            </div>
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
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Detail Prodi</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Standar Akreditasi</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($prodis as $prodi)
                            <tr class="hover:bg-indigo-50/10 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-xl smart-gradient flex items-center justify-center mr-4 text-white font-black text-xs">
                                            {{ $prodi->kode }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $prodi->nama }}</div>
                                            <div class="text-[10px] text-slate-400 font-medium uppercase">{{ $prodi->jenjang }} — {{ $prodi->kode }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="px-3 py-1.5 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 text-[10px] font-black uppercase tracking-tight">
                                        {{ str_replace('lam-', '', $prodi->lam_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-all">
                                        <button wire:click="edit({{ $prodi->id }})" class="p-2 text-slate-400 hover:text-indigo-600 bg-white dark:bg-slate-900 rounded-lg shadow-sm border border-slate-200 dark:border-slate-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <button wire:click="delete({{ $prodi->id }})" wire:confirm="Hapus prodi ini? Semua data terkait akan ikut terhapus." class="p-2 text-slate-400 hover:text-rose-600 bg-white dark:bg-slate-900 rounded-lg shadow-sm border border-slate-200 dark:border-slate-800">
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
