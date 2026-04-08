<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Enums\Channel;
use App\Enums\ConversationStatus;
use App\Enums\MessageRole;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationTest extends TestCase
{
    use RefreshDatabase;

    public function test_conversation_can_be_created_with_chat_channel(): void
    {
        $conversation = Conversation::create([
            'channel' => Channel::Chat,
            'status' => ConversationStatus::Active,
        ]);

        $this->assertDatabaseHas('conversations', [
            'id' => $conversation->id,
            'channel' => 'chat',
            'status' => 'active',
        ]);
    }

    public function test_conversation_has_messages(): void
    {
        $conversation = Conversation::create([
            'channel' => Channel::Chat,
            'status' => ConversationStatus::Active,
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'role' => MessageRole::User,
            'content' => 'Hallo',
        ]);

        $this->assertCount(1, $conversation->messages);
    }

    public function test_conversation_belongs_to_contact(): void
    {
        $contact = Contact::create(['name' => 'Test Contact']);

        $conversation = Conversation::create([
            'contact_id' => $contact->id,
            'channel' => Channel::Chat,
            'status' => ConversationStatus::Active,
        ]);

        $this->assertEquals($contact->id, $conversation->contact->id);
    }
}
