<x-app-layout>
    <div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Profile Header -->
            <div class="mb-10 flex flex-col md:flex-row items-center justify-between gap-6 bg-white dark:bg-slate-900 p-8 rounded-[2rem] shadow-sm border border-slate-100 dark:border-slate-800">
                <div class="flex flex-col md:flex-row items-center md:items-start md:space-x-8 text-center md:text-left">
                    <div class="relative mb-4 md:mb-0">
                        <div class="w-32 h-32 rounded-[2.5rem] overflow-hidden shadow-2xl ring-4 ring-indigo-50 dark:ring-indigo-900/20">
                            @if (Auth::user()->avatar)
                                <img src="{{ Storage::url(Auth::user()->avatar) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-4xl font-bold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-emerald-500 border-4 border-white dark:border-slate-900 rounded-full"></div>
                    </div>
                    <div class="pt-4">
                        <h1 class="text-3xl font-extrabold text-slate-800 dark:text-slate-100 mb-1">{{ Auth::user()->name }}</h1>
                        <p class="text-slate-500 dark:text-slate-400 font-medium mb-4">{{ Auth::user()->email }}</p>
                        <div class="flex flex-wrap justify-center md:justify-start gap-2">
                            <span class="inline-flex items-center px-4 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                {{ Auth::user()->role ?? 'User' }}
                            </span>
                            @if(Auth::user()->prodi && Auth::user()->role !== 'admin')
                            <span class="inline-flex items-center px-4 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-100 dark:border-amber-800">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011-1v5m-4 0h4"></path></svg>
                                {{ Auth::user()->prodi->nama ?? 'Prodi' }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div x-data="{ activeTab: 'general' }" class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Navigation -->
                <aside class="w-full lg:w-72">
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-[2rem] shadow-sm border border-slate-100 dark:border-slate-800 sticky top-8">
                        <nav class="space-y-2">
                            <button @click="activeTab = 'general'" 
                                :class="activeTab === 'general' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200 dark:shadow-indigo-900/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800'" 
                                class="w-full text-left px-5 py-4 rounded-2xl font-bold transition-all duration-300 flex items-center space-x-3 group">
                                <div :class="activeTab === 'general' ? 'bg-indigo-500' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30'" class="p-2 rounded-xl transition-colors">
                                    <svg class="w-5 h-5 transition-colors" :class="activeTab === 'general' ? 'text-white' : 'text-slate-500 group-hover:text-indigo-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <span>Informasi Akun</span>
                            </button>

                            <button @click="activeTab = 'password'" 
                                :class="activeTab === 'password' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200 dark:shadow-indigo-900/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800'" 
                                class="w-full text-left px-5 py-4 rounded-2xl font-bold transition-all duration-300 flex items-center space-x-3 group">
                                <div :class="activeTab === 'password' ? 'bg-indigo-500' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30'" class="p-2 rounded-xl transition-colors">
                                    <svg class="w-5 h-5 transition-colors" :class="activeTab === 'password' ? 'text-white' : 'text-slate-500 group-hover:text-indigo-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <span>Keamanan</span>
                            </button>

                            <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800">
                                <button @click="activeTab = 'danger'" 
                                    :class="activeTab === 'danger' ? 'bg-rose-600 text-white shadow-lg shadow-rose-200 dark:shadow-rose-900/30' : 'text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/10'" 
                                    class="w-full text-left px-5 py-4 rounded-2xl font-bold transition-all duration-300 flex items-center space-x-3 group">
                                    <div :class="activeTab === 'danger' ? 'bg-rose-500' : 'bg-rose-100 dark:bg-rose-900/30'" class="p-2 rounded-xl">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </div>
                                    <span>Hapus Akun</span>
                                </button>
                            </div>
                        </nav>
                    </div>
                </aside>

                <!-- Main Content Area -->
                <div class="flex-1">
                    <div x-show="activeTab === 'general'" 
                        x-transition:enter="transition ease-out duration-500" 
                        x-transition:enter-start="opacity-0 translate-y-8" 
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="bg-white dark:bg-slate-900 p-8 md:p-10 rounded-[2rem] shadow-sm border border-slate-100 dark:border-slate-800">
                        <livewire:profile.update-profile-information-form />
                    </div>

                    <div x-show="activeTab === 'password'" 
                        x-transition:enter="transition ease-out duration-500" 
                        x-transition:enter-start="opacity-0 translate-y-8" 
                        x-transition:enter-end="opacity-100 translate-y-0"
                        style="display: none;"
                        class="bg-white dark:bg-slate-900 p-8 md:p-10 rounded-[2rem] shadow-sm border border-slate-100 dark:border-slate-800">
                        <livewire:profile.update-password-form />
                    </div>

                    <div x-show="activeTab === 'danger'" 
                        x-transition:enter="transition ease-out duration-500" 
                        x-transition:enter-start="opacity-0 translate-y-8" 
                        x-transition:enter-end="opacity-100 translate-y-0"
                        style="display: none;"
                        class="bg-white dark:bg-slate-900 p-8 md:p-10 rounded-[2rem] shadow-sm border border-slate-100 dark:border-slate-800">
                        <livewire:profile.delete-user-form />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
