<!-- ApexCharts Script -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

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
            <p class="text-sm font-medium text-slate-500">Kriteria BAN-PT</p>
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
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 group-hover:scale-110 group-hover:rotate-6 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                </div>
                <span class="text-xs font-bold text-pink-500">PROGRESS</span>
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
                        <span class="w-2 h-2 rounded-full bg-pink-500 mr-2"></span>
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
                    Progress Kriteria BAN-PT
                </h3>
                <div id="criteria-progress-chart" class="h-80"></div>
            </div>
        </div>
            
            <!-- AI Insights Panel -->
            <div class="p-6 glass-card">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 mr-2 animate-pulse"></span>
                    Smart Insights
                </h3>
                <div class="space-y-4">
                    <div class="p-3 bg-indigo-50/50 dark:bg-indigo-900/10 border border-indigo-500/10 rounded-xl">
                        <p class="text-[11px] text-slate-600 dark:text-slate-400 font-medium">Kriteria C9 menunjukkan potensi skor maksimal tinggi berdasarkan analisis luaran 3 tahun terakhir.</p>
                    </div>
                    <div class="p-3 bg-amber-50/50 dark:bg-amber-900/10 border border-amber-500/10 rounded-xl">
                        <p class="text-[11px] text-slate-600 dark:text-slate-400 font-medium">Perlu perhatian pada Kriteria C4: Sumber Daya Manusia. Masih kekurangan bukti sertifikasi kompetensi dosen.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts JavaScript -->
    <script>
        document.addEventListener('livewire:loaded', function () {
            @this.on('refreshCharts', () => {
                initializeCharts();
            });

            initializeCharts();
        });

        function initializeCharts() {
            // Score Trend Chart
            const scoreTrendData = @json($scoreTrendData);
            if (scoreTrendData.length > 0) {
                const scoreTrendOptions = {
                    series: [{
                        name: 'Skor Rata-rata',
                        data: scoreTrendData.map(item => item.score)
                    }],
                    chart: {
                        type: 'line',
                        height: 250,
                        toolbar: { show: false },
                        background: 'transparent'
                    },
                    colors: ['#10B981'],
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    markers: {
                        size: 6,
                        colors: ['#10B981'],
                        strokeColors: '#fff',
                        strokeWidth: 2
                    },
                    xaxis: {
                        categories: scoreTrendData.map(item => item.month),
                        labels: {
                            style: {
                                colors: '#64748B'
                            }
                        }
                    },
                    yaxis: {
                        min: 0,
                        max: 4,
                        labels: {
                            style: {
                                colors: '#64748B'
                            }
                        }
                    },
                    grid: {
                        borderColor: '#E2E8F0',
                        strokeDashArray: 3
                    },
                    tooltip: {
                        theme: 'dark'
                    }
                };

                const scoreTrendChart = new ApexCharts(document.querySelector("#score-trend-chart"), scoreTrendOptions);
                scoreTrendChart.render();
            }

            // Status Distribution Chart
            const statusData = @json($statusDistributionData);
            if (statusData.length > 0) {
                const statusOptions = {
                    series: statusData.map(item => item.count),
                    chart: {
                        type: 'donut',
                        height: 250,
                        background: 'transparent'
                    },
                    labels: statusData.map(item => item.status),
                    colors: statusData.map(item => item.color),
                    legend: {
                        position: 'bottom',
                        labels: {
                            colors: '#64748B'
                        }
                    },
                    tooltip: {
                        theme: 'dark'
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%'
                            }
                        }
                    }
                };

                const statusChart = new ApexCharts(document.querySelector("#status-distribution-chart"), statusOptions);
                statusChart.render();
            }

            // Workflow Activity Chart
            const workflowData = @json($workflowActivityData);
            if (workflowData.length > 0) {
                const workflowOptions = {
                    series: [{
                        name: 'Aktivitas',
                        data: workflowData.map(item => item.activities)
                    }],
                    chart: {
                        type: 'bar',
                        height: 250,
                        toolbar: { show: false },
                        background: 'transparent'
                    },
                    colors: ['#EC4899'],
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '60%'
                        }
                    },
                    xaxis: {
                        categories: workflowData.map(item => item.date),
                        labels: {
                            style: {
                                colors: '#64748B'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#64748B'
                            }
                        }
                    },
                    grid: {
                        borderColor: '#E2E8F0',
                        strokeDashArray: 3
                    },
                    tooltip: {
                        theme: 'dark'
                    }
                };

                const workflowChart = new ApexCharts(document.querySelector("#workflow-activity-chart"), workflowOptions);
                workflowChart.render();
            }

            // AI Analysis Heatmap
            const heatmapData = @json($aiAnalysisHeatmap);
            if (heatmapData.length > 0) {
                const heatmapOptions = {
                    series: heatmapData.map(item => ({
                        name: item.range,
                        data: [item.count]
                    })),
                    chart: {
                        type: 'bar',
                        height: 250,
                        toolbar: { show: false },
                        background: 'transparent'
                    },
                    colors: heatmapData.map(item => item.color),
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            barHeight: '60%'
                        }
                    },
                    xaxis: {
                        categories: heatmapData.map(item => item.range),
                        labels: {
                            style: {
                                colors: '#64748B'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#64748B'
                            }
                        }
                    },
                    grid: {
                        borderColor: '#E2E8F0',
                        strokeDashArray: 3
                    },
                    tooltip: {
                        theme: 'dark'
                    }
                };

                const heatmapChart = new ApexCharts(document.querySelector("#ai-heatmap-chart"), heatmapOptions);
                heatmapChart.render();
            }

            // Criteria Progress Chart
            const criteriaData = @json($criteriaProgressData);
            if (criteriaData.length > 0) {
                const criteriaOptions = {
                    series: [{
                        name: 'Progress (%)',
                        data: criteriaData.map(item => item.progress)
                    }],
                    chart: {
                        type: 'radar',
                        height: 320,
                        toolbar: { show: false },
                        background: 'transparent'
                    },
                    colors: ['#8B5CF6'],
                    xaxis: {
                        categories: criteriaData.map(item => item.kriteria),
                        labels: {
                            style: {
                                colors: '#64748B'
                            }
                        }
                    },
                    yaxis: {
                        min: 0,
                        max: 100,
                        labels: {
                            style: {
                                colors: '#64748B'
                            }
                        }
                    },
                    markers: {
                        size: 4,
                        colors: ['#8B5CF6'],
                        strokeColors: '#fff',
                        strokeWidth: 2
                    },
                    tooltip: {
                        theme: 'dark',
                        y: {
                            formatter: function(val) {
                                return val + '%';
                            }
                        }
                    },
                    grid: {
                        borderColor: '#E2E8F0',
                        strokeDashArray: 3
                    }
                };

                const criteriaChart = new ApexCharts(document.querySelector("#criteria-progress-chart"), criteriaOptions);
                criteriaChart.render();
            }
        }
    </script>
</div>
