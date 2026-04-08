<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MessageRole;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\Jobs\SendTicketNotification;
use App\Jobs\SendUrgentAlert;
use App\Jobs\SyncAppointmentToGoogle;
use App\Models\Appointment;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatAiService
{
    private KnowledgeBaseService $knowledgeBase;

    public function __construct(KnowledgeBaseService $knowledgeBase)
    {
        $this->knowledgeBase = $knowledgeBase;
    }

    public function processMessage(Conversation $conversation): Message
    {
        $systemPrompt = $this->knowledgeBase->buildSystemPrompt();
        $messages = $this->buildMessageHistory($conversation);
        $tools = $this->getToolDefinitions();

        $response = $this->callClaudeApi($systemPrompt, $messages, $tools);

        return $this->handleResponse($conversation, $response);
    }

    private function buildMessageHistory(Conversation $conversation): array
    {
        return $conversation->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->orderBy('created_at')
            ->get()
            ->map(fn (Message $message) => [
                'role' => $message->role->value,
                'content' => $message->content,
            ])
            ->toArray();
    }

    private function getToolDefinitions(): array
    {
        return [
            [
                'name' => 'create_ticket',
                'description' => 'Maak een nieuw ticket aan voor een storing, afspraak, offerte of overige vraag. Gebruik dit wanneer de klant een probleem meldt of een verzoek heeft dat actie vereist.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'type' => [
                            'type' => 'string',
                            'enum' => ['storing', 'afspraak', 'offerte', 'overig'],
                            'description' => 'Het type ticket',
                        ],
                        'priority' => [
                            'type' => 'string',
                            'enum' => ['low', 'medium', 'high', 'urgent'],
                            'description' => 'De prioriteit. Urgent voor: geen verwarming in de winter, gaslucht, grote lekkage',
                        ],
                        'subject' => [
                            'type' => 'string',
                            'description' => 'Korte samenvatting van het probleem/verzoek',
                        ],
                        'description' => [
                            'type' => 'string',
                            'description' => 'Gedetailleerde beschrijving',
                        ],
                    ],
                    'required' => ['type', 'priority', 'subject', 'description'],
                ],
            ],
            [
                'name' => 'schedule_appointment',
                'description' => 'Plan een afspraak in voor een monteurbezoek. Gebruik dit wanneer de klant een afspraak wil maken.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'date' => [
                            'type' => 'string',
                            'description' => 'Gewenste datum in YYYY-MM-DD formaat',
                        ],
                        'time' => [
                            'type' => 'string',
                            'description' => 'Gewenste tijd in HH:MM formaat',
                        ],
                        'duration' => [
                            'type' => 'integer',
                            'description' => 'Geschatte duur in minuten',
                            'default' => 60,
                        ],
                        'description' => [
                            'type' => 'string',
                            'description' => 'Wat moet er gedaan worden',
                        ],
                    ],
                    'required' => ['date', 'time', 'description'],
                ],
            ],
            [
                'name' => 'collect_contact_info',
                'description' => 'Sla contactgegevens op van de klant. Gebruik dit zodra je naam, telefoon of adres hebt ontvangen.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string', 'description' => 'Volledige naam'],
                        'phone' => ['type' => 'string', 'description' => 'Telefoonnummer'],
                        'email' => ['type' => 'string', 'description' => 'E-mailadres'],
                        'address' => ['type' => 'string', 'description' => 'Straatnaam en huisnummer'],
                        'city' => ['type' => 'string', 'description' => 'Stad'],
                        'postal_code' => ['type' => 'string', 'description' => 'Postcode'],
                    ],
                    'required' => ['name'],
                ],
            ],
            [
                'name' => 'escalate_to_human',
                'description' => 'Escaleer het gesprek naar een menselijke medewerker. Gebruik dit als je de vraag niet kunt beantwoorden of als de klant erom vraagt.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'reason' => [
                            'type' => 'string',
                            'description' => 'Reden voor escalatie',
                        ],
                    ],
                    'required' => ['reason'],
                ],
            ],
        ];
    }

    private function callClaudeApi(string $systemPrompt, array $messages, array $tools): array
    {
        $response = Http::withHeaders([
            'x-api-key' => config('services.anthropic.api_key'),
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
            'model' => config('services.anthropic.model'),
            'max_tokens' => 1024,
            'system' => $systemPrompt,
            'messages' => $messages,
            'tools' => $tools,
        ]);

        if ($response->failed()) {
            Log::error('Claude API error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException('Claude API call failed: ' . $response->body());
        }

        return $response->json();
    }

    private function handleResponse(Conversation $conversation, array $response): Message
    {
        $textContent = '';
        $toolResults = [];

        foreach ($response['content'] ?? [] as $block) {
            if ($block['type'] === 'text') {
                $textContent .= $block['text'];
            }

            if ($block['type'] === 'tool_use') {
                $result = $this->executeTool($conversation, $block['name'], $block['input']);
                $toolResults[] = [
                    'tool' => $block['name'],
                    'input' => $block['input'],
                    'result' => $result,
                ];
            }
        }

        if (! empty($toolResults) && $response['stop_reason'] === 'tool_use') {
            $toolResultMessages = $this->buildToolResultMessages($response, $toolResults);
            $allMessages = $this->buildMessageHistory($conversation);
            $allMessages[] = ['role' => 'assistant', 'content' => $response['content']];
            $allMessages[] = ['role' => 'user', 'content' => $toolResultMessages];

            $followUp = $this->callClaudeApi(
                $this->knowledgeBase->buildSystemPrompt(),
                $allMessages,
                $this->getToolDefinitions(),
            );

            foreach ($followUp['content'] ?? [] as $block) {
                if ($block['type'] === 'text') {
                    $textContent = $block['text'];
                }
            }
        }

        return Message::create([
            'conversation_id' => $conversation->id,
            'role' => MessageRole::Assistant,
            'content' => $textContent,
            'metadata' => ! empty($toolResults) ? ['tool_calls' => $toolResults] : null,
        ]);
    }

    private function buildToolResultMessages(array $response, array $toolResults): array
    {
        $results = [];

        foreach ($response['content'] as $block) {
            if ($block['type'] !== 'tool_use') {
                continue;
            }

            $matchingResult = collect($toolResults)->firstWhere('tool', $block['name']);

            $results[] = [
                'type' => 'tool_result',
                'tool_use_id' => $block['id'],
                'content' => json_encode($matchingResult['result'] ?? ['status' => 'ok']),
            ];
        }

        return $results;
    }

    private function executeTool(Conversation $conversation, string $toolName, array $input): array
    {
        return match ($toolName) {
            'create_ticket' => $this->executeCreateTicket($conversation, $input),
            'schedule_appointment' => $this->executeScheduleAppointment($conversation, $input),
            'collect_contact_info' => $this->executeCollectContactInfo($conversation, $input),
            'escalate_to_human' => $this->executeEscalateToHuman($conversation, $input),
            default => ['status' => 'error', 'message' => "Unknown tool: {$toolName}"],
        };
    }

    private function executeCreateTicket(Conversation $conversation, array $input): array
    {
        $contact = $conversation->contact;

        if (! $contact) {
            return ['status' => 'error', 'message' => 'Geen contactgegevens beschikbaar. Vraag eerst om contactgegevens.'];
        }

        $ticket = Ticket::create([
            'conversation_id' => $conversation->id,
            'contact_id' => $contact->id,
            'type' => $input['type'],
            'priority' => $input['priority'],
            'status' => TicketStatus::Open,
            'subject' => $input['subject'],
            'description' => $input['description'],
        ]);

        SendTicketNotification::dispatch($ticket);

        if ($input['priority'] === 'urgent') {
            SendUrgentAlert::dispatch($ticket);
        }

        return ['status' => 'ok', 'ticket_id' => $ticket->id, 'message' => 'Ticket aangemaakt'];
    }

    private function executeScheduleAppointment(Conversation $conversation, array $input): array
    {
        $contact = $conversation->contact;

        if (! $contact) {
            return ['status' => 'error', 'message' => 'Geen contactgegevens beschikbaar.'];
        }

        $ticket = $conversation->ticket;

        if (! $ticket) {
            $ticket = Ticket::create([
                'conversation_id' => $conversation->id,
                'contact_id' => $contact->id,
                'type' => TicketType::Afspraak,
                'priority' => TicketPriority::Medium,
                'status' => TicketStatus::Scheduled,
                'subject' => 'Afspraak: ' . $input['description'],
                'description' => $input['description'],
            ]);
        }

        $scheduledAt = $input['date'] . ' ' . $input['time'];
        $monteur = User::query()->where('role', 'monteur')->first();

        $appointment = Appointment::create([
            'ticket_id' => $ticket->id,
            'contact_id' => $contact->id,
            'user_id' => $monteur?->id ?? $conversation->contact_id,
            'scheduled_at' => $scheduledAt,
            'duration_minutes' => $input['duration'] ?? 60,
            'notes' => $input['description'],
        ]);

        if ($monteur) {
            SyncAppointmentToGoogle::dispatch($appointment);
        }

        return ['status' => 'ok', 'appointment_id' => $appointment->id, 'message' => 'Afspraak ingepland'];
    }

    private function executeCollectContactInfo(Conversation $conversation, array $input): array
    {
        $contact = $conversation->contact;

        if ($contact) {
            $contact->update(array_filter($input));
        } else {
            $contact = Contact::create(array_filter($input));
            $conversation->update(['contact_id' => $contact->id]);
        }

        return ['status' => 'ok', 'contact_id' => $contact->id, 'message' => 'Contactgegevens opgeslagen'];
    }

    private function executeEscalateToHuman(Conversation $conversation, array $input): array
    {
        $conversation->update(['summary' => 'ESCALATIE: ' . $input['reason']]);

        $notificationEmails = Setting::get('notification_emails', '');

        if ($notificationEmails) {
            SendUrgentAlert::dispatch(null, 'Escalatie: ' . $input['reason'], $notificationEmails);
        }

        return ['status' => 'ok', 'message' => 'Gesprek geëscaleerd naar medewerker'];
    }
}
