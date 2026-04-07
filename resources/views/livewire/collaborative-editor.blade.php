<div class="max-w-6xl mx-auto space-y-6">

    <!-- Editor Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $document->nama_file }}</h1>
                <div class="flex items-center space-x-4 text-sm text-slate-500">
                    <span>Kriteria: {{ $document->kriteria->nama }}</span>
                    <span>Dibuat: {{ $document->created_at->format('d M Y') }}</span>
                    @if($lastSaved)
                        <span class="text-green-600">Tersimpan: {{ $lastSaved->diffForHumans() }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-3">
            <!-- Save Button -->
            <button wire:click="saveDocument"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 disabled:opacity-50 transition-colors flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span wire:loading.remove>Simpan</span>
                <span wire:loading>Menyimpan...</span>
            </button>

            <!-- Lock/Unlock Button -->
            @if(!$isLocked || $lockedBy === Auth::id())
                <button wire:click="{{ $isLocked ? 'releaseLock' : 'requestLock' }}"
                        class="px-4 py-2 {{ $isLocked ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white rounded-lg focus:ring-2 focus:ring-blue-500 transition-colors flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isLocked ? 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z' : 'M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z' }}"></path>
                    </svg>
                    <span>{{ $isLocked ? 'Buka Kunci' : 'Kunci Dokumen' }}</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Document Lock Status -->
    @if($isLocked)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                        Dokumen dikunci oleh {{ Auth::user()->name }}
                    </p>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        Kunci akan otomatis terbuka pada {{ $lockExpiresAt ? $lockExpiresAt->format('H:i:s') : 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Active Users Panel -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-medium text-slate-900 dark:text-white">Pengguna Aktif</h3>
            <div class="flex items-center space-x-1">
                @foreach($activeUsers as $user)
                    <div class="relative">
                        <div class="w-8 h-8 {{ $user['avatar_color'] }} rounded-full flex items-center justify-center text-white font-bold text-xs border-2 border-white dark:border-slate-800">
                            {{ substr($user['user_name'], 0, 1) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 border-2 border-white dark:border-slate-800 rounded-full"></div>
                    </div>
                @endforeach
                @if(count($activeUsers) === 0)
                    <span class="text-sm text-slate-500">Tidak ada pengguna aktif</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Editor Interface -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        <!-- Main Editor -->
        <div class="lg:col-span-3">
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">

                <!-- Editor Toolbar -->
                <div class="border-b border-slate-200 dark:border-slate-700 p-3">
                    <div class="flex items-center space-x-2">
                        <button class="p-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded transition-colors"
                                title="Bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h12"></path>
                            </svg>
                        </button>
                        <button class="p-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded transition-colors"
                                title="Italic">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m-4-4h8"></path>
                            </svg>
                        </button>
                        <div class="w-px h-6 bg-slate-300 dark:bg-slate-600"></div>
                        <button class="p-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded transition-colors"
                                title="Bullet List">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                        </button>
                        <button class="p-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded transition-colors"
                                title="Numbered List">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Editor Content -->
                <div class="p-6">
                    <textarea
                        wire:model.debounce.500ms="content"
                        wire:keydown.enter.prevent
                        placeholder="Mulai menulis konten dokumen Anda..."
                        class="w-full h-96 p-4 border-0 focus:ring-0 focus:outline-none bg-transparent text-slate-900 dark:text-white resize-none font-mono text-sm leading-relaxed break-words"
                        style="min-height: 24rem; overflow-wrap: break-word; word-wrap: break-word;"
                        {{ $isLocked && $lockedBy !== Auth::id() ? 'readonly' : '' }}
                    ></textarea>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">

            <!-- Document Info -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Info Dokumen</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-slate-600 dark:text-slate-400">Nama File</label>
                        <p class="text-sm text-slate-900 dark:text-white">{{ $document->nama_file }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600 dark:text-slate-400">Kriteria</label>
                        <p class="text-sm text-slate-900 dark:text-white">{{ $document->kriteria->nama }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600 dark:text-slate-400">Status</label>
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                            @if($document->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                            @elseif($document->status === 'under_review') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                            @elseif($document->status === 'submitted') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                            @else bg-slate-100 text-slate-800 dark:bg-slate-900/30 dark:text-slate-400
                            @endif">
                            {{ ucfirst($document->status) }}
                        </span>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600 dark:text-slate-400">Terakhir Diedit</label>
                        <p class="text-sm text-slate-900 dark:text-white">
                            {{ $document->updated_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    <button class="w-full px-3 py-2 text-left text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-700 rounded transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export ke PDF
                    </button>
                    <button class="w-full px-3 py-2 text-left text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-700 rounded transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Copy ke Clipboard
                    </button>
                    <button class="w-full px-3 py-2 text-left text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-700 rounded transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                        Lihat Versi Sebelumnya
                    </button>
                </div>
            </div>

            <!-- Comments Summary -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Komentar</h3>
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600 dark:text-slate-400">Total</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $document->comments()->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600 dark:text-slate-400">Belum Terjawab</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $document->comments()->where('is_resolved', false)->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600 dark:text-slate-400">Terselesaikan</span>
                        <span class="font-medium text-green-600">{{ $document->comments()->where('is_resolved', true)->count() }}</span>
                    </div>
                </div>
                <button class="w-full mt-4 px-3 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 transition-colors">
                    Lihat Semua Komentar
                </button>
            </div>

            <!-- Version History -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Riwayat Versi
                </h3>

                @if($versionHistory && $versionHistory->count() > 0)
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @foreach($versionHistory->take(10) as $version)
                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700 rounded-lg">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-medium text-slate-900 dark:text-white">{{ $version->version_number }}</span>
                                        <span class="text-sm text-slate-500">oleh {{ $version->user->name }}</span>
                                    </div>
                                    <div class="text-sm text-slate-600 dark:text-slate-400">
                                        {{ $version->created_at->diffForHumans() }}
                                        @if($version->change_summary)
                                            • {{ $version->change_summary }}
                                        @endif
                                    </div>
                                </div>
                                <button wire:click="restoreVersion({{ $version->id }})"
                                        wire:confirm="Apakah Anda yakin ingin memulihkan dokumen ke versi ini?"
                                        class="px-3 py-1 text-sm bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-300 rounded hover:bg-slate-300 dark:hover:bg-slate-500 transition-colors">
                                    Pulihkan
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 flex space-x-2">
                        <button wire:click="createManualVersion"
                                class="flex-1 px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                            Buat Versi Baru
                        </button>
                        <button wire:click="$dispatch('open-version-modal')"
                                class="px-3 py-2 bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm rounded hover:bg-slate-300 dark:hover:bg-slate-500 transition-colors">
                            Lihat Semua
                        </button>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-slate-500">Belum ada versi tersimpan</p>
                        <button wire:click="createManualVersion"
                                class="mt-3 px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 transition-colors">
                            Buat Versi Pertama
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Auto-save Indicator -->
    <div x-data="{ showSaved: false }"
         x-show="showSaved"
         x-transition
         x-init="
            $wire.on('content-updated', () => {
                showSaved = true;
                setTimeout(() => showSaved = false, 2000);
            });
         "
         class="fixed bottom-6 right-6 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50">
        ✓ Tersimpan otomatis
    </div>

    <!-- Success/Error Messages -->
    @if(session()->has('message'))
        <div class="fixed top-6 right-6 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in">
            {{ session('message') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="fixed top-6 right-6 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in">
            {{ session('error') }}
        </div>
    @endif

</div>

<script>
document.addEventListener('livewire:loaded', function () {
    // Real-time presence updates
    setInterval(() => {
        @this.call('handleUserActivity', {{ Auth::id() }});
    }, 30000); // Update every 30 seconds

    // Listen for lock events
    Livewire.on('lock-acquired', () => {
        console.log('Document locked for editing');
    });

    Livewire.on('lock-released', () => {
        console.log('Document unlocked');
    });

    // Handle content updates from other users
    Livewire.on('content-updated', (data) => {
        console.log('Content updated by', data.user);
    });

    // Real-time broadcast listener
    if (window.Echo) {
        window.Echo.channel('document.{{ $document->id }}')
            .listen('.content.updated', (e) => {
                console.log('Broadcast received:', e);
                // Optionally refresh content or notify
                @this.call('refreshEditor');
            });
    }
});
</script>
