<div class="px-4 py-8 mx-auto max-w-7xl">
    <!-- Header with Smart Gradient -->
    <header class="flex items-center justify-between mb-12 animate-fade-in animate-duration-700">
        <div>
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Dashboard <span class="text-transparent bg-clip-text smart-gradient">Akreditasi Cerdas</span>
            </h1>
            <p class="mt-2 text-lg text-slate-600 dark:text-slate-400">
                Data real-time penilaian dan gap analysis dokumen.
            </p>
        </div>
        
        <div class="flex items-center px-6 py-3 space-x-3 glass-card">
            <div class="flex items-center -space-x-2">
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-emerald-500 text-white text-xs border-2 border-white dark:border-slate-800">AI</div>
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-indigo-500 text-white text-xs border-2 border-white dark:border-slate-800">DB</div>
            </div>
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Arsitektur Aktif</span>
        </div>
    </header>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-6 mb-12 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Criteria Count -->
        <div class="p-6 glass-card group">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 group-hover:scale-110 group-hover:rotate-6 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <span class="text-xs font-bold text-indigo-500">STANDAR</span>
            </div>
            <h3 class="text-3xl font-bold text-slate-900 dark:text-white">{{ $totalCriteria }}</h3>
            <p class="text-sm font-medium text-slate-500">Kriteria {{ strtoupper($prodi->lam_type) }}</p>
        </div>

        <!-- Documents Uploaded -->
        <div class="p-6 glass-card group">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 group-hover:scale-110 group-hover:rotate-6 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <span class="text-xs font-bold text-purple-500">UPLOAD</span>
            </div>
            <h3 class="text-3xl font-bold text-slate-900 dark:text-white">{{ $totalDocs }}</h3>
            <p class="text-sm font-medium text-slate-500">Total Dokumen</p>
        </div>

        <!-- AI Average Score -->
        <div class="p-6 glass-card group">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 group-hover:rotate-6 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <span class="text-xs font-bold text-emerald-500">PENILAIAN AI</span>
            </div>
            <div class="flex items-baseline space-x-1">
                <h3 class="text-3xl font-bold text-slate-900 dark:text-white">{{ $avgScore }}</h3>
                <span class="text-slate-400 text-sm">/ 4.0</span>
            </div>
            <p class="text-sm font-medium text-slate-500">Skor Rata-rata</p>
        </div>

        <!-- Progress Timeline -->
        <div class="p-6 glass-card group">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 group-hover:scale-110 group-hover:rotate-6 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                </div>
                <span class="text-xs font-bold text-blue-500">PROGRESS</span>
            </div>
            <h3 class="text-3xl font-bold text-slate-900 dark:text-white">{{ $progressPercentage }}%</h3>
            <div class="w-full h-1.5 bg-slate-200 dark:bg-slate-800 rounded-full mt-3">
                <div class="h-full rounded-full smart-gradient shadow-[0_0_10px_rgba(79,70,229,0.5)]" style="width: {{ $progressPercentage }}%"></div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Left Column: Summary & Welcome -->
        <div class="lg:col-span-2 space-y-8">
            <div class="p-10 glass-card bg-white dark:bg-slate-900 overflow-hidden relative group">
                <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-32 h-32 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path></svg>
                </div>
                <div class="relative z-10">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-4">Selamat Datang di Portal Akreditasi</h2>
                    <p class="text-slate-500 mb-8 max-w-lg">Sistem ini menggunakan kecerdasan buatan untuk membantu Anda menyusun dokumen akreditasi dengan standar kualitas tertinggi.</p>
                    
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('data-dukung') }}" wire:navigate class="px-6 py-3 smart-gradient text-white text-xs font-black rounded-xl shadow-lg hover:shadow-indigo-500/40 transition-all uppercase tracking-widest">
                            MULAI UPLOAD DOKUMEN
                        </a>
                        <a href="{{ route('lkps') }}" wire:navigate class="px-6 py-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-black rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-200 transition-all uppercase tracking-widest">
                            INPUT DATA LKPS
                        </a>
                    </div>
                </div>
            </div>

            <!-- Analytics Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Score Trend Chart -->
                <div class="p-6 glass-card">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                        Tren Skor AI (6 Bulan)
                    </h3>
                    <div id="score-trend-chart" class="h-64"></div>
                </div>

                <!-- Status Distribution Chart -->
                <div class="p-6 glass-card">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 mr-2"></span>
                        Distribusi Status Dokumen
                    </h3>
                    <div id="status-distribution-chart" class="h-64"></div>
                </div>

                <!-- Workflow Activity Chart -->
                <div class="p-6 glass-card">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center">
                        <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                        Aktivitas Workflow (7 Hari)
                    </h3>
                    <div id="workflow-activity-chart" class="h-64"></div>
                </div>

                <!-- AI Analysis Heatmap -->
                <div class="p-6 glass-card">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center">
                        <span class="w-2 h-2 rounded-full bg-amber-500 mr-2"></span>
                        Distribusi Skor AI
                    </h3>
                    <div id="ai-heatmap-chart" class="h-64"></div>
                </div>
            </div>

            <!-- Criteria Progress Chart -->
            <div class="p-6 glass-card">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-purple-500 mr-2"></span>
                    Progress Kriteria {{ strtoupper($prodi->lam_type) }}
                </h3>
                <div id="criteria-progress-chart" class="h-80"></div>
            </div>
        </div>
            
            <!-- Smart AI Recommendations Column -->
            <div class="space-y-8">
                <!-- Smart Prediction Card -->
                <div class="p-8 rounded-[2rem] smart-gradient text-white relative overflow-hidden group shadow-2xl shadow-indigo-500/20">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
                    <div class="relative z-10">
                        <div class="flex items-center space-x-2 mb-6">
                            <div class="w-8 h-8 bg-white/20 rounded-lg backdrop-blur-md flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest opacity-80">AI Prediction</span>
                        </div>
                        <h3 class="text-xl font-extrabold mb-2">Estimasi Akreditasi</h3>
                        <div class="text-4xl font-black mb-4">{{ $predictionRank }}</div>
                        <div class="h-1.5 w-full bg-white/20 rounded-full mb-4 overflow-hidden">
                            <div class="h-full bg-white animate-progress-fill rounded-full" style="width: {{ $predictionConfidence }}%"></div>
                        </div>
                        <p class="text-xs text-white/70 leading-relaxed font-bold">
                            @if($predictionRank == 'DATA MINIM')
                                Data belum mencukupi untuk melakukan prediksi akurat. Unggah lebih banyak dokumen bukti fisik.
                            @else
                                Prediksi berdasarkan rata-rata skor AI ({{ $avgScore }}) dan kelengkapan data ({{ $progressPercentage }}%). Predikat ini bersifat simulasi awal.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="p-6 glass-card">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center">
                            <span class="w-2 h-2 rounded-full bg-indigo-500 mr-2 animate-pulse"></span>
                            Smart Insights
                        </h3>
                        <button class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest hover:underline">Refresh AI</button>
                    </div>
                    <div class="space-y-4">
                        @forelse($insights as $insight)
                            <div class="p-4 rounded-2xl border flex items-start space-x-3 transition-all hover:bg-white dark:hover:bg-slate-800 {{ 
                                $insight['type'] === 'success' ? 'bg-emerald-50/50 border-emerald-500/10 text-emerald-700' : (
                                $insight['type'] === 'warning' ? 'bg-amber-50/50 border-amber-500/10 text-amber-700' : 
                                'bg-rose-50/50 border-rose-500/10 text-rose-700')
                            }}">
                                <div class="mt-0.5 flex-shrink-0">
                                    @if($insight['type'] === 'success')
                                        <div class="w-5 h-5 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        </div>
                                    @else
                                        <div class="w-5 h-5 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                        </div>
                                    @endif
                                </div>
                                <p class="text-[11px] font-extrabold leading-relaxed  tracking-tight">{{ $insight['message'] }}</p>
                            </div>
                        @empty
                            <div class="p-8 text-center border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-2xl">
                                <p class="text-xs text-slate-400 italic">Belum ada insight baru. Kumpulkan lebih banyak data dokumen untuk mendapatkan saran cerdas.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <button 
                            wire:click="askAssistant"
                            wire:loading.attr="disabled"
                            wire:target="askAssistant"
                            class="w-full py-4 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-xs uppercase tracking-widest flex items-center justify-center group transform transition-all active:scale-95 shadow-lg disabled:opacity-50 disabled:cursor-wait">
                            <svg wire:loading.class="animate-spin" wire:target="askAssistant" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <span wire:loading.remove wire:target="askAssistant">Tanya Smart Assistant</span>
                            <span wire:loading wire:target="askAssistant">Menganalisis Data Dashboard...</span>
                        </button>

                        @if($smartResponse)
                            <div class="mt-6 p-5 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl border border-indigo-100 dark:border-indigo-800 animate-fade-in">
                                <div class="flex items-center space-x-2 mb-3">
                                    <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                                    <span class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">Rekomendasi AI</span>
                                </div>
                                <div class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed font-medium prose dark:prose-invert max-w-none">
                                    {!! Str::markdown($smartResponse) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts JavaScript -->
    <script>
        document.addEventListener('livewire:navigated', () => {
            initializeCharts();
        });

        document.addEventListener('DOMContentLoaded', () => {
            initializeCharts();
        });

        // Re-init on Livewire updates
        window.addEventListener('contentChanged', event => {
            initializeCharts();
        });

        function initializeCharts() {
            // Hilangkan chart lama jika ada untuk menghindari duplikasi
            const chartContainers = [
                "#score-trend-chart", "#status-distribution-chart", 
                "#workflow-activity-chart", "#ai-heatmap-chart", 
                "#criteria-progress-chart"
            ];
            
            chartContainers.forEach(selector => {
                const el = document.querySelector(selector);
                if (el) el.innerHTML = ''; 
            });

            // Score Trend Chart
            const scoreTrendData = @json($scoreTrendData);
            if (scoreTrendData && scoreTrendData.length > 0) {
                const scoreTrendOptions = {
                    series: [{
                        name: 'Skor AI',
                        data: scoreTrendData.map(item => item.score)
                    }],
                    chart: { type: 'area', height: 250, toolbar: { show: false }, zoom: { enabled: false } },
                    colors: ['#10B981'],
                    stroke: { curve: 'smooth', width: 3 },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } },
                    xaxis: { categories: scoreTrendData.map(item => item.month) },
                    yaxis: { min: 0, max: 4, tickAmount: 4 },
                    tooltip: { theme: 'dark' }
                };
                new ApexCharts(document.querySelector("#score-trend-chart"), scoreTrendOptions).render();
            }

            // Status Distribution Chart
            const statusData = @json($statusDistributionData);
            if (statusData && statusData.length > 0) {
                const statusOptions = {
                    series: statusData.map(item => item.count),
                    chart: { type: 'donut', height: 250 },
                    labels: statusData.map(item => item.status),
                    colors: statusData.map(item => item.color),
                    legend: { position: 'bottom' },
                    tooltip: { theme: 'dark' }
                };
                new ApexCharts(document.querySelector("#status-distribution-chart"), statusOptions).render();
            }

            // Workflow Activity Chart
            const workflowData = @json($workflowActivityData);
            if (workflowData && workflowData.length > 0) {
                const workflowOptions = {
                    series: [{ name: 'Aktivitas', data: workflowData.map(item => item.activities) }],
                    chart: { type: 'bar', height: 250, toolbar: { show: false } },
                    colors: ['#3B82F6'],
                    xaxis: { categories: workflowData.map(item => item.date) },
                    tooltip: { theme: 'dark' }
                };
                new ApexCharts(document.querySelector("#workflow-activity-chart"), workflowOptions).render();
            }

            // AI Analysis Heatmap
            const heatmapData = @json($aiAnalysisHeatmap);
            if (heatmapData && heatmapData.length > 0) {
                const heatmapOptions = {
                    series: [{ name: 'Jumlah Dokumen', data: heatmapData.map(item => item.count) }],
                    chart: { type: 'bar', height: 250, toolbar: { show: false } },
                    colors: ['#6366F1'],
                    plotOptions: { bar: { horizontal: true, distributed: true } },
                    xaxis: { categories: heatmapData.map(item => item.range) },
                    tooltip: { theme: 'dark' }
                };
                new ApexCharts(document.querySelector("#ai-heatmap-chart"), heatmapOptions).render();
            }

            // Criteria Progress Chart
            const criteriaData = @json($criteriaProgressData);
            if (criteriaData && criteriaData.length > 0) {
                const criteriaOptions = {
                    series: [{ name: 'Progress (%)', data: criteriaData.map(item => item.progress) }],
                    chart: { type: 'radar', height: 320, toolbar: { show: false } },
                    colors: ['#8B5CF6'],
                    xaxis: { categories: criteriaData.map(item => item.kriteria) },
                    yaxis: { min: 0, max: 100, tickAmount: 5 },
                    tooltip: { theme: 'dark' }
                };
                new ApexCharts(document.querySelector("#criteria-progress-chart"), criteriaOptions).render();
            }
        }
    </script>
</div>
