<div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <div>
                <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                    Monitoring <span class="text-transparent bg-clip-text smart-gradient">Progress</span>
                </h1>
                <p class="mt-2 text-slate-500 font-medium italic">"Data yang baik adalah kunci keputusan yang tepat."</p>
            </div>
            <button wire:click="openEditModal" 
                class="px-6 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none flex items-center space-x-3 hover:border-indigo-500 transition-all group">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center group-hover:bg-indigo-600 transition-all">
                    <svg class="w-5 h-5 text-indigo-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div class="text-left">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Manajemen Jadwal</p>
                    <p class="text-xs font-black text-slate-700 dark:text-slate-200">Update Roadmap</p>
                </div>
            </button>
        </div>

        <!-- Accreditation Lifecycle Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-10">
            <!-- Expiry Countdown -->
            <div class="lg:col-span-2 p-8 bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-slate-800 relative overflow-hidden group">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-600/5 rounded-full blur-3xl group-hover:bg-indigo-600/10 transition-all"></div>
                <div class="relative flex items-center space-x-6">
                    <div class="relative inline-flex items-center justify-center p-2">
                        @php
                            $expiry = $prodi->tanggal_kadaluarsa ? \Carbon\Carbon::parse($prodi->tanggal_kadaluarsa) : null;
                            $diffDays = $expiry ? \Carbon\Carbon::now()->diffInDays($expiry, false) : 0;
                            $percent = $expiry ? max(0, min(100, ($diffDays / 1825) * 100)) : 0; // Assuming 5 years cycle
                            $color = $diffDays < 180 ? 'text-rose-600' : ($diffDays < 365 ? 'text-amber-500' : 'text-emerald-500');
                        @endphp
                        <svg class="w-32 h-32 transform -rotate-90">
                            <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="10" fill="transparent" class="text-slate-100 dark:text-slate-800" />
                            <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="10" fill="transparent" stroke-dasharray="364" 
                                stroke-dashoffset="{{ 364 - (364 * $percent / 100) }}"
                                class="{{ $color }} stroke-current rounded-full transition-all duration-1000" />
                        </svg>
                        <div class="absolute text-center">
                            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ round($diffDays) }}</div>
                            <div class="text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Hari Lagi</div>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1 leading-none">Masa Berlaku Akreditasi</p>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white leading-tight">
                            {{ $prodi->tanggal_kadaluarsa ? $expiry->format('d M Y') : 'Set Tanggal Expiry' }}
                        </h3>
                        <div class="mt-4 flex items-center space-x-3">
                            <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-black rounded-lg uppercase">
                                Status: {{ $prodi->status_akreditasi ?: 'Aktif' }}
                            </span>
                            @if($diffDays < 180)
                                <span class="flex items-center text-rose-600 text-[10px] font-black animate-pulse uppercase tracking-widest">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    Segera Re-Akreditasi!
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Target vs Current -->
            <div class="p-8 bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-slate-800 flex flex-col justify-center">
                <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-4 text-center">Strategi Peringkat</p>
                <div class="flex items-center justify-center space-x-4">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-2">
                            <span class="text-xl font-black text-slate-700 dark:text-white">{{ $prodi->peringkat_saat_ini ?: '-' }}</span>
                        </div>
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest leading-none">Saat Ini</p>
                    </div>
                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mb-2 shadow-lg shadow-indigo-500/30">
                            <span class="text-xl font-black text-white">{{ $prodi->target_peringkat ?: '?' }}</span>
                        </div>
                        <p class="text-[8px] font-bold text-indigo-600 uppercase tracking-widest leading-none">Target</p>
                    </div>
                </div>
            </div>

            <!-- Submission Deadline -->
            <div class="p-8 bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-slate-800">
                <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-2 leading-none">Deadline Submit Borang</p>
                <h4 class="text-2xl font-black text-slate-900 dark:text-white">
                    {{ $prodi->target_submit ? \Carbon\Carbon::parse($prodi->target_submit)->format('d/m/Y') : '--/--/----' }}
                </h4>
                <div class="mt-6 flex items-center space-x-2">
                    <div class="flex-1 h-3 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                        @php
                            $submitPercent = $totalDocs > 0 ? ($statusSummary['approved'] / $totalDocs) * 100 : 0;
                        @endphp
                        <div class="h-full bg-emerald-500 transition-all duration-1000 shadow-[0_0_10px_rgba(16,185,129,0.3)]" style="width: {{ $submitPercent }}%"></div>
                    </div>
                    <span class="text-xs font-black text-slate-400">{{ round($submitPercent) }}%</span>
                </div>
                <p class="mt-2 text-[8px] font-bold text-slate-400 uppercase tracking-widest italic">Persiapan Dokumen SAPTO/LAM</p>
            </div>
        </div>

        <!-- Pipeline Stats Small -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-10">
            <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <div class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Draft</div>
                    <div class="text-xl font-black text-slate-700 dark:text-white">{{ $statusSummary['draft'] }}</div>
                </div>
                <div class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
            </div>
            <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-indigo-100 dark:border-indigo-900/30 flex items-center justify-between">
                <div>
                    <div class="text-[8px] font-black text-indigo-400 uppercase tracking-widest">Submitted</div>
                    <div class="text-xl font-black text-indigo-600">{{ $statusSummary['submitted'] }}</div>
                </div>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>
            <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-amber-100 dark:border-amber-900/30 flex items-center justify-between">
                <div>
                    <div class="text-[8px] font-black text-amber-500 uppercase tracking-widest">Review</div>
                    <div class="text-xl font-black text-amber-600">{{ $statusSummary['review'] }}</div>
                </div>
                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-emerald-100 dark:border-emerald-900/30 flex items-center justify-between">
                <div>
                    <div class="text-[8px] font-black text-emerald-500 uppercase tracking-widest">Approved</div>
                    <div class="text-xl font-black text-emerald-600">{{ $statusSummary['approved'] }}</div>
                </div>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-rose-100 dark:border-rose-900/30 flex items-center justify-between">
                <div>
                    <div class="text-[8px] font-black text-rose-400 uppercase tracking-widest">Revision</div>
                    <div class="text-xl font-black text-rose-600">{{ $statusSummary['revision'] }}</div>
                </div>
                <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Kriteria Status Grid -->
            <div class="lg:col-span-2 space-y-6">
                <div class="glass-card p-8">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-8 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                        Matriks Status Kriteria
                        <span class="ml-auto flex items-center text-[10px] text-indigo-600 font-black uppercase tracking-widest bg-indigo-50 px-3 py-1 rounded-full animate-pulse">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                            AI Guidance Active
                        </span>
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach($kriterias as $k)
                        <div class="p-5 rounded-[2rem] border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 group hover:border-indigo-400 transition-all relative">
                            <button wire:click="openAiGuide({{ $k->id }})" 
                                class="absolute top-4 right-4 w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all hover:bg-indigo-600 group-hover:shadow-lg group-hover:shadow-indigo-500/20">
                                <svg class="w-4 h-4 text-indigo-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </button>
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="font-black text-indigo-600 text-xs">{{ $k->kode }}</span>
                                    <button wire:click="openAiGuide({{ $k->id }})" class="p-1 hover:bg-white rounded-lg transition-all group">
                                        <svg class="w-3 h-3 text-indigo-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </button>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400">{{ $k->dokumens_count }} Docs</span>
                            </div>
                            <div class="flex flex-wrap gap-1 mb-2">
                                @for($i = 0; $i < $k->approved_count; $i++) <span class="w-2 h-2 rounded-full bg-emerald-500"></span> @endfor
                                @for($i = 0; $i < $logCount = ($k->submitted_count + $k->review_count); $i++) <span class="w-2 h-2 rounded-full bg-indigo-500"></span> @endfor
                                @for($i = 0; $i < $k->revision_count; $i++) <span class="w-2 h-2 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.4)]"></span> @endfor
                                @if($k->dokumens_count == 0) <span class="w-2 h-2 rounded-full bg-slate-100 dark:bg-slate-800"></span> @endif
                            </div>
                            <div class="text-[9px] font-bold text-slate-400 leading-tight line-clamp-1 group-hover:text-indigo-400 transition-colors">{{ $k->nama }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Approval Workflow Logs -->
                <div class="glass-card p-8">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-8">Log Aktivitas & Workflow</h2>
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @forelse($workflows as $log)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                    <span class="absolute left-5 top-5 -ml-px h-full w-0.5 bg-slate-200 dark:bg-slate-800" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex items-start space-x-3">
                                        <div class="relative">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full ring-8 ring-white dark:ring-slate-900 
                                                {{ $log->to_status === 'approved' ? 'bg-emerald-500' : ($log->to_status === 'revision' ? 'bg-rose-500' : 'bg-indigo-500') }}">
                                                @if($log->to_status === 'approved')
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                @elseif($log->to_status === 'revision')
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                @else
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1 py-1.5">
                                            <div class="text-sm text-slate-500 font-medium">
                                                <span class="font-bold text-slate-900 dark:text-white">{{ $log->user->name }}</span>
                                                mengubah status ke 
                                                <span class="badge-smart 
                                                    {{ $log->to_status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 
                                                       ($log->to_status === 'revision' ? 'bg-rose-100 text-rose-700' : 'bg-indigo-100 text-indigo-700') }}">
                                                    {{ strtoupper($log->to_status) }}
                                                </span>
                                            </div>
                                            @if($log->comment)
                                            <div class="mt-2 text-sm text-slate-600 dark:text-slate-400 italic">
                                                "{{ $log->comment }}"
                                            </div>
                                            @endif
                                            <div class="mt-2 text-xs text-slate-400">
                                                {{ $log->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @empty
                            <p class="text-center text-slate-400 py-10 italic">Belum ada riwayat aktivitas workflow.</p>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right: Progress Overview -->
            <div class="space-y-6">
                <!-- Circular Chart Placeholder -->
                <div class="p-8 glass-card text-center">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Total Kesiapan Dokumen</h3>
                    <div class="relative inline-flex items-center justify-center p-4">
                        <svg class="w-40 h-40 transform -rotate-90">
                            <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="12" fill="transparent" class="text-slate-100 dark:text-slate-800" />
                            <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="12" fill="transparent" stroke-dasharray="440" 
                                stroke-dashoffset="{{ 440 - (440 * ($totalDocs > 0 ? $statusSummary['approved'] / $totalDocs : 0)) }}"
                                class="text-indigo-600 stroke-current rounded-full" />
                        </svg>
                        <div class="absolute text-center">
                            <div class="text-3xl font-black text-slate-900 dark:text-white">{{ round($totalDocs > 0 ? ($statusSummary['approved'] / $totalDocs) * 100 : 0) }}%</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Verified</div>
                        </div>
                    </div>
                    <p class="mt-6 text-sm text-slate-500 font-medium">Dokumen yang telah disetujui asesor terhadap total target unggahan.</p>
                </div>

                <!-- Notifications Panel (Mock) -->
                <div class="p-6 glass-card">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Pusat Notifikasi</h3>
                    <div class="space-y-4">
                        @forelse($notifications as $notif)
                        <div class="p-3 {{ $notif->type === 'system' ? 'bg-indigo-50/50 dark:bg-indigo-900/10 border-indigo-500/10' : 'bg-amber-50/50 dark:bg-amber-900/10 border-amber-500/10' }} border rounded-xl relative overflow-hidden">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full {{ $notif->type === 'system' ? 'bg-indigo-100 text-indigo-600' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center flex-shrink-0">
                                    @if($notif->type === 'system')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-black {{ $notif->type === 'system' ? 'text-indigo-600' : 'text-amber-600' }} uppercase tracking-widest">{{ $notif->type }} Message</p>
                                    <p class="text-xs text-slate-600 dark:text-slate-400 font-medium">{{ $notif->message }}</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="py-10 text-center">
                            <p class="text-xs text-slate-400 italic">Belum ada notifikasi baru.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Roadmap -->
    @if($showModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-md transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200 dark:border-slate-800">
                <form wire:submit.prevent="saveRoadmap">
                    <div class="bg-indigo-600 p-8">
                        <h3 class="text-2xl font-black text-white italic tracking-tight">Roadmap Strategis Akreditasi</h3>
                        <p class="text-indigo-100 text-xs mt-1 font-medium italic">Pastikan target jadwal sesuai dengan target tahun pelaksanaan.</p>
                    </div>

                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="label-smart">Peringkat Saat Ini</label>
                                <select wire:model="form.peringkat_saat_ini" class="input-smart">
                                    <option value="">- Pilih Peringkat -</option>
                                    <option value="Unggul">Unggul</option>
                                    <option value="Baik Sekali">Baik Sekali</option>
                                    <option value="Baik">Baik</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                </select>
                            </div>
                            <div>
                                <label class="label-smart">Target Peringkat</label>
                                <select wire:model="form.target_peringkat" class="input-smart border-indigo-200 bg-indigo-50/10">
                                    <option value="">- Pilih Target -</option>
                                    <option value="Unggul">Unggul</option>
                                    <option value="Baik Sekali">Baik Sekali</option>
                                    <option value="Baik">Baik</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="label-smart">Tanggal Kadaluarsa Sertifikat</label>
                                <input type="date" wire:model="form.tanggal_kadaluarsa" class="input-smart">
                            </div>
                            <div>
                                <label class="label-smart">Target Submit Borang (Deadine LAM)</label>
                                <input type="date" wire:model="form.target_submit" class="input-smart">
                            </div>
                        </div>

                        <div>
                            <label class="label-smart">Status Aktivitas Saat Ini</label>
                            <select wire:model="form.status_akreditasi" class="input-smart">
                                <option value="aktif">Aktif (Berjalan Normal)</option>
                                <option value="persiapan">Fase Persiapan Dokumen</option>
                                <option value="submitted">Sudah Dikirim ke SAPTO/LAM</option>
                                <option value="visitasi">Fase Visitasi Lapangan</option>
                                <option value="selesai">Re-Akreditasi Selesai</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-8 bg-slate-50 dark:bg-slate-800/50 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                        <button type="submit" class="flex-1 px-6 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black shadow-xl shadow-indigo-500/30 transition-all">
                            SIMPAN ROADMAP STRATEGIS
                        </button>
                        <button type="button" wire:click="closeModal" class="px-6 py-4 text-slate-500 font-bold hover:text-slate-700">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    @endif

    <!-- AI Slide-over Panel -->
    @if($showAiGuide)
    <div class="fixed inset-0 z-[70] overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closeAiGuide"></div>

            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div class="pointer-events-auto w-screen max-w-md transform transition duration-500 sm:duration-700">
                    <div class="flex h-full flex-col overflow-y-scroll bg-white dark:bg-slate-900 shadow-2xl border-l border-slate-200 dark:border-slate-800">
                        <div class="bg-indigo-600 py-10 px-8 relative overflow-hidden">
                            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                            <div class="relative flex items-center justify-between">
                                <h2 class="text-2xl font-black text-white italic tracking-tight" id="slide-over-title">AI Smart Guide</h2>
                                <button type="button" wire:click="closeAiGuide" class="rounded-xl text-indigo-200 hover:text-white hover:bg-white/10 p-2 transition-all">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            <div class="mt-4 relative bg-white/10 backdrop-blur-lg border border-white/20 p-4 rounded-2xl flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center shadow-lg">
                                    <span class="text-lg font-black text-indigo-600">{{ $selectedCriterion->kode }}</span>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-indigo-100 uppercase tracking-widest leading-none">Dokumen Pendukung Untuk</p>
                                    <p class="text-xs font-bold text-white line-clamp-1">{{ $selectedCriterion->nama }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-8">
                            <div class="flex items-center space-x-2 mb-8">
                                <div class="w-1 h-6 bg-indigo-600 rounded-full shadow-[0_0_10px_rgba(79,70,229,0.5)]"></div>
                                <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-[0.2em] flex-1">Checklist Strategis AI</h3>
                                <select wire:model.live="aiLimit" class="text-[10px] font-bold bg-slate-50 dark:bg-slate-800 border-none rounded-lg focus:ring-0 cursor-pointer">
                                    <option value="3">3 Tips</option>
                                    <option value="5">5 Tips</option>
                                    <option value="7">7 Tips</option>
                                    <option value="10">10 Tips</option>
                                </select>
                            </div>

                            <div class="space-y-4">
                                @forelse($aiGuidance as $item)
                                <div class="p-5 bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-slate-100 dark:border-slate-800 group hover:border-indigo-300 transition-all flex flex-col">
                                    <div class="flex items-start space-x-4">
                                        <div class="mt-1 w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                            <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs font-black text-slate-700 dark:text-slate-200 tracking-tight">{{ $item }}</p>
                                            <p class="mt-1 text-[10px] text-slate-400 font-medium italic">Sangat direkomendasikan untuk pembuktian indikator.</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800/50 flex justify-end">
                                        @if(in_array($item, $existingTasks))
                                            <div class="flex items-center space-x-2 text-emerald-600 dark:text-emerald-400 font-bold text-[10px] bg-emerald-50 dark:bg-emerald-900/30 px-3 py-1.5 rounded-xl border border-emerald-100 dark:border-emerald-800">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                <span>SUDAH MASUK DAFTAR</span>
                                            </div>
                                        @else
                                            <button wire:click="addAsTask('{{ str_replace("'", "\'", $item) }}')" 
                                                wire:loading.attr="disabled"
                                                class="px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all flex items-center space-x-2">
                                                <svg wire:loading.remove class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                <svg wire:loading class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                <span>SIAPKAN DRAFT</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <p class="text-center text-slate-400 py-10">AI sedang memproses rekomendasi lainnya...</p>
                                @endforelse
                            </div>

                            <div class="mt-10 p-6 bg-indigo-50 dark:bg-indigo-900/20 rounded-[2rem] border border-indigo-100 dark:border-indigo-800 relative overflow-hidden">
                                <div class="relative z-10">
                                    <p class="text-[9px] font-black text-indigo-600 uppercase tracking-[0.2em] mb-2 leading-none">💡 Pro-Tip Asesor</p>
                                    <p class="text-xs text-indigo-800/70 dark:text-indigo-300/70 font-medium italic">"Pastikan dokumen memiliki tanda tangan basah atau stempel QR Code resmi untuk meningkatkan validitas penilaian."</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto p-8 border-t border-slate-100 dark:border-slate-800 space-y-3">
                            <button wire:click="refreshAiGuide({{ $selectedKriteriaId }})" 
                                wire:loading.attr="disabled"
                                class="w-full py-4 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 font-black rounded-2xl hover:bg-indigo-100 transition-all flex items-center justify-center space-x-2">
                                <span wire:loading.remove><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg></span>
                                <span wire:loading class="animate-spin"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg></span>
                                <span>REFRESH ANALISIS AI</span>
                            </button>
                            <button wire:click="closeAiGuide" class="w-full py-4 bg-slate-900 dark:bg-slate-800 text-white font-black rounded-2xl hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/20">
                                TANDAI SAYA SUDAH TAHU
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
