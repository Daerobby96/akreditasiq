{{-- Comment Item Partial --}}
<div class="comment-item {{ $depth > 0 ? 'ml-12 border-l-2 border-slate-200 dark:border-slate-700 pl-4' : '' }}"
     x-data="{ showReplyForm: false, isEditing: false }">

    <div class="glass-card p-4 {{ $comment->is_resolved ? 'bg-green-50 dark:bg-green-900/10 border-green-200 dark:border-green-800' : '' }}">

        <!-- Comment Header -->
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                    {{ substr($comment->user->name, 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $comment->user->name }}</span>
                        @if($comment->is_resolved)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Terselesaikan
                            </span>
                        @endif
                    </div>
                    <div class="text-sm text-slate-500">
                        {{ $comment->created_at->diffForHumans() }}
                        @if($comment->updated_at != $comment->created_at)
                            <span class="text-slate-400">(diedit)</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Comment Actions -->
            <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity"
                 x-show="!isEditing"
                 x-transition>
                @if(Auth::id() === $comment->user_id)
                    <button wire:click="startEditing({{ $comment->id }})"
                            class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 rounded transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button wire:click="deleteComment({{ $comment->id }})"
                            wire:confirm="Apakah Anda yakin ingin menghapus komentar ini?"
                            class="p-1 text-slate-400 hover:text-red-600 rounded transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                @endif

                <button wire:click="toggleResolved({{ $comment->id }})"
                        class="p-1 {{ $comment->is_resolved ? 'text-green-600' : 'text-slate-400 hover:text-green-600' }} rounded transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </button>

                @if($depth < 3) {{-- Limit reply depth --}}
                    <button @click="showReplyForm = true"
                            class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 rounded transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                    </button>
                @endif
            </div>
        </div>

        <!-- Comment Content -->
        <div class="mb-3">
            @if($this->editingCommentId === $comment->id)
                <!-- Edit Form -->
                <div class="space-y-3">
                    <textarea
                        wire:model="editingContent"
                        class="w-full p-3 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-slate-800 text-slate-900 dark:text-white resize-none"
                        rows="3"
                        wire:keydown.enter.prevent="updateComment"
                    ></textarea>
                    <div class="flex justify-end space-x-2">
                        <button wire:click="cancelEditing"
                                class="px-3 py-1 text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
                            Batal
                        </button>
                        <button wire:click="updateComment"
                                class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors">
                            Simpan
                        </button>
                    </div>
                </div>
            @else
                <!-- Display Content -->
                <div class="prose prose-sm dark:prose-invert max-w-none">
                    {!! nl2br(e($this->highlightMentions($comment->content))) !!}
                </div>

                @if($comment->is_resolved && $comment->resolvedBy)
                    <div class="mt-2 text-sm text-green-600 dark:text-green-400">
                        Ditandai sebagai terselesaikan oleh {{ $comment->resolvedBy->name }} pada {{ $comment->resolved_at->format('d M Y H:i') }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Reply Form -->
        <div x-show="showReplyForm"
             x-transition
             class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
            <div class="flex space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-6 h-6 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-xs">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
                <div class="flex-1">
                    <textarea
                        wire:model="replyContent"
                        placeholder="Tulis balasan Anda..."
                        class="w-full p-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-slate-800 text-slate-900 dark:text-white resize-none text-sm"
                        rows="2"
                        wire:keydown.enter.prevent="replyToComment({{ $comment->id }})"
                    ></textarea>
                    <div class="flex justify-end space-x-2 mt-2">
                        <button @click="showReplyForm = false; $wire.cancelReply()"
                                class="px-3 py-1 text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors text-sm">
                            Batal
                        </button>
                        <button wire:click="replyToComment({{ $comment->id }})"
                                class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors text-sm">
                            Balas
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attachments (if any) -->
        @if($comment->attachments)
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($comment->attachments as $attachment)
                    <a href="{{ Storage::url($attachment['path']) }}"
                       target="_blank"
                       class="inline-flex items-center px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-full text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        {{ $attachment['name'] }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Nested Replies -->
    @if($comment->replies && $comment->replies->count() > 0)
        <div class="mt-4 space-y-4">
            @foreach($comment->replies as $reply)
                @include('livewire.partials.comment-item', ['comment' => $reply, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>