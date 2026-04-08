<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Contact;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Open tickets', Ticket::query()->where('status', 'open')->count())
                ->icon('heroicon-o-ticket')
                ->color('warning'),
            Stat::make('Afspraken Vandaag', Appointment::query()->whereDate('scheduled_at', today())->count())
                ->icon('heroicon-o-calendar-days')
                ->color('info'),
            Stat::make('Nieuwe Contacten', Contact::query()->whereDate('created_at', today())->count())
                ->icon('heroicon-o-users')
                ->color('success'),
        ];
    }
}
