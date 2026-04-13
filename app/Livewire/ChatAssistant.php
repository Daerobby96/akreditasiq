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
        $this->messages = [
            [
                'role' => 'assistant',
                'content' => 'Halo! Saya asisten cerdas AKRE. Ada yang bisa saya bantu terkait instrumen akreditasi Anda hari ini?'
            ]
        ];
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
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

        $userMessage = $this->message;
        $this->messages[] = ['role' => 'user', 'content' => $userMessage];
        $this->message = '';
        $this->isTyping = true;

        // Perform AI request
        $response = $groq->chat($this->messages);
        
        $this->messages[] = ['role' => 'assistant', 'content' => $response];
        $this->isTyping = false;
        $this->dispatch('messagesUpdated');
    }

    public function render()
    {
        return view('livewire.chat-assistant');
    }
}
