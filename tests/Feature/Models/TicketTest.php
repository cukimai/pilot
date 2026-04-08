<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\Models\Contact;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_can_be_created(): void
    {
        $contact = Contact::create(['name' => 'Jan de Vries']);

        $ticket = Ticket::create([
            'contact_id' => $contact->id,
            'type' => TicketType::Storing,
            'priority' => TicketPriority::High,
            'status' => TicketStatus::Open,
            'subject' => 'CV-ketel storing',
            'description' => 'Ketel maakt raar geluid',
        ]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'type' => 'storing',
            'priority' => 'high',
            'status' => 'open',
        ]);
    }

    public function test_ticket_belongs_to_contact(): void
    {
        $contact = Contact::create(['name' => 'Test']);

        $ticket = Ticket::create([
            'contact_id' => $contact->id,
            'type' => TicketType::Overig,
            'priority' => TicketPriority::Low,
            'status' => TicketStatus::Open,
            'subject' => 'Test',
            'description' => 'Test',
        ]);

        $this->assertEquals('Test', $ticket->contact->name);
    }
}
