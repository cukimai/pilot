<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestTickets extends TableWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Laatste Tickets';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Ticket::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->label('Onderwerp')
                    ->limit(40),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioriteit')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('contact.name')
                    ->label('Contact'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y H:i'),
            ])
            ->paginated(false);
    }
}
