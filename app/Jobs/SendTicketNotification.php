<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\NotificationLog;
use App\Models\Setting;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTicketNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Ticket $ticket,
    ) {
    }

    public function handle(): void
    {
        $emails = $this->getNotificationEmails();

        if (empty($emails)) {
            return;
        }

        $subject = "Nieuw ticket: {$this->ticket->subject}";
        $body = "Er is een nieuw ticket aangemaakt.\n\n"
            . "Type: {$this->ticket->type->label()}\n"
            . "Prioriteit: {$this->ticket->priority->label()}\n"
            . "Onderwerp: {$this->ticket->subject}\n"
            . "Beschrijving: {$this->ticket->description}\n"
            . "Contact: {$this->ticket->contact?->name}\n";

        foreach ($emails as $email) {
            Mail::raw($body, function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });

            NotificationLog::create([
                'type' => 'email',
                'recipient' => $email,
                'subject' => $subject,
                'body' => $body,
                'related_type' => Ticket::class,
                'related_id' => $this->ticket->id,
            ]);
        }
    }

    private function getNotificationEmails(): array
    {
        $raw = Setting::get('notification_emails', '');

        return array_filter(
            array_map('trim', explode("\n", $raw)),
        );
    }
}
