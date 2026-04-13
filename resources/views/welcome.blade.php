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
        <div
            class="absolute top-1/2 -right-40 w-[500px] h-[500px] bg-purple-500/10 rounded-full blur-[120px] animate-pulse">
        </div>
        <div class="absolute -bottom-20 left-1/3 w-80 h-80 bg-pink-500/10 rounded-full blur-[90px] animate-pulse"></div>
    </div>

    <!-- Navigation Bar (Glassmorphic) -->
    <nav class="nav-glass mx-4 mt-6 rounded-2xl max-w-7xl lg:mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div
                class="w-10 h-10 smart-gradient rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                    </path>
                </svg>
            </div>
            <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">AKRE <span
                    class="text-indigo-600 font-light ml-1">SMART</span></span>
        </div>

        <div class="flex items-center space-x-4">
            @auth
                <a href="{{ url('/dashboard') }}"
                    class="px-6 py-2.5 rounded-xl text-sm font-bold bg-white dark:bg-slate-800 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 hover:border-indigo-500 transition-all shadow-sm">Buka
                    Dashboard</a>
            @else
                <a href="{{ route('login') }}"
                    class="text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-indigo-600 transition-colors">Masuk</a>
                <a href="{{ route('register') }}"
                    class="px-6 py-2.5 rounded-xl text-sm font-extrabold bg-indigo-600 text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20">Mulai
                    Sekarang</a>
            @endauth
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="relative z-10 pt-20 pb-24 px-6 text-center lg:pt-32 lg:pb-40">
        <div class="max-w-4xl mx-auto">
            <span
                class="badge-smart bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 mb-6 flex-inline animate-bounce">AI-POWERED
                ACCREDITATION SYSTEM</span>
            <h1 class="text-5xl lg:text-7xl font-extrabold text-slate-900 dark:text-white leading-[1.1] mb-8">
                Akreditasi Perguruan Tinggi <br>
                <span class="text-transparent bg-clip-text smart-gradient">Terotomasi & Cerdas</span>
            </h1>
            <p class="text-lg lg:text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto mb-12 leading-relaxed">
                Ubah proses akreditasi Manual menjadi Digital. Gunakan Teknologi LLM (AI) untuk melakukan Penilaian
                Mandiri, Gap Analysis, dan Rekomendasi data secara otomatis.
            </p>

            <div
                class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6 mb-16">
                <a href="{{ route('login') }}"
                    class="w-full sm:w-auto px-10 py-5 smart-gradient text-white font-extrabold rounded-2xl shadow-xl shadow-indigo-500/30 transform hover:-translate-y-1 transition-all text-lg flex items-center justify-center">
                    <span>Mulai Manajemen Data</span>
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
                <a href="#features"
                    class="w-full sm:w-auto px-10 py-5 glass-card text-slate-900 dark:text-white font-bold rounded-2xl hover:bg-white/50 transition-all text-lg flex items-center justify-center border-slate-200 dark:border-slate-800">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Pelajari Alur Kerja
                </a>
            </div>

            <!-- Smart Prompt Interface (Restored & Enhanced) -->
            <div class="max-w-3xl mx-auto px-4 mb-20 animate-fade-in" x-data="{ query: '' }" style="animation-delay: 0.2s">
                <div class="relative group">
                    <div
                        class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-cyan-500 rounded-2xl blur opacity-25 group-hover:opacity-60 transition duration-700 group-hover:duration-300 group-hover:animate-pulse">
                    </div>
                    <form @submit.prevent="if(query) { $dispatch('trigger-chat', { message: query }); query = ''; }"
                        class="relative flex items-center bg-white dark:bg-slate-900 rounded-2xl p-2 shadow-2xl border border-slate-200 dark:border-slate-800 transform transition-all duration-300 group-hover:scale-[1.01]">
                        <div class="flex-shrink-0 ml-4 hidden sm:block">
                            <div
                                class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center transition-colors duration-300 group-hover:bg-indigo-600">
                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400 group-hover:text-white transition-colors duration-300" 
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <input x-model="query" type="text" placeholder="Tanya AI: 'Bagaimana cara mengisi butir LAM Teknik 1.A.5?'"
                            class="w-full bg-transparent border-none focus:ring-0 text-slate-900 dark:text-white px-4 py-4 text-lg placeholder-slate-400 dark:placeholder-slate-600 font-medium" />
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white p-3 rounded-xl transition-all duration-300 shadow-lg shadow-indigo-500/20 active:scale-90 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                    <div class="mt-4 flex flex-wrap justify-center gap-4 text-sm text-slate-500 opacity-0 animate-fade-in" style="animation-delay: 0.6s; animation-fill-mode: forwards;">
                        <span class="font-bold text-indigo-500">Populer:</span>
                        <button @click="$dispatch('trigger-chat', { message: 'Bagaimana cara menyusun Narasi LED yang baik?' })"
                            class="hover:text-indigo-600 transition-all duration-300 hover:-translate-y-0.5 underline decoration-indigo-500/30 underline-offset-4">Narasi
                            LED S1</button>
                        <button @click="$dispatch('trigger-chat', { message: 'Apa saja kriteria LAMEMBA?' })"
                            class="hover:text-indigo-600 transition-all duration-300 hover:-translate-y-0.5 underline decoration-indigo-500/30 underline-offset-4">Gap
                            Analysis LAMEMBA</button>
                        <button @click="$dispatch('trigger-chat', { message: 'Bagaimana cara upload dokumen bukti fisik?' })"
                            class="hover:text-indigo-600 transition-all duration-300 hover:-translate-y-0.5 underline decoration-indigo-500/30 underline-offset-4">Daftar
                            Dosen</button>
                    </div>
                </div>
            </div>

        <!-- Dashboard Preview Mockup (Enhanced) -->
        <div class="mt-10 lg:mt-24 max-w-6xl mx-auto px-4 animate-fade-in">
            <div
                class="glass-card p-2 rounded-[2.5rem] bg-gradient-to-tr from-white/30 to-indigo-500/10 backdrop-blur-3xl shadow-2xl overflow-hidden group border-white/20">
                <div
                    class="bg-slate-950 rounded-[2.2rem] aspect-[16/9] lg:aspect-[2/1.2] overflow-hidden relative border border-white/5 shadow-inner">
                    <div
                        class="absolute inset-x-0 top-0 h-12 bg-slate-900/80 backdrop-blur border-b border-white/5 flex items-center px-6 justify-between z-20">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 rounded-full bg-red-500/50"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500/50"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500/50"></div>
                        </div>
                        <div class="text-[10px] text-white/30 uppercase tracking-[0.2em] font-bold italic">AI Analytical
                            Core Engine v4.0</div>
                        <div class="w-4 h-4 rounded-full bg-indigo-500/20"></div>
                    </div>

                    <!-- Main Content Content Mockup -->
                    <div class="absolute inset-0 pt-20 px-8">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-white/5 rounded-2xl border border-white/10 p-5 backdrop-blur-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="text-[10px] font-bold text-white/40 uppercase tracking-widest">
                                        Confidence Score</div>
                                </div>
                                <div class="text-2xl font-black text-white mb-1">98.4%</div>
                                <div class="w-full h-1 bg-white/10 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500 w-[98%]"></div>
                                </div>
                            </div>
                            <div class="bg-white/5 rounded-2xl border border-white/10 p-5 backdrop-blur-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="text-[10px] font-bold text-white/40 uppercase tracking-widest">Process
                                        Time</div>
                                </div>
                                <div class="text-2xl font-black text-white mb-1">1.2s <span
                                        class="text-sm font-normal text-white/30">/ doc</span></div>
                                <div class="w-full h-1 bg-white/10 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500 w-1/3"></div>
                                </div>
                            </div>
                            <div class="bg-white/5 rounded-2xl border border-white/10 p-5 backdrop-blur-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center text-purple-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="text-[10px] font-bold text-white/40 uppercase tracking-widest">AI
                                        Reasoning</div>
                                </div>
                                <div class="text-2xl font-black text-white mb-1">Active</div>
                                <div class="flex space-x-1">
                                    @for($i = 0; $i < 5; $i++)
                                        <div class="w-3 h-1 bg-purple-500 animate-pulse"
                                    style="animation-delay: {{ $i * 0.2 }}s"></div> @endfor
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-white/5 rounded-2xl border border-white/10 p-6 flex-1 min-h-[160px] relative overflow-hidden">
                            <div class="flex items-center space-x-4 mb-6">
                                <div
                                    class="w-12 h-12 rounded-full bg-indigo-500 flex items-center justify-center shadow-lg shadow-indigo-500/40">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="h-2 w-32 bg-white/20 rounded-full mb-2"></div>
                                    <div class="h-3 w-64 bg-white/40 rounded-full"></div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="h-2 w-full bg-white/10 rounded-full"></div>
                                <div class="h-2 w-11/12 bg-white/10 rounded-full"></div>
                                <div class="h-2 w-4/5 bg-white/5 rounded-full"></div>
                            </div>

                            <div class="absolute bottom-4 right-6 flex items-center space-x-3">
                                <div
                                    class="px-3 py-1 bg-white/10 rounded-full text-[10px] text-white/60 font-bold border border-white/10">
                                    ANALYZING...</div>
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-indigo-500 border-t-transparent animate-spin">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-60 pointer-events-none">
                    </div>

                    <!-- Center Button overlay -->
                    <div
                        class="absolute inset-0 flex items-center justify-center z-40 bg-slate-950/20 backdrop-blur-[2px]">
                        <a href="{{ route('dashboard') }}" wire:navigate
                            class="bg-white text-slate-900 px-8 py-4 rounded-2xl font-black text-lg shadow-2xl transform hover:scale-105 transition-all flex items-center group">
                            <svg class="w-6 h-6 mr-3 text-indigo-600 group-hover:animate-spin" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                </path>
                            </svg>
                            Lihat Dashboard AI Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Smart Lifecycle Section -->
    <section class="py-32 relative overflow-hidden bg-white dark:bg-slate-950">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center gap-20">
                <div class="lg:w-1/2">
                    <div
                        class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-bold tracking-widest uppercase mb-6 border border-indigo-100 dark:border-indigo-800">
                        <span class="flex h-2 w-2 rounded-full bg-indigo-500 mr-2 animate-ping"></span>
                        Smart Workflow
                    </div>
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-slate-900 dark:text-white mb-8 leading-tight">
                        Akreditasi Tanpa <br><span class="text-indigo-600">Kerumitan Manual</span>
                    </h2>

                    <div class="space-y-10">
                        <div class="flex group">
                            <div class="flex-shrink-0 mr-6">
                                <div
                                    class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center font-black text-xl border border-indigo-100 dark:border-indigo-800 group-hover:bg-indigo-600 group-hover:text-white transition-all transform group-hover:rotate-6">
                                    01</div>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Unggah & Ekstraksi
                                    Cerdas</h3>
                                <p class="text-slate-500 dark:text-slate-400 leading-relaxed">Letakkan dokumen standar
                                    Anda. AI kami akan secara otomatis memilah data dosen, mahasiswa, dan kurikulum
                                    tanpa input manual.</p>
                            </div>
                        </div>
                        <div class="flex group">
                            <div class="flex-shrink-0 mr-6">
                                <div
                                    class="w-14 h-14 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 rounded-2xl flex items-center justify-center font-black text-xl border border-purple-100 dark:border-purple-800 group-hover:bg-purple-600 group-hover:text-white transition-all transform group-hover:rotate-6">
                                    02</div>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Generasi Narasi LED
                                </h3>
                                <p class="text-slate-500 dark:text-slate-400 leading-relaxed">Berhenti menghabiskan
                                    waktu berbulan-bulan menulis narasi. AI menyusun draf laporan evaluasi diri yang
                                    koheren dalam hitungan detik.</p>
                            </div>
                        </div>
                        <div class="flex group">
                            <div class="flex-shrink-0 mr-6">
                                <div
                                    class="w-14 h-14 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center font-black text-xl border border-emerald-100 dark:border-emerald-800 group-hover:bg-emerald-600 group-hover:text-white transition-all transform group-hover:rotate-6">
                                    03</div>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Rekomendasi Skor
                                    Maksimal</h3>
                                <p class="text-slate-500 dark:text-slate-400 leading-relaxed">Dapatkan masukan spesifik
                                    untuk setiap butir instrumen yang belum optimal agar Anda bisa mencapai akreditasi
                                    Unggul.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:w-1/2 relative">
                    <!-- Abstract Visual Representative of AI Intelligence -->
                    <div class="relative py-8">
                        <div class="absolute inset-0 bg-indigo-500/10 dark:bg-indigo-600/5 blur-[120px] rounded-full">
                        </div>
                        <div
                            class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[3rem] p-8 shadow-2xl backdrop-blur-3xl overflow-hidden">
                            <div
                                class="flex items-center justify-between mb-8 border-b border-slate-100 dark:border-slate-800 pb-6">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 smart-gradient rounded-xl flex items-center justify-center text-white font-bold">
                                        A</div>
                                    <div class="text-sm font-bold text-slate-900 dark:text-white">AI Assistant Insight
                                    </div>
                                </div>
                                <div class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></div>
                            </div>

                            <div class="space-y-6">
                                <div class="flex items-start space-x-4">
                                    <div
                                        class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex-shrink-0 flex items-center justify-center text-slate-400">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path>
                                        </svg>
                                    </div>
                                    <div
                                        class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl rounded-tl-none p-4 text-sm text-slate-600 dark:text-slate-400 border border-slate-100 dark:border-slate-700 leading-relaxed">
                                        "Tolong analisis Tabel 3.1 tentang Kualifikasi Dosen S1 Informatika."
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4 flex-row-reverse space-x-reverse">
                                    <div
                                        class="w-8 h-8 rounded-full smart-gradient flex-shrink-0 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                    </div>
                                    <div
                                        class="bg-indigo-600 rounded-2xl rounded-tr-none p-4 text-sm text-white shadow-xl shadow-indigo-500/20 border border-indigo-500 leading-relaxed">
                                        "Berdasarkan data yang diunggah, rasio dosen berpendidikan S3 saat ini adalah
                                        45%. Untuk mencapai skor 4, Anda perlu menambah 2 dosen bergelar Doktor lagi
                                        atau meningkatkan kualifikasi dosen yang ada."
                                    </div>
                                </div>

                                <div class="pt-4">
                                    <div
                                        class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 px-1 text-center">
                                        Auto-generated Action</div>
                                    <button
                                        class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-emerald-500/20 flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12">
                                            </path>
                                        </svg>
                                        Ekspor Rekomendasi ke PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section id="features" class="py-24 px-6 max-w-7xl mx-auto border-t border-slate-100 dark:border-slate-900">
        <div class="text-center mb-16">
            <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900 dark:text-white mb-4">Fitur Smart
                Accreditation</h2>
            <p class="text-slate-500 max-w-xl mx-auto">Dirancang untuk memudahkan Asesor dan Institusi dalam
                mempersiapkan instrumen akreditasi.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- AI Engine -->
            <div class="p-8 glass-card border-none hover:bg-white dark:hover:bg-slate-900 transition-all">
                <div
                    class="w-14 h-14 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-3 text-glow">AI Assessment</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Analisis kuantitatif dan kualitatif dokumen unggahan
                    Anda menggunakan model LLM Tercanggih secara otomatis.</p>
            </div>

            <!-- Monitoring -->
            <div class="p-8 glass-card border-none hover:bg-white dark:hover:bg-slate-900 transition-all">
                <div
                    class="w-14 h-14 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-3 text-glow">Real-time Monitoring
                </h3>
                <p class="text-slate-500 text-sm leading-relaxed">Pantau dashboard interaktif untuk melihat progres
                    pemenuhan 9 standar kriteria secara instan.</p>
            </div>

            <!-- PDF Export -->
            <div class="p-8 glass-card border-none hover:bg-white dark:hover:bg-slate-900 transition-all">
                <div
                    class="w-14 h-14 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-3 text-glow">Smart Reports</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Unduh laporan resmi dalam format PDF yang berisi
                    ringkasan skor dan gap analysis untuk persiapan akreditasi.</p>
            </div>
        </div>
    </section>

    <!-- Smart Statistics Section -->
    <section class="py-24 bg-slate-50 dark:bg-slate-950/50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl lg:text-5xl font-black text-slate-900 dark:text-white mb-2">
                        {{ $stats['docs'] }}</div>
                    <div class="text-sm text-slate-500 font-bold uppercase tracking-widest">Dokumen Terupload</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl lg:text-5xl font-black text-indigo-600 mb-2">99.8%</div>
                    <div class="text-sm text-slate-500 font-bold uppercase tracking-widest">Akurasi AI</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl lg:text-5xl font-black text-slate-900 dark:text-white mb-2">
                        {{ $stats['prodis'] }}</div>
                    <div class="text-sm text-slate-500 font-bold uppercase tracking-widest">Program Studi</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl lg:text-5xl font-black text-emerald-500 mb-2">{{ $stats['categories'] }}</div>
                    <div class="text-sm text-slate-500 font-bold uppercase tracking-widest">Jenis Akreditasi</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Floating AI Assistant Bubble (Functional) -->
    <livewire:chat-assistant />

    <!-- CSS Animation Utilities -->
    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.8s ease-out forwards;
        }

        .animate-duration-1000 {
            animation-duration: 1s;
        }

        .animate-duration-3000 {
            animation-duration: 3s;
        }

        @keyframes hover {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        .animate-hover {
            animation: hover 4s ease-in-out infinite;
        }

        .glass-card-sm {
            @apply bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl shadow-xl;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #1e293b;
        }
    </style>

</body>

</html>