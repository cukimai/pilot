<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tickets\Schemas;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\Models\Contact;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('subject')
                    ->label('Onderwerp')
                    ->required()
                    ->maxLength(255),
                Select::make('contact_id')
                    ->label('Contact')
                    ->options(Contact::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('type')
                    ->label('Type')
                    ->options(TicketType::class)
                    ->required(),
                Select::make('priority')
                    ->label('Prioriteit')
                    ->options(TicketPriority::class)
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(TicketStatus::class)
                    ->default('open')
                    ->required(),
                Select::make('assigned_to')
                    ->label('Toegewezen aan')
                    ->options(User::pluck('name', 'id'))
                    ->searchable(),
                DateTimePicker::make('scheduled_at')
                    ->label('Ingepland op'),
                Textarea::make('description')
                    ->label('Beschrijving')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
