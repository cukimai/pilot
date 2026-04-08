<?php

declare(strict_types=1);

namespace App\Filament\Resources\Conversations\Tables;

use App\Enums\Channel;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ConversationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('channel')
                    ->label('Kanaal')
                    ->badge(),
                TextColumn::make('contact.name')
                    ->label('Contact')
                    ->default('Anoniem')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('messages_count')
                    ->label('Berichten')
                    ->counts('messages'),
                TextColumn::make('created_at')
                    ->label('Gestart')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('channel')
                    ->label('Kanaal')
                    ->options(Channel::class),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc');
    }
}
