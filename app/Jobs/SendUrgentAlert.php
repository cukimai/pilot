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

class SendUrgentAlert implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public ?Ticket $ticket = null,
        public ?string $reason = null,
        public ?string $overrideEmails = null,
    ) {
    }

    public function handle(): void
    {
        $emails = $this->overrideEmails
            ? array_filter(array_map('trim', explode("\n", $this->overrideEmails)))
            : $this->getNotificationEmails();

        if (empty($emails)) {
            return;
        }

        $subject = '[URGENT] ' . ($this->ticket?->subject ?? $this->reason ?? 'Urgente melding');
        $body = "URGENTE MELDING\n\n";

        if ($this->ticket) {
            $body .= "Ticket: {$this->ticket->subject}\n"
                . "Type: {$this->ticket->type->label()}\n"
                . "Beschrijving: {$this->ticket->description}\n"
                . "Contact: {$this->ticket->contact?->name}\n"
                . "Telefoon: {$this->ticket->contact?->phone}\n";
        }

        if ($this->reason) {
            $body .= "Reden: {$this->reason}\n";
        }

        foreach ($emails as $email) {
            Mail::raw($body, function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });

            NotificationLog::create([
                'type' => 'email',
                'recipient' => $email,
                'subject' => $subject,
                'body' => $body,
                'related_type' => $this->ticket ? Ticket::class : null,
                'related_id' => $this->ticket?->id,
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
