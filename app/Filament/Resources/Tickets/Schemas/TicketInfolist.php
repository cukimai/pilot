<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TicketInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ticketgegevens')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('subject')
                            ->label('Onderwerp'),
                        TextEntry::make('type')
                            ->label('Type')
                            ->badge(),
                        TextEntry::make('priority')
                            ->label('Prioriteit')
                            ->badge(),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge(),
                        TextEntry::make('description')
                            ->label('Beschrijving')
                            ->columnSpanFull(),
                    ]),
                Section::make('Gekoppeld')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('contact.name')
                            ->label('Contact'),
                        TextEntry::make('assignedTo.name')
                            ->label('Toegewezen aan')
                            ->placeholder('Niet toegewezen'),
                        TextEntry::make('scheduled_at')
                            ->label('Ingepland')
                            ->dateTime('d-m-Y H:i')
                            ->placeholder('Niet ingepland'),
                        TextEntry::make('created_at')
                            ->label('Aangemaakt')
                            ->dateTime('d-m-Y H:i'),
                    ]),
            ]);
    }
}
