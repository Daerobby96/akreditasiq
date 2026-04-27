<?php

namespace App\Livewire;

use App\Services\GroqService;
use Livewire\Component;
use Livewire\Attributes\On;

class ChatAssistant extends Component
{
    public $message = '';
    public $messages = [];
    public $isOpen = false;
    public $isTyping = false;

    public function mount()
    {
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $userId = auth()->id();
        $prodiId = session('selected_prodi_id');

        $storedMessages = \App\Models\ChatMessage::where('user_id', $userId)
            // ->where('prodi_id', $prodiId) // Optional: filter by prodi
            ->orderBy('created_at', 'asc')
            ->get();

        if ($storedMessages->isEmpty()) {
            $this->messages = [
                [
                    'role' => 'assistant',
                    'content' => 'Halo! Saya asisten cerdas AKRE. Ada yang bisa saya bantu terkait instrumen akreditasi Anda hari ini?'
                ]
            ];
        } else {
            $this->messages = $storedMessages->map(function ($msg) {
                return [
                    'role' => $msg->role,
                    'content' => $msg->content
                ];
            })->toArray();
        }
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->dispatch('messagesUpdated');
        }
    }

    #[On('trigger-chat')]
    public function handleExternalTrigger($message)
    {
        $this->isOpen = true;
        $this->message = $message;
        $this->sendMessage(app(GroqService::class));
    }

    public function sendMessage(GroqService $groq)
    {
        if (empty(trim($this->message))) {
            return;
        }

        $userId = auth()->id();
        $prodiId = session('selected_prodi_id');
        $userMessage = $this->message;

        // Save User Message
        \App\Models\ChatMessage::create([
            'user_id' => $userId,
            'prodi_id' => $prodiId,
            'role' => 'user',
            'content' => $userMessage
        ]);

        $this->messages[] = ['role' => 'user', 'content' => $userMessage];
        $this->message = '';
        $this->isTyping = true;

        try {
            // Perform AI request
            $response = $groq->chat($this->messages);
            
            // Save Assistant Message
            \App\Models\ChatMessage::create([
                'user_id' => $userId,
                'prodi_id' => $prodiId,
                'role' => 'assistant',
                'content' => $response
            ]);

            $this->messages[] = ['role' => 'assistant', 'content' => $response];
        } catch (\Exception $e) {
            $this->messages[] = ['role' => 'assistant', 'content' => 'Maaf, terjadi kesalahan teknis saat menghubungi AI. Silakan coba lagi.'];
        }

        $this->isTyping = false;
        $this->dispatch('messagesUpdated');
    }

    public function clearHistory()
    {
        \App\Models\ChatMessage::where('user_id', auth()->id())->delete();
        $this->mount();
        $this->dispatch('messagesUpdated');
    }

    public function render()
    {
        return view('livewire.chat-assistant');
    }
}
