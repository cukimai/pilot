<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TodayAppointments extends TableWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Afspraken Vandaag';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Appointment::query()
                    ->whereDate('scheduled_at', today())
                    ->orderBy('scheduled_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Tijd')
                    ->dateTime('H:i'),
                Tables\Columns\TextColumn::make('contact.name')
                    ->label('Contact'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Monteur'),
                Tables\Columns\TextColumn::make('ticket.subject')
                    ->label('Ticket')
                    ->limit(30),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->paginated(false)
            ->emptyStateHeading('Geen afspraken vandaag')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }
}
