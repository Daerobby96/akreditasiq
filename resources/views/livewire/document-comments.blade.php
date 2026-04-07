<div class="space-y-6">
    <!-- Comment Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Diskusi & Komentar</h3>
                <p class="text-sm text-slate-500">{{ $comments->count() }} komentar</p>
            </div>
        </div>

        <!-- Filter Toggle -->
        <label class="flex items-center space-x-2 cursor-pointer">
            <input type="checkbox" wire:model.live="showResolved" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm text-slate-600 dark:text-slate-400">Tampilkan yang terselesaikan</span>
        </label>
    </div>

    <!-- Add New Comment -->
    <div class="glass-card p-6">
        <div class="flex space-x-3">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            </div>
            <div class="flex-1">
                <textarea
                    wire:model="newComment"
                    placeholder="Tambahkan komentar Anda..."
                    class="w-full p-3 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-slate-800 text-slate-900 dark:text-white resize-none"
                    rows="3"
                    wire:keydown.enter.prevent="addComment"
                ></textarea>
                <div class="flex justify-between items-center mt-3">
                    <div class="text-sm text-slate-500">
                        Tekan Enter untuk mengirim, gunakan @nama untuk menyebutkan pengguna
                    </div>
                    <button
                        wire:click="addComment"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 transition-colors"
                    >
                        <span wire:loading.remove>Kirim Komentar</span>
                        <span wire:loading>Mengirim...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments List -->
    <div class="space-y-4" wire:poll.10s="refreshComments">
        @forelse($comments as $comment)
            <div class="comment-thread {{ $comment->is_resolved ? 'opacity-75' : '' }}">
                @include('livewire.partials.comment-item', ['comment' => $comment, 'depth' => 0])
            </div>
        @empty
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">Belum ada komentar</h3>
                <p class="text-slate-500">Jadilah yang pertama memberikan komentar pada dokumen ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Success/Error Messages -->
    @if(session()->has('message'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in">
            {{ session('message') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in">
            {{ session('error') }}
        </div>
    @endif

    <!-- Real-time Activity Indicator -->
    <div class="fixed bottom-4 right-4 flex items-center space-x-2 bg-white dark:bg-slate-800 shadow-lg rounded-full px-4 py-2 border border-slate-200 dark:border-slate-700">
        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
        <span class="text-sm text-slate-600 dark:text-slate-400">Real-time aktif</span>
    </div>
</div>
