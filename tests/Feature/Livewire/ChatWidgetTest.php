<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\ChatWidget;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChatWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_widget_renders(): void
    {
        Setting::set('ai_greeting', 'Welkom!');

        Livewire::test(ChatWidget::class)
            ->assertSee('Welkom!');
    }

    public function test_chat_widget_toggles_open(): void
    {
        Livewire::test(ChatWidget::class)
            ->assertSet('isOpen', false)
            ->call('toggle')
            ->assertSet('isOpen', true);
    }

    public function test_chat_widget_creates_conversation_on_first_message(): void
    {
        Livewire::test(ChatWidget::class)
            ->set('input', 'Hallo')
            ->call('sendMessage')
            ->assertSet('input', '')
            ->assertNotNull(fn ($component) => $component->conversationId);
    }

    public function test_chat_widget_ignores_empty_messages(): void
    {
        Livewire::test(ChatWidget::class)
            ->set('input', '   ')
            ->call('sendMessage')
            ->assertNull(fn ($component) => $component->conversationId);
    }
}
