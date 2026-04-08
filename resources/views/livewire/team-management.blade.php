<div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                    Manajemen <span class="text-transparent bg-clip-text smart-gradient">Tim Akreditasi</span>
                </h1>
                <p class="mt-2 text-slate-500 text-sm">Kelola anggota tim dan hak akses untuk {{ $prodi->nama }}</p>
            </div>
            
            <!-- Invite Form -->
            <div class="glass-card p-2 flex flex-col md:flex-row items-center gap-2 bg-white/50 border-indigo-100 dark:border-indigo-900/30">
                <div class="flex-1 min-w-[200px] w-full">
                    <div class="relative group">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                        <input type="email" wire:model="newEmail" placeholder="Email anggota..." 
                               class="w-full bg-transparent border-none focus:ring-0 text-sm pl-10 text-slate-700 dark:text-slate-200">
                    </div>
                </div>

                <div class="flex-1 min-w-[200px] w-full">
                    <div class="relative group">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <input type="text" wire:model="newName" placeholder="Nama Lengkap (Opsional)..." 
                               class="w-full bg-transparent border-none focus:ring-0 text-sm pl-10 text-slate-700 dark:text-slate-200">
                    </div>
                </div>

                <div class="flex flex-col space-y-2 w-full md:w-auto">
                    <select wire:model="newRole" class="text-xs font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-800 border-none rounded-xl focus:ring-0 py-2.5 px-4 w-full">
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <label class="flex items-center space-x-2 px-2 cursor-pointer">
                        <input type="checkbox" wire:model="sendEmail" class="w-3 h-3 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Kirim detail via Email</span>
                    </label>
                </div>
                <button wire:click="invite" class="px-6 py-2.5 bg-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20 whitespace-nowrap w-full md:w-auto">Tambah</button>
            </div>
        </div>

        <!-- Password Generator Alert -->
        @if($generatedPassword)
        <div class="bg-emerald-50 dark:bg-emerald-900/10 border-2 border-emerald-500/50 rounded-3xl p-8 mb-8 animate-bounce-short">
            <div class="flex items-center justify-between">
                <div class="flex items-start space-x-5">
                    <div class="w-14 h-14 bg-emerald-500 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-emerald-500/20">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-emerald-900 dark:text-emerald-400">User Baru Berhasil Dibuat!</h3>
                        <p class="text-sm text-emerald-700 dark:text-emerald-500 font-medium max-w-lg mt-1">Silakan berikan password sementara di bawah ini kepada pengguna baru. Password ini hanya muncul sekali.</p>
                        
                        <div class="mt-6 flex items-center space-x-4">
                            <div class="bg-white dark:bg-slate-900 px-6 py-3 rounded-2xl border-2 border-dashed border-emerald-500 flex items-center space-x-4">
                                <span class="text-2xl font-mono font-black text-emerald-600 tracking-wider">
                                    {{ $generatedPassword }}
                                </span>
                                <button onclick="navigator.clipboard.writeText('{{ $generatedPassword }}'); alert('Password berhasil disalin!');" class="p-2 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors text-emerald-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <button wire:click="closePasswordAlert" class="text-emerald-400 hover:text-emerald-600 focus:outline-none p-2 bg-white dark:bg-emerald-950 rounded-full shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
        @endif

        <!-- Members Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($members as $member)
            <div class="glass-card p-6 flex flex-col items-center text-center relative group hover:scale-[1.02] transition-all duration-300">
                <!-- Role Badge -->
                <div class="absolute top-4 right-4">
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest 
                        {{ $member->role == 'admin' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ $roles[$member->role] ?? 'Anggota' }}
                    </span>
                </div>

                <!-- Avatar -->
                <div class="w-16 h-16 rounded-2xl smart-gradient flex items-center justify-center text-white text-xl font-black shadow-xl shadow-indigo-500/20 mb-4 transition-transform group-hover:rotate-6">
                    {{ strtoupper(substr($member->name, 0, 1)) }}
                </div>

                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">{{ $member->name }}</h3>
                <p class="text-xs text-slate-400 mb-6">{{ $member->email }}</p>

                <div class="w-full pt-6 mt-auto border-t border-slate-100 dark:border-slate-800 flex items-center justify-center space-x-2">
                    <!-- Change Role (Admin Only or Self) -->
                    <select wire:change="updateRole({{ $member->id }}, $event.target.value)" 
                            class="text-[10px] font-black uppercase tracking-widest bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:ring-0 px-3 py-1.5 hover:border-indigo-500 transition-colors">
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}" {{ $member->role == $key ? 'selected' : '' }}>Set As {{ $label }}</option>
                        @endforeach
                    </select>

                    <!-- Remove Button -->
                    @if($member->id != auth()->id())
                    <button wire:confirm="Apakah Anda yakin ingin menghapus {{ $member->name }} dari tim?"
                            wire:click="remove({{ $member->id }})" 
                            class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 transition-colors"
                            title="Hapus dari tim">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Empty State -->
        @if($members->isEmpty())
        <div class="glass-card p-20 text-center">
            <div class="w-20 h-20 bg-slate-100 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Belum Ada Anggota Tim</h2>
            <p class="text-slate-500 mt-2 max-w-md mx-auto">Tambahkan anggota tim akreditasi menggunakan email mereka di bagian kanan atas.</p>
        </div>
        @endif
    </div>
</div>
