<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in space-y-6">
    <!-- Header Section: More Compact & Balanced -->
    <div class="relative overflow-hidden rounded-[2rem] bg-indigo-600 p-6 sm:p-10 shadow-2xl shadow-indigo-500/20">
        <div class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-white/10 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-indigo-400/20 rounded-full blur-3xl opacity-50"></div>
        
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center space-x-6">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-xl rounded-2xl flex items-center justify-center shadow-inner border border-white/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-white tracking-tight leading-none mb-2">Template Dokumen</h1>
                    <p class="text-indigo-100 text-sm font-medium opacity-90 tracking-wide">Solusi cerdas manajemen dokumen akreditasi</p>
                </div>
            </div>

            <button wire:click="$set('showCreateModal', true)"
                    class="group relative inline-flex items-center justify-center px-8 py-3.5 font-bold text-indigo-600 transition-all bg-white rounded-2xl hover:bg-indigo-50 shadow-xl hover:shadow-indigo-400/20 active:scale-95">
                <svg class="w-5 h-5 mr-2.5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="text-xs tracking-widest uppercase">Buat Template</span>
            </button>
        </div>
    </div>

    <!-- Smart Filter & Search Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-2">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
            <!-- Search -->
            <div class="md:col-span-5 relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari template..."
                       class="w-full pl-11 pr-4 py-3 bg-transparent border-none rounded-xl focus:ring-0 text-slate-700 dark:text-slate-200 placeholder-slate-400 font-medium text-sm">
            </div>

            <div class="md:col-span-7 flex flex-wrap md:flex-nowrap items-center gap-2">
                <!-- Status Filter -->
                <div class="flex-1 min-w-[120px]">
                    <select wire:model.live="filterStatus"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-indigo-500/10 text-slate-700 dark:text-slate-200 font-medium text-sm appearance-none cursor-pointer">
                        <option value="all">Semua Status</option>
                        <option value="draft">📁 Draft</option>
                        <option value="published">🚀 Published</option>
                        <option value="archived">📦 Archived</option>
                    </select>
                </div>

                <!-- Kriteria Filter -->
                <div class="flex-1 min-w-[150px]">
                    <select wire:model.live="filterKriteria"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-indigo-500/10 text-slate-700 dark:text-slate-200 font-medium text-sm appearance-none cursor-pointer">
                        <option value="all">Semua Kriteria</option>
                        @foreach($kriteriaOptions as $kriteria)
                            <option value="{{ $kriteria->id }}">{{ $kriteria->kode }} • {{ Str::limit($kriteria->nama, 15) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort -->
                <div class="flex-1 min-w-[120px]">
                    <select wire:model.live="sortBy"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-indigo-500/10 text-slate-700 dark:text-slate-200 font-medium text-sm appearance-none cursor-pointer">
                        <option value="created_at">⏰ Terbaru</option>
                        <option value="name">🔤 Nama</option>
                        <option value="usage_count">🔥 Terpopuler</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates Dynamic Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
            <div class="group flex flex-col bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-300 hover:-translate-y-1.5 overflow-hidden">
                
                <!-- Preview Canvas -->
                <div class="relative aspect-[16/9] bg-slate-100 dark:bg-slate-800 border-b border-slate-100 dark:border-slate-800">
                    @if($template->getPreviewUrl())
                        <img src="{{ $template->getPreviewUrl() }}" alt="{{ $template->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center p-6 text-center">
                            <div class="w-12 h-12 bg-white dark:bg-slate-700 rounded-xl shadow-sm flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No Preview Available</span>
                        </div>
                    @endif

                    <!-- Status Tag -->
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg shadow-sm backdrop-blur-md border 
                            @if($template->status === 'published') bg-emerald-500/90 border-emerald-400 text-white
                            @elseif($template->status === 'draft') bg-amber-500/90 border-amber-400 text-white
                            @else bg-slate-500/90 border-slate-400 text-white
                            @endif">
                            {{ $template->status }}
                        </span>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="p-6 flex-1 flex flex-col">
                    <div class="mb-4">
                        <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest mb-1.5">{{ $template->kriteria->kode }} • {{ Str::limit($template->kriteria->nama, 30) }}</p>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight group-hover:text-indigo-600 transition-colors line-clamp-1 italic">
                            {{ $template->name }}
                        </h3>
                    </div>

                    @if($template->description)
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed mb-6 italic opacity-80">
                            "{{ $template->description }}"
                        </p>
                    @endif

                    <div class="mt-auto flex items-center justify-between pt-5 border-t border-slate-50 dark:border-slate-800">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-[10px] font-black" title="Used {{ $template->usage_count }} times">
                                {{ $template->usage_count }}
                            </div>
                            <div class="text-[10px] font-medium text-slate-400 uppercase tracking-widest">Digital Usage</div>
                        </div>

                        <div class="flex items-center space-x-2">
                             @if($template->isPublished() || $template->creator->id === Auth::id() || Auth::user()->role === 'admin')
                                <button wire:click="useTemplate({{ $template->id }})"
                                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-indigo-500/10 active:scale-95">
                                    PAKAI
                                </button>
                            @endif

                            @if($template->creator->id === Auth::id() || Auth::user()->role === 'admin')
                                <div class="flex items-center bg-slate-50 dark:bg-slate-800/50 p-1 rounded-xl">
                                    <button wire:click="editTemplate({{ $template->id }})" class="p-2 text-slate-400 hover:text-indigo-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 flex flex-col items-center justify-center text-center bg-white dark:bg-slate-900 rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-slate-800">
                <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800 rounded-3xl flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight mb-2">Inventory Template Kosong</h3>
                <p class="text-sm text-slate-500 mb-8 max-w-sm">Anda belum memiliki template dokumen. Buat template sekarang untuk mempermudah standarisasi dokumen akreditasi Anda.</p>
                <button wire:click="$set('showCreateModal', true)"
                        class="px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-indigo-500/20 transition-all active:scale-95">
                    Buat Template Pertama
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($templates->hasPages())
        <div class="flex justify-center pt-10">
            {{ $templates->links() }}
        </div>
    @endif

    <!-- Modals (Partials) -->
    @include('livewire.partials.template-modal', [
        'modalId' => 'createTemplateModal',
        'showModal' => 'showCreateModal',
        'title' => 'Buat Template Baru',
        'action' => 'createTemplate',
        'actionLabel' => 'Simpan Template'
    ])

    @include('livewire.partials.template-modal', [
        'modalId' => 'editTemplateModal',
        'showModal' => 'showEditModal',
        'title' => 'Perbarui Template',
        'action' => 'updateTemplate',
        'actionLabel' => 'Simpan Perubahan'
    ])

    <!-- Deploy Template Modal -->
    <div x-data="{ show: @entangle('showUseModal') }" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-hidden" style="display: none">
        <div class="flex items-center justify-center min-h-screen p-4 sm:p-6 lg:p-8">
            <div x-show="show" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-md"></div>

            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-8" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="relative bg-white dark:bg-slate-900 w-full max-w-7xl h-[90vh] rounded-[3rem] shadow-2xl flex flex-col overflow-hidden border border-white/20">
                
                <!-- Modal Head -->
                <div class="px-10 py-6 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center space-x-5">
                        <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Deploy Template</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-0.5">{{ $usingTemplate?->name ?? '...' }}</p>
                        </div>
                    </div>
                    <button @click="show = false; $wire.set('showUseModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Body (Split) -->
                <div class="flex-1 overflow-hidden flex flex-col lg:flex-row">
                    <!-- Form Side -->
                    <div class="w-full lg:w-[400px] bg-white dark:bg-slate-900 border-r border-slate-100 dark:border-slate-800 p-8 overflow-y-auto overflow-x-hidden">
                        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-8 flex items-center">
                            <span class="w-6 h-6 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center mr-3">01</span>
                            Variable Mapping
                        </p>
                        <div class="space-y-6">
                            @if($usingTemplate)
                                @foreach($usingTemplate->getVariables() as $key => $variable)
                                    <div class="group">
                                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2 px-1 group-focus-within:text-indigo-500 transition-colors">
                                            {{ $variable['label'] }} @if($variable['required'] ?? true) <span class="text-rose-500">*</span> @endif
                                        </label>
                                        @if(($variable['type'] ?? 'text') === 'textarea')
                                            <textarea wire:model.blur="fillData.{{ $key }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-2 border-transparent focus:border-indigo-500 rounded-2xl transition-all text-sm font-medium focus:ring-0" rows="4"></textarea>
                                        @else
                                            <input type="{{ $variable['type'] ?? 'text' }}" wire:model.blur="fillData.{{ $key }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-2 border-transparent focus:border-indigo-500 rounded-2xl transition-all text-sm font-medium focus:ring-0">
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Preview Side -->
                    <div class="flex-1 bg-slate-50 dark:bg-slate-950 p-10 overflow-hidden flex flex-col">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-8 flex items-center px-4">
                            <span class="w-6 h-6 bg-white dark:bg-slate-800 rounded-lg flex items-center justify-center mr-3 shadow-sm text-slate-400">02</span>
                            Real-time Document Preview
                        </p>
                        <div class="flex-1 bg-white dark:bg-slate-900 shadow-2xl rounded-[2.5rem] p-12 overflow-y-auto scroll-fancy border border-white/20">
                            <div class="max-w-2xl mx-auto py-10 font-serif leading-loose text-slate-700 dark:text-slate-300">
                                @if($previewContent)
                                    <div class="prose prose-slate dark:prose-invert max-w-none">
                                        {!! nl2br(e($previewContent)) !!}
                                    </div>
                                @else
                                    <div class="h-full flex flex-col items-center justify-center text-center space-y-4 opacity-30 italic">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        <p class="text-sm font-bold tracking-widest uppercase">Waiting for variables...</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Foot -->
                <div class="px-10 py-6 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <p class="hidden sm:flex items-center text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Generated file will be saved in Data Dukung repository.
                    </p>
                    <div class="flex items-center space-x-4 w-full sm:w-auto">
                        <button @click="show = false; $wire.set('showUseModal', false)" class="flex-1 sm:flex-none px-8 py-3 text-xs font-black text-slate-400 hover:text-slate-900 dark:hover:text-white uppercase tracking-widest transition-colors">BATAL</button>
                        <button wire:click="createDocumentFromTemplate" 
                                wire:loading.attr="disabled"
                                class="flex-1 sm:flex-none px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-indigo-500/20 transition-all active:scale-95 disabled:opacity-50">
                            <span wire:loading.remove>Generate Dokumen</span>
                            <span wire:loading class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Toasts -->
    @if(session()->has('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-12"
             class="fixed bottom-10 right-10 z-[100] bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-8 py-5 rounded-[2rem] shadow-2xl flex items-center space-x-5 border border-slate-700 dark:border-slate-100">
            <div class="w-10 h-10 bg-indigo-500 rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest opacity-50 mb-1 leading-none">Operation Success</p>
                <p class="font-bold text-sm tracking-tight">{{ session('message') }}</p>
            </div>
        </div>
    @endif
</div>
