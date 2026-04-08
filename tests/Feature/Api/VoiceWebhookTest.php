<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Conversation;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoiceWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_accepts_valid_payload(): void
    {
        $response = $this->postJson('/api/voice/webhook', [
            'caller_name' => 'Jan de Vries',
            'caller_phone' => '06-12345678',
            'summary' => 'CV-ketel storing',
            'type' => 'storing',
            'urgency' => 'high',
            'transcript' => [
                ['role' => 'assistant', 'content' => 'Goedemorgen, waarmee kan ik u helpen?'],
                ['role' => 'user', 'content' => 'Mijn CV-ketel doet het niet meer'],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }

    public function test_webhook_creates_conversation_and_ticket(): void
    {
        $this->postJson('/api/voice/webhook', [
            'caller_name' => 'Test Persoon',
            'caller_phone' => '06-99999999',
            'summary' => 'Airco kapot',
            'type' => 'storing',
            'urgency' => 'medium',
            'transcript' => [
                ['role' => 'user', 'content' => 'Mijn airco doet het niet'],
            ],
        ]);

        $this->assertDatabaseHas('conversations', ['channel' => 'voice']);
        $this->assertDatabaseHas('contacts', ['phone' => '06-99999999']);
        $this->assertDatabaseHas('tickets', ['subject' => 'Airco kapot']);
    }

    public function test_webhook_rejects_invalid_type(): void
    {
        $response = $this->postJson('/api/voice/webhook', [
            'type' => 'invalid_type',
        ]);

        $response->assertStatus(422);
    }
}
