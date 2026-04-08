<?php

declare(strict_types=1);

namespace App\Filament\Resources\Appointments\Schemas;

use App\Enums\AppointmentStatus;
use App\Models\Contact;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ticket_id')
                    ->label('Ticket')
                    ->options(Ticket::pluck('subject', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('contact_id')
                    ->label('Contact')
                    ->options(Contact::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('user_id')
                    ->label('Monteur')
                    ->options(User::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                DateTimePicker::make('scheduled_at')
                    ->label('Datum & Tijd')
                    ->required(),
                TextInput::make('duration_minutes')
                    ->label('Duur (minuten)')
                    ->numeric()
                    ->default(60),
                Select::make('status')
                    ->label('Status')
                    ->options(AppointmentStatus::class)
                    ->default('planned'),
                Textarea::make('notes')
                    ->label('Notities')
                    ->columnSpanFull(),
            ]);
    }
}
