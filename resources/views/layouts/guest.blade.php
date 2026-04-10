<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Outfit:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-[Outfit] text-slate-900 dark:text-white antialiased bg-slate-50 dark:bg-slate-950">
    @php
        $settings = \App\Models\Setting::first();
    @endphp
    <div class="min-h-screen flex flex-col justify-center items-center py-12 px-6 lg:px-8 relative overflow-hidden">
        <!-- Background Decorations -->
        <div class="absolute inset-0 -z-10 group">
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-blue-500/5 rounded-full blur-[120px] transition-all group-hover:bg-blue-500/10">
            </div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-500/10 rounded-full blur-[80px]"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-cyan-500/10 rounded-full blur-[80px]"></div>
        </div>

        <div class="w-full max-w-md relative z-10">
            <div class="mb-10 text-center animate-fade-in">
                <a href="/" wire:navigate class="flex flex-col items-center group transition-all">
                    <div class="mb-6 relative">
                        @if($settings && $settings->logo_path)
                            <img src="{{ asset('storage/' . $settings->logo_path) }}"
                                class="h-20 w-auto object-contain filter drop-shadow-2xl transition-transform group-hover:scale-110">
                        @else
                            <div
                                class="w-16 h-16 smart-gradient rounded-3xl flex items-center justify-center shadow-2xl shadow-blue-500/20 group-hover:scale-110 transition-transform">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <h2
                        class="text-3xl font-black tracking-tighter text-transparent bg-clip-text smart-gradient uppercase">
                        AKRE SMART</h2>
                    @if($settings)
                        <p class="mt-2 text-xs font-bold text-slate-400 dark:text-slate-500 tracking-[0.2em] uppercase">
                            {{ $settings->nama_institusi }}</p>
                    @endif
                </a>
            </div>

            <div
                class="glass-card p-10 border-white/40 dark:border-slate-800 shadow-2xl animate-fade-in animate-duration-1000 relative overflow-hidden backdrop-blur-2xl">
                <div class="absolute top-0 left-0 w-full h-1 smart-gradient opacity-60"></div>
                {{ $slot }}
            </div>

            <p class="mt-10 text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                &copy; {{ date('Y') }} {{ $settings->nama_institusi ?? 'Sistem Akreditasi' }} • DEVELOPED BY DAEROBBY
            </p>
        </div>
    </div>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.6s ease-out forwards;
        }

        .animate-duration-1000 {
            animation-duration: 1s;
        }
    </style>
</body>

</html>