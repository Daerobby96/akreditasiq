<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public $selectedProdiId;
    public $prodis;
    public $installPrompt = null;
    public $showInstallPrompt = false;

    public $logo_path;
    public $nama_institusi;

    public function mount()
    {
        $this->prodis = \App\Models\Prodi::all();
        $this->selectedProdiId = session('selected_prodi_id', optional($this->prodis->first())->id);
        if ($this->selectedProdiId) {
            session(['selected_prodi_id' => $this->selectedProdiId]);
        }

        $setting = \App\Models\Setting::first();
        if ($setting) {
            $this->logo_path = $setting->logo_path;
            $this->nama_institusi = $setting->nama_institusi;
        }
    }

    public function selectProdi($id)
    {
        session(['selected_prodi_id' => $id]);
        $this->selectedProdiId = $id;
        $this->redirect(request()->header('Referer'), navigate: true);
    }

    public function installPWA()
    {
        $this->showInstallPrompt = false;
        // PWA install logic will be handled by JavaScript
    }

    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false, showInstallPrompt: @entangle('showInstallPrompt') }" class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200/50 dark:border-slate-800/50 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center space-x-3 mr-8 group">
                    <div class="w-10 h-10 flex items-center justify-center transition-transform group-hover:scale-110">
                        @if($logo_path)
                            <img src="{{ asset('storage/' . $logo_path) }}" class="w-full h-full object-contain filter drop-shadow-sm">
                        @else
                            <div class="w-9 h-9 smart-gradient rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-black bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-sky-500 dark:from-blue-400 dark:to-sky-400 tracking-tighter leading-none uppercase">AKRE SMART</span>
                        @if($nama_institusi)
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 tracking-tight mt-0.5 line-clamp-1 max-w-[120px]">{{ $nama_institusi }}</span>
                        @endif
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <div class="hidden lg:flex items-center space-x-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <!-- Dropdown: Instrumen -->
                    <div class="relative inline-flex items-center" x-data="{ instrumen: false }">
                        <button @click="instrumen = !instrumen" @click.outside="instrumen = false"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-5 transition
                            {{ request()->routeIs('lkps') || request()->routeIs('led') ? 'text-indigo-600 border-b-2 border-indigo-500' : 'text-slate-500 hover:text-indigo-600' }}">
                            Instrumen
                            <svg class="ms-1 w-4 h-4 transition-transform" :class="{ 'rotate-180': instrumen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="instrumen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute top-full left-0 mt-1 w-56 rounded-xl bg-white dark:bg-slate-900 shadow-xl border border-slate-200 dark:border-slate-800 py-2 z-50" style="display:none">
                            <a href="{{ route('lkps') }}" wire:navigate class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-600 transition-colors">
                                <div class="font-bold">Data LKPS</div>
                                <div class="text-[10px] text-slate-400">Kinerja Kuantitatif</div>
                            </a>
                            <a href="{{ route('led') }}" wire:navigate class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-600 transition-colors">
                                <div class="font-bold">Narasi LED</div>
                                <div class="text-[10px] text-slate-400">Evaluasi Diri Kualitatif</div>
                            </a>
                            <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                            <a href="{{ route('data-dukung') }}" wire:navigate class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-600 transition-colors">
                                <div class="font-bold">Database Bukti Fisik</div>
                                <div class="text-[10px] text-slate-400">Upload & Manajemen Dokumen</div>
                            </a>
                        </div>
                    </div>

                    @if(in_array(auth()->user()->role, ['admin', 'asesor', 'user']))
                    <x-nav-link :href="route('ai-audit')" :active="request()->routeIs('ai-audit')" wire:navigate>
                        {{ __('Audit AI') }}
                    </x-nav-link>
                    @endif

                    <x-nav-link :href="route('monitoring')" :active="request()->routeIs('monitoring')" wire:navigate>
                        {{ __('Monitoring') }}
                    </x-nav-link>

                    @if(in_array(auth()->user()->role, ['admin', 'user']))
                    <!-- Dropdown: Templates & Collaboration -->
                    <div class="relative inline-flex items-center" x-data="{ collab: false }">
                        <button @click="collab = !collab" @click.outside="collab = false"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-5 transition
                            {{ request()->routeIs('templates*') || request()->routeIs('documents.edit') ? 'text-indigo-600 border-b-2 border-indigo-500' : 'text-slate-500 hover:text-indigo-600' }}">
                            Kolaborasi
                            <svg class="ms-1 w-4 h-4 transition-transform" :class="{ 'rotate-180': collab }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="collab" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute top-full left-0 mt-1 w-64 rounded-xl bg-white dark:bg-slate-900 shadow-xl border border-slate-200 dark:border-slate-800 py-2 z-50" style="display:none">
                            <a href="{{ route('templates') }}" wire:navigate class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-600 transition-colors">
                                <div class="font-bold flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Template Dokumen
                                </div>
                                <div class="text-[10px] text-slate-400">Auto-fill & versioning</div>
                            </a>
                            @if(auth()->user()->role == 'admin')
                            <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                            <a href="{{ route('team-management') }}" wire:navigate class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-600 transition-colors">
                                <div class="font-bold flex items-center text-emerald-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    Manajemen Tim
                                </div>
                                <div class="text-[10px] text-slate-400">Atur anggota & hak akses</div>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if(auth()->user()->role == 'admin')
                    <!-- Dropdown: Master -->
                    <div class="relative inline-flex items-center" x-data="{ master: false }">
                        <button @click="master = !master" @click.outside="master = false"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-5 transition
                            {{ request()->routeIs('prodi') ? 'text-indigo-600 border-b-2 border-indigo-500' : 'text-slate-500 hover:text-indigo-600' }}">
                            Master
                            <svg class="ms-1 w-4 h-4 transition-transform" :class="{ 'rotate-180': master }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="master" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute top-full left-0 mt-1 w-56 rounded-xl bg-white dark:bg-slate-900 shadow-xl border border-slate-200 dark:border-slate-800 py-2 z-50" style="display:none">
                            <a href="{{ route('prodi') }}" wire:navigate class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-600 transition-colors">
                                <div class="font-bold">Manajemen Prodi</div>
                                <div class="text-[10px] text-slate-400">Kelola & Mapping LAM</div>
                            </a>
                            <a href="{{ route('kriteria') }}" wire:navigate class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-600 transition-colors">
                                <div class="font-bold">Manajemen Kriteria</div>
                                <div class="text-[10px] text-slate-400">Atur Standar BAN-PT/LAM</div>
                            </a>
                            <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                            <a href="{{ route('instrument-setting') }}" wire:navigate class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-600 transition-colors">
                                <div class="font-bold">Pengaturan Instrumen</div>
                                <div class="text-[10px] text-slate-400">Custom Tabel & Header LKPS</div>
                            </a>
                            <a href="{{ route('settings') }}" wire:navigate class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-600 transition-colors">
                                <div class="font-bold">Identitas Kampus</div>
                                <div class="text-[10px] text-slate-400">Nama, Logo & Profil Institusi</div>
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right Side: User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-3">
                <!-- Prodi Switcher (Moved here) -->
                <div class="hidden md:flex items-center pr-4 border-r border-slate-200 dark:border-slate-800 h-8">
                    <x-dropdown align="right" width="64">
                        <x-slot name="trigger">
                            <button class="flex items-center space-x-2 px-3 py-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all group">
                                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                <div class="text-left">
                                    @php $currentProdi = $prodis->firstWhere('id', $selectedProdiId); @endphp
                                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-tighter leading-none">Program Studi</div>
                                    <div class="text-xs font-bold text-slate-700 dark:text-slate-200 leading-tight">{{ \Illuminate\Support\Str::limit($currentProdi->nama ?? 'Pilih Prodi...', 20) }}</div>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="px-4 py-2 text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-800 mb-1">Pindah Dashboard Prodi</div>
                            @foreach($prodis as $p)
                                <button wire:click="selectProdi({{ $p->id }})" class="w-full text-left px-4 py-2.5 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors group">
                                    <div class="text-sm font-bold {{ $selectedProdiId == $p->id ? 'text-indigo-600' : 'text-slate-700 dark:text-slate-300' }}">{{ $p->nama }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium uppercase tracking-tight">{{ $p->jenjang }} • {{ strtoupper($p->lam_type) }}</div>
                                </button>
                            @endforeach
                        </x-slot>
                    </x-dropdown>
                </div>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 bg-slate-100/80 dark:bg-slate-800/80 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                            <div class="w-6 h-6 rounded-full smart-gradient flex items-center justify-center mr-2">
                                <span class="text-white text-[10px] font-black">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            </div>
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                            <svg class="ms-1 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>{{ __('Profile') }}</x-dropdown-link>
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>{{ __('Log Out') }}</x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Hamburger -->
            <div class="-me-2 flex items-center lg:hidden">
                <button @click="open = !open" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-all">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden lg:hidden border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
        <!-- Mobile Status Bar -->
        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-end">

                <!-- PWA Install Button -->
                <button x-show="showInstallPrompt"
                        x-on:click="$wire.installPWA()"
                        class="px-3 py-1 bg-indigo-600 text-white text-xs rounded-full hover:bg-indigo-700 transition-colors">
                    Install App
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Links -->
        <div class="py-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                </svg>
                Dashboard
            </x-responsive-nav-link>

            <!-- Mobile Prodi Switcher -->
            <div class="px-4 py-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Program Studi</label>
                <select wire:model.live="selectedProdiId" wire:change="selectProdi($event.target.value)"
                        class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm">
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}" {{ $selectedProdiId == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Mobile Navigation Sections -->
            <div class="border-t border-slate-200 dark:border-slate-800 mt-2">
                <div class="px-4 py-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Instrumen</span>
                </div>
                <x-responsive-nav-link :href="route('lkps')" :active="request()->routeIs('lkps')" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Data LKPS
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('led')" :active="request()->routeIs('led')" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Narasi LED
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('data-dukung')" :active="request()->routeIs('data-dukung')" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                    </svg>
                    Database Bukti Fisik
                </x-responsive-nav-link>
            </div>

            @if(in_array(auth()->user()->role, ['admin', 'user']))
            <!-- Collaboration Section -->
            <div class="border-t border-slate-200 dark:border-slate-800">
                <div class="px-4 py-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Kolaborasi</span>
                </div>
                <x-responsive-nav-link :href="route('templates')" :active="request()->routeIs('templates*')" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Template Dokumen
                </x-responsive-nav-link>
                @if(auth()->user()->role == 'admin')
                <x-responsive-nav-link :href="route('team-management')" :active="request()->routeIs('team-management')" wire:navigate>
                    <svg class="w-5 h-5 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Manajemen Tim
                </x-responsive-nav-link>
                @endif
            </div>
            @endif

            <!-- Tools Section -->
            <div class="border-t border-slate-200 dark:border-slate-800">
                <div class="px-4 py-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Tools</span>
                </div>
                @if(in_array(auth()->user()->role, ['admin', 'asesor', 'user']))
                <x-responsive-nav-link :href="route('ai-audit')" :active="request()->routeIs('ai-audit')" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Audit AI & Scoring
                </x-responsive-nav-link>
                @endif
                <x-responsive-nav-link :href="route('monitoring')" :active="request()->routeIs('monitoring')" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Monitoring
                </x-responsive-nav-link>
            </div>
        </div>

        <!-- Mobile User Section -->
        <div class="border-t border-slate-200 dark:border-slate-800 px-4 py-4">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="font-bold text-slate-900 dark:text-white" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                    <div class="text-sm text-slate-500">{{ auth()->user()->email }}</div>
                </div>
            </div>
            <div class="space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profile
                </x-responsive-nav-link>
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Log Out
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>