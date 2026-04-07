<div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                Monitoring <span class="text-transparent bg-clip-text smart-gradient">Progress</span>
            </h1>
            <p class="mt-2 text-slate-500">Lacak status dokumen, histori persetujuan, dan kesiapan kriteria secara real-time.</p>
        </div>

        <!-- Pipeline Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div class="p-6 glass-card text-center">
                <div class="text-2xl font-black text-slate-700 dark:text-white">{{ $statusSummary['draft'] }}</div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Draft</div>
            </div>
            <div class="p-6 glass-card text-center border-b-4 border-indigo-500">
                <div class="text-2xl font-black text-indigo-600">{{ $statusSummary['submitted'] }}</div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Submitted</div>
            </div>
            <div class="p-6 glass-card text-center border-b-4 border-amber-500">
                <div class="text-2xl font-black text-amber-600">{{ $statusSummary['review'] }}</div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">In Review</div>
            </div>
            <div class="p-6 glass-card text-center border-b-4 border-emerald-500">
                <div class="text-2xl font-black text-emerald-600">{{ $statusSummary['approved'] }}</div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Approved</div>
            </div>
            <div class="p-6 glass-card text-center border-b-4 border-rose-500">
                <div class="text-2xl font-black text-rose-600">{{ $statusSummary['revision'] }}</div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Revision</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Kriteria Status Grid -->
            <div class="lg:col-span-2 space-y-6">
                <div class="glass-card p-8">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-8 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                        Matriks Status Kriteria
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach($kriterias as $k)
                        <div class="p-4 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-black text-indigo-600 text-xs">{{ $k->kode }}</span>
                                <span class="text-[10px] font-bold text-slate-400">{{ $k->dokumens_count }} Docs</span>
                            </div>
                            <div class="flex flex-wrap gap-1">
                                @for($i = 0; $i < $k->approved_count; $i++) <span class="w-2 h-2 rounded-full bg-emerald-500"></span> @endfor
                                @for($i = 0; $i < $k->submitted_count + $k->review_count; $i++) <span class="w-2 h-2 rounded-full bg-indigo-500"></span> @endfor
                                @for($i = 0; $i < $k->revision_count; $i++) <span class="w-2 h-2 rounded-full bg-rose-500"></span> @endfor
                                @if($k->dokumens_count == 0) <span class="w-2 h-2 rounded-full bg-slate-200 dark:bg-slate-700"></span> @endif
                            </div>
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
</div>
