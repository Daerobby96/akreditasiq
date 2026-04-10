<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8 border-b pb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Identitas Perguruan Tinggi</h2>
                        <p class="text-sm text-gray-500 mt-1">Kelola informasi institusi yang akan ditampilkan di seluruh laporan akreditasi.</p>
                    </div>
                    <div class="h-12 w-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>

                @if (session()->has('message'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded flex items-center shadow-sm">
                        <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                    </div>
                @endif

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Perguruan Tinggi</label>
                                <input type="text" wire:model="nama_institusi" 
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200" 
                                    placeholder="Contoh: Universitas Akreditasi Indonesia">
                                @error('nama_institusi') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kota</label>
                                    <input type="text" wire:model="kota" 
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Website</label>
                                    <input type="text" wire:model="website" 
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap</label>
                                <textarea wire:model="alamat" rows="3" 
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200"></textarea>
                            </div>

                            <div class="pt-4 border-t">
                                <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wider">Pejabat Berwenang (Rektor)</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Rektor</label>
                                        <input type="text" wire:model="rektor_nama" 
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">NIP/NIDN</label>
                                        <input type="text" wire:model="rektor_nip" 
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-4 text-center">Logo Institusi</label>
                                <div class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors duration-200 group relative">
                                    @if ($logo)
                                        <img src="{{ $logo->temporaryUrl() }}" class="h-40 w-40 object-contain shadow-md rounded-lg">
                                    @elseif($logo_path)
                                        <img src="{{ asset('storage/' . $logo_path) }}" class="h-40 w-40 object-contain shadow-md rounded-lg">
                                    @else
                                        <div class="h-40 w-40 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    <input type="file" wire:model="logo" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <p class="mt-4 text-xs text-gray-500 font-medium">Klik untuk ganti logo (PNG/JPG, Max 1MB)</p>
                                    @error('logo') <span class="text-xs text-red-500 mt-2 block font-medium">{{ $message }}</span> @enderror
                                    
                                    <div wire:loading wire:target="logo" class="mt-2 text-xs text-indigo-600 font-medium italic animate-pulse">
                                        Mengunggah logo...
                                    </div>
                                </div>
                            </div>

                            <div class="bg-indigo-50 p-6 rounded-xl border border-indigo-100 italic text-sm text-indigo-800 leading-relaxed shadow-inner">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-indigo-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Logo dan identitas ini akan muncul secara otomatis pada halaman <strong>Cover LED/DED</strong> dan tabel informasi <strong>LKPS/DIK/DEK</strong> untuk mencerminkan identitas resmi kampus Anda dalam proses akreditasi.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-8 border-t">
                        <button type="submit" 
                            class="inline-flex items-center px-8 py-3 bg-indigo-600 border border-transparent rounded-lg font-bold text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:shadow-indigo-500/30 transform hover:-translate-y-0.5"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                            <span wire:loading wire:target="save" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
