<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\GoogleCalendarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncAppointmentToGoogle implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(
        public Appointment $appointment,
    ) {
    }

    public function handle(GoogleCalendarService $calendarService): void
    {
        $user = $this->appointment->user;

        if (! $user?->google_calendar_id) {
            Log::info('Skipping Google Calendar sync: no calendar ID for user', [
                'appointment_id' => $this->appointment->id,
            ]);

            return;
        }

        $eventId = $calendarService->createEvent($this->appointment);

        $this->appointment->update(['google_event_id' => $eventId]);
    }
}
