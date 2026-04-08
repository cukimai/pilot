<?php

declare(strict_types=1);

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contactgegevens')
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->label('Naam')
                            ->required(),
                        TextInput::make('email')
                            ->label('E-mail')
                            ->email(),
                        TextInput::make('phone')
                            ->label('Telefoon')
                            ->tel(),
                    ]),
                Section::make('Adres')
                    ->columns(3)
                    ->schema([
                        TextInput::make('address')
                            ->label('Adres'),
                        TextInput::make('city')
                            ->label('Stad'),
                        TextInput::make('postal_code')
                            ->label('Postcode')
                            ->maxLength(10),
                    ]),
                Textarea::make('notes')
                    ->label('Notities')
                    ->columnSpanFull(),
            ]);
    }
}
