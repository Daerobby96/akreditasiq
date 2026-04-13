<div class="fixed bottom-8 right-8 z-[9999]" @trigger-chat.window="$wire.handleExternalTrigger($event.detail.message)">
    <div class="relative group">
        <div class="absolute -inset-2 bg-indigo-500 rounded-full blur opacity-40 group-hover:opacity-100 {{ $isTyping ? 'animate-pulse' : '' }} transition duration-1000"></div>
        <button wire:click="toggleChat" class="relative w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center text-white shadow-2xl transform hover:scale-110 transition-all active:scale-95">
            @if(!$isOpen)
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            @else
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            @endif
        </button>
        
        <!-- Chat Window -->
        <div class="absolute bottom-20 right-0 w-80 sm:w-96 bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden transform transition-all duration-300 {{ $isOpen ? 'scale-100 opacity-100 pointer-events-auto' : 'scale-0 opacity-0 pointer-events-none' }} origin-bottom-right">
            <div class="p-6 smart-gradient text-white">
                <div class="flex items-center space-x-3 mb-1">
                    <div class="w-10 h-10 bg-white/20 rounded-xl backdrop-blur-md flex items-center justify-center">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-lg">AKRE SMART AI</h4>
                        <div class="flex items-center text-[10px] font-bold uppercase tracking-widest opacity-80">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full mr-1.5 animate-pulse"></span>
                            Online & Ready
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="chat-messages" class="p-6 h-80 overflow-y-auto bg-slate-50 dark:bg-slate-950/50 space-y-4 flex flex-col">
                @foreach($messages as $msg)
                    @if($msg['role'] === 'assistant')
                        <div class="flex items-start space-x-3 self-start max-w-[85%]">
                            <div class="w-8 h-8 rounded-full smart-gradient flex-shrink-0 flex items-center justify-center text-[10px] text-white font-bold">AI</div>
                            <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl rounded-tl-none text-sm text-slate-600 dark:text-slate-400 shadow-sm border border-slate-100 dark:border-slate-700">
                                {!! nl2br(e($msg['content'])) !!}
                            </div>
                        </div>
                    @else
                        <div class="flex items-start space-x-3 self-end max-w-[85%] flex-row-reverse space-x-reverse">
                            <div class="w-8 h-8 rounded-full bg-slate-200 flex-shrink-0 flex items-center justify-center text-[10px] text-slate-600 font-bold">ME</div>
                            <div class="bg-indigo-600 p-4 rounded-2xl rounded-tr-none text-sm text-white shadow-md">
                                {{ $msg['content'] }}
                            </div>
                        </div>
                    @endif
                @endforeach

                @if($isTyping)
                    <div class="flex items-start space-x-3 self-start animate-pulse">
                        <div class="w-8 h-8 rounded-full smart-gradient flex-shrink-0 flex items-center justify-center text-[10px] text-white font-bold">AI</div>
                        <div class="bg-white dark:bg-slate-800 p-3 rounded-2xl rounded-tl-none flex space-x-1">
                            <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce"></div>
                            <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                <form wire:submit.prevent="sendMessage" class="relative flex items-center">
                    <input wire:model="message" type="text" placeholder="Ketik pertanyaan..." class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl py-3 pl-4 pr-12 text-sm focus:ring-2 focus:ring-indigo-500 transition-all dark:text-white" />
                    <button type="submit" class="absolute right-2 p-2 text-indigo-600 hover:scale-110 transition-transform" wire:loading.attr="disabled">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('messagesUpdated', () => {
                setTimeout(() => {
                    const chat = document.getElementById('chat-messages');
                    chat.scrollTop = chat.scrollHeight;
                }, 100);
            });
        });
    </script>
</div>
