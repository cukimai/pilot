<?php

declare(strict_types=1);

namespace App\Filament\Resources\Conversations\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    protected static ?string $title = 'Berichten';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (mixed $state): string => match ((string) $state) {
                        'user' => 'info',
                        'assistant' => 'success',
                        'system' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('content')
                    ->label('Bericht')
                    ->wrap()
                    ->limit(200),
                TextColumn::make('created_at')
                    ->label('Tijd')
                    ->dateTime('d-m-Y H:i:s'),
            ])
            ->defaultSort('created_at', 'asc')
            ->paginated(false);
    }
}
