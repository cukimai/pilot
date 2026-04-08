<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Naam')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Telefoon')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Wachtwoord')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->maxLength(255),
                Select::make('role')
                    ->label('Rol')
                    ->options(UserRole::class)
                    ->default('monteur')
                    ->required(),
                TextInput::make('google_calendar_id')
                    ->label('Google Agenda ID')
                    ->maxLength(255),
            ]);
    }
}
