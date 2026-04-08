<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\Channel;
use App\Enums\ConversationStatus;
use App\Enums\MessageRole;
use App\Jobs\ProcessChatMessage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Setting;
use Livewire\Component;

class ChatWidget extends Component
{
    public bool $isOpen = false;

    public ?string $conversationId = null;

    public string $input = '';

    public array $messages = [];

    public bool $isTyping = false;

    public function mount(): void
    {
        $greeting = Setting::get('ai_greeting', 'Welkom! Hoe kan ik u helpen?');
        $this->messages = [
            ['role' => 'assistant', 'content' => $greeting],
        ];
    }

    public function toggle(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function sendMessage(): void
    {
        $content = trim($this->input);

        if ($content === '') {
            return;
        }

        $this->input = '';

        if (! $this->conversationId) {
            $conversation = Conversation::create([
                'channel' => Channel::Chat,
                'status' => ConversationStatus::Active,
            ]);
            $this->conversationId = $conversation->id;
        }

        $message = Message::create([
            'conversation_id' => $this->conversationId,
            'role' => MessageRole::User,
            'content' => $content,
        ]);

        $this->messages[] = [
            'role' => 'user',
            'content' => $content,
        ];

        $this->isTyping = true;

        ProcessChatMessage::dispatch(
            Conversation::find($this->conversationId),
        );
    }

    public function onMessageReceived(array $data): void
    {
        $this->messages[] = [
            'role' => $data['role'],
            'content' => $data['content'],
        ];

        $this->isTyping = false;
    }

    public function getListeners(): array
    {
        if (! $this->conversationId) {
            return [];
        }

        return [
            "echo:conversation.{$this->conversationId},ChatMessageReceived" => 'onMessageReceived',
        ];
    }

    public function render()
    {
        return view('livewire.chat-widget');
    }
}
