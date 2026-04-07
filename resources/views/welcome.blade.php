<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistem Akreditasi Cerdas (AKRE)</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=Outfit:400,500,600,700,800&display=swap" rel="stylesheet" />
        <!-- Scripts/Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-[Outfit] bg-slate-50 dark:bg-slate-950 overflow-x-hidden">
        
        <!-- Background Decorations -->
        <div class="fixed inset-0 pointer-events-none -z-10 overflow-hidden">
            <div class="absolute -top-24 -left-20 w-96 h-96 bg-indigo-500/10 rounded-full blur-[100px] animate-pulse"></div>
            <div class="absolute top-1/2 -right-40 w-[500px] h-[500px] bg-purple-500/10 rounded-full blur-[120px] animate-pulse"></div>
            <div class="absolute -bottom-20 left-1/3 w-80 h-80 bg-pink-500/10 rounded-full blur-[90px] animate-pulse"></div>
        </div>

        <!-- Navigation Bar (Glassmorphic) -->
        <nav class="nav-glass mx-4 mt-6 rounded-2xl max-w-7xl lg:mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 smart-gradient rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">AKRE <span class="text-indigo-600 font-light ml-1">SMART</span></span>
            </div>
            
            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 rounded-xl text-sm font-bold bg-white dark:bg-slate-800 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 hover:border-indigo-500 transition-all shadow-sm">Buka Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-indigo-600 transition-colors">Masuk</a>
                    <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-xl text-sm font-extrabold bg-indigo-600 text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20">Mulai Sekarang</a>
                @endauth
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="relative z-10 pt-20 pb-24 px-6 text-center lg:pt-32 lg:pb-40">
            <div class="max-w-4xl mx-auto">
                <span class="badge-smart bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 mb-6 flex-inline animate-bounce">AI-POWERED ACCREDITATION SYSTEM</span>
                <h1 class="text-5xl lg:text-7xl font-extrabold text-slate-900 dark:text-white leading-[1.1] mb-8">
                    Akreditasi Perguruan Tinggi <br>
                    <span class="text-transparent bg-clip-text smart-gradient">Terotomasi & Cerdas</span>
                </h1>
                <p class="text-lg lg:text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto mb-12 leading-relaxed">
                    Ubah proses akreditasi Manual menjadi Digital. Gunakan Teknologi LLM (AI) untuk melakukan Penilaian Mandiri, Gap Analysis, dan Rekomendasi data secara otomatis.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-10 py-5 smart-gradient text-white font-extrabold rounded-2xl shadow-xl shadow-indigo-500/30 transform hover:-translate-y-1 transition-all text-lg">Mulai Manajemen Data</a>
                    <a href="#features" class="w-full sm:w-auto px-10 py-5 glass-card text-slate-900 dark:text-white font-bold rounded-2xl hover:bg-white/50 transition-all text-lg">Pelajari Alur Kerja</a>
                </div>
            </div>

            <!-- Dashboard Preview Mockup -->
            <div class="mt-20 lg:mt-32 max-w-6xl mx-auto px-4 animate-fade-in animate-duration-1000">
                <div class="glass-card p-2 rounded-[2.5rem] bg-gradient-to-tr from-white/30 to-indigo-500/10 backdrop-blur-3xl shadow-2xl overflow-hidden group">
                    <div class="bg-slate-900 rounded-[2.2rem] aspect-[16/9] lg:aspect-[2/1] overflow-hidden relative border border-white/10">
                        <div class="absolute inset-0 bg-cover bg-center opacity-30 mix-blend-overlay group-hover:scale-105 transition-transform duration-1000" style="background-image: url('https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2070&auto=format&fit=crop');"></div>
                        
                        <!-- Floating Glass UI elements overlay -->
                        <div class="absolute top-10 left-10 w-48 h-24 glass-card-sm border-white/5 animate-hover"></div>
                        <div class="absolute bottom-10 right-10 w-64 h-32 glass-card-sm border-white/5 animate-hover animate-duration-3000"></div>
                        
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-lg mb-4 mx-auto animate-pulse">
                                    <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"></path></svg>
                                </div>
                                <span class="text-white/60 text-sm font-medium tracking-widest uppercase">Lihat Demonstrasi Cerdas</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Features Grid -->
        <section id="features" class="py-24 px-6 max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900 dark:text-white mb-4">Fitur Smart Accreditation</h2>
                <p class="text-slate-500 max-w-xl mx-auto">Dirancang untuk memudahkan Asesor dan Institusi dalam mempersiapkan instrumen akreditasi.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- AI Engine -->
                <div class="p-8 glass-card border-none hover:bg-white dark:hover:bg-slate-900 transition-all">
                    <div class="w-14 h-14 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-3 text-glow">AI Assessment</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Analisis kuantitatif dan kualitatif dokumen unggahan Anda menggunakan model LLM Tercanggih secara otomatis.</p>
                </div>

                <!-- Monitoring -->
                <div class="p-8 glass-card border-none hover:bg-white dark:hover:bg-slate-900 transition-all">
                    <div class="w-14 h-14 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-3 text-glow">Real-time Monitoring</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Pantau dashboard interaktif untuk melihat progres pemenuhan 9 standar kriteria secara instan.</p>
                </div>

                <!-- PDF Export -->
                <div class="p-8 glass-card border-none hover:bg-white dark:hover:bg-slate-900 transition-all">
                    <div class="w-14 h-14 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-3 text-glow">Smart Reports</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Unduh laporan resmi dalam format PDF yang berisi ringkasan skor dan gap analysis untuk persiapan akreditasi.</p>
                </div>
            </div>
        </section>

        <!-- CSS Animation Utilities -->
        <style>
            @keyframes fade-in { from { opacity: 0; transform: translateY(20px); } }
            .animate-fade-in { animation: fade-in 0.8s ease-out forwards; }
            .animate-duration-1000 { animation-duration: 1s; }
            .animate-duration-3000 { animation-duration: 3s; }
            @keyframes hover { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }
            .animate-hover { animation: hover 4s ease-in-out infinite; }
            .glass-card-sm { @apply bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl shadow-xl; }
        </style>

    </body>
</html>
