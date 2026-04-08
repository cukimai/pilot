<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\Channel;
use App\Enums\ConversationStatus;
use App\Enums\MessageRole;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessVoiceWebhook implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public array $payload,
    ) {
    }

    public function handle(): void
    {
        $contact = null;
        $callerName = $this->payload['caller_name'] ?? null;
        $callerPhone = $this->payload['caller_phone'] ?? null;

        if ($callerName || $callerPhone) {
            $contact = Contact::firstOrCreate(
                ['phone' => $callerPhone],
                ['name' => $callerName ?? 'Onbekend', 'phone' => $callerPhone],
            );
        }

        $conversation = Conversation::create([
            'contact_id' => $contact?->id,
            'channel' => Channel::Voice,
            'status' => ConversationStatus::Closed,
            'summary' => $this->payload['summary'] ?? null,
        ]);

        if (! empty($this->payload['transcript'])) {
            foreach ($this->payload['transcript'] as $entry) {
                Message::create([
                    'conversation_id' => $conversation->id,
                    'role' => $entry['role'] === 'assistant' ? MessageRole::Assistant : MessageRole::User,
                    'content' => $entry['content'],
                ]);
            }
        }

        if (! $contact) {
            return;
        }

        $priority = $this->payload['urgency'] === 'urgent'
            ? TicketPriority::Urgent
            : TicketPriority::Medium;

        $ticket = Ticket::create([
            'conversation_id' => $conversation->id,
            'contact_id' => $contact->id,
            'type' => $this->mapTicketType($this->payload['type'] ?? 'overig'),
            'priority' => $priority,
            'status' => TicketStatus::Open,
            'subject' => $this->payload['summary'] ?? 'Telefonische melding',
            'description' => $this->payload['summary'] ?? 'Binnenkomend telefoongesprek',
        ]);

        SendTicketNotification::dispatch($ticket);

        if ($priority === TicketPriority::Urgent) {
            SendUrgentAlert::dispatch($ticket);
        }
    }

    private function mapTicketType(string $type): TicketType
    {
        return match ($type) {
            'storing' => TicketType::Storing,
            'afspraak' => TicketType::Afspraak,
            'offerte' => TicketType::Offerte,
            default => TicketType::Overig,
        };
    }
}
