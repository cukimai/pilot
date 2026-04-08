<?php

declare(strict_types=1);

namespace App\Filament\Resources\Conversations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ConversationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Gesprek Details')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('channel')
                            ->label('Kanaal')
                            ->badge(),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge(),
                        TextEntry::make('contact.name')
                            ->label('Contact')
                            ->default('Anoniem'),
                        TextEntry::make('created_at')
                            ->label('Gestart')
                            ->dateTime('d-m-Y H:i'),
                    ]),
                Section::make('Samenvatting')
                    ->schema([
                        TextEntry::make('summary')
                            ->hiddenLabel()
                            ->default('Geen samenvatting beschikbaar'),
                    ]),
            ]);
    }
}
