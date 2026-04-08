<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Appointment;
use App\Models\Setting;
use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    private ?GoogleClient $client = null;

    public function getClient(): GoogleClient
    {
        if ($this->client) {
            return $this->client;
        }

        $this->client = new GoogleClient();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect_uri'));
        $this->client->addScope(Calendar::CALENDAR_EVENTS);
        $this->client->setAccessType('offline');

        $token = Setting::get('google_calendar_token');

        if ($token) {
            $tokenData = json_decode($token, true);
            $this->client->setAccessToken($tokenData);

            if ($this->client->isAccessTokenExpired() && $this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                Setting::set('google_calendar_token', json_encode($this->client->getAccessToken()));
            }
        }

        return $this->client;
    }

    public function createEvent(Appointment $appointment): ?string
    {
        $client = $this->getClient();

        if (! $client->getAccessToken()) {
            Log::warning('Google Calendar not authenticated');

            return null;
        }

        $calendarService = new Calendar($client);
        $calendarId = $appointment->user->google_calendar_id ?? 'primary';

        $contact = $appointment->contact;
        $ticket = $appointment->ticket;

        $event = new Event([
            'summary' => $ticket?->subject ?? 'Afspraak',
            'description' => implode("\n", array_filter([
                "Contact: {$contact?->name}",
                $contact?->phone ? "Tel: {$contact->phone}" : null,
                $contact?->address ? "Adres: {$contact->address}, {$contact->city} {$contact->postal_code}" : null,
                $appointment->notes ? "\nNotities: {$appointment->notes}" : null,
            ])),
            'start' => new EventDateTime([
                'dateTime' => $appointment->scheduled_at->toRfc3339String(),
                'timeZone' => 'Europe/Amsterdam',
            ]),
            'end' => new EventDateTime([
                'dateTime' => $appointment->scheduled_at->addMinutes($appointment->duration_minutes)->toRfc3339String(),
                'timeZone' => 'Europe/Amsterdam',
            ]),
        ]);

        if ($contact?->address) {
            $event->setLocation("{$contact->address}, {$contact->postal_code} {$contact->city}");
        }

        $createdEvent = $calendarService->events->insert($calendarId, $event);

        return $createdEvent->getId();
    }

    public function getAuthUrl(): string
    {
        return $this->getClient()->createAuthUrl();
    }

    public function handleCallback(string $code): void
    {
        $client = $this->getClient();
        $token = $client->fetchAccessTokenWithAuthCode($code);
        Setting::set('google_calendar_token', json_encode($token));
    }
}
