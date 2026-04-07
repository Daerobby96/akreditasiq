@php
    $placeholderText = 'Masukkan konten template. Gunakan &lbrace;&lbrace;variable_name&rbrace;&rbrace; untuk field yang bisa diisi otomatis.';
@endphp

<!-- Template Modal Partial -->
<div x-data="{ show: @entangle($showModal) }"
     x-show="show"
     x-on:keydown.escape.window="show = false; $wire.set('{{$showModal}}', false)"
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">

    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity"
             @click="show = false; $wire.set('{{$showModal}}', false)"></div>

        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">

            <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white" id="modal-title">
                        {{ $title }}
                    </h3>
                    <button @click="show = false; $wire.set('{{$showModal}}', false)"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="{{ $action }}" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                    Nama Template <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model="templateName"
                                       class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
                                       placeholder="Masukkan nama template">
                                @error('templateName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                    Kriteria BAN-PT <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="templateKriteriaId"
                                        class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
                                    <option value="">Pilih Kriteria</option>
                                    @foreach($kriteriaOptions ?? [] as $kriteria)
                                        <option value="{{ $kriteria->id }}">{{ $kriteria->kode }} - {{ $kriteria->nama }}</option>
                                    @endforeach
                                </select>
                                @error('templateKriteriaId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                    Deskripsi
                                </label>
                                <textarea wire:model="templateDescription"
                                          class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white resize-none"
                                          rows="3"
                                          placeholder="Deskripsi template (opsional)"></textarea>
                            </div>

                            <!-- File Uploads -->
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                        File Template (DOC, DOCX, PDF)
                                    </label>
                                    <input type="file"
                                           wire:model="templateFile"
                                           accept=".doc,.docx,.pdf"
                                           class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
                                    @error('templateFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                        Preview Image
                                    </label>
                                    <input type="file"
                                           wire:model="templatePreview"
                                           accept="image/*"
                                           class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
                                    @error('templatePreview') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Template Content -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                Konten Template <span class="text-red-500">*</span>
                            </label>
                            <textarea wire:model="templateContent"
                                      class="w-full h-80 px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white font-mono text-sm resize-none"
                                      placeholder="{!! $placeholderText !!}"></textarea>
                            @error('templateContent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                            <!-- Variables Preview -->
                            <div class="mt-4">
                                <h4 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Variabel Terdeteksi:</h4>
                                @if(!empty($templateVariables))
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($templateVariables as $key => $variable)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-400">
                                                {{ $key }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-slate-500">Belum ada variabel terdeteksi. Gunakan format @{{nama_variabel}} dalam konten.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-slate-50 dark:bg-slate-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button wire:click="{{ $action }}"
                        wire:loading.attr="disabled"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                    <span wire:loading.remove>{{ $actionLabel }}</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
                <button @click="show = false; $wire.set('{{$showModal}}', false); $wire.call('resetTemplateForm')"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-800 text-base font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>