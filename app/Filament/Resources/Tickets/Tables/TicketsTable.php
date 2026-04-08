<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tickets\Tables;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->label('Onderwerp')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge(),
                TextColumn::make('priority')
                    ->label('Prioriteit')
                    ->badge(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('contact.name')
                    ->label('Contact')
                    ->searchable(),
                TextColumn::make('assignedTo.name')
                    ->label('Monteur')
                    ->default('-'),
                TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(TicketType::class),
                SelectFilter::make('priority')
                    ->label('Prioriteit')
                    ->options(TicketPriority::class),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(TicketStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('assign')
                    ->label('Toewijzen')
                    ->icon('heroicon-o-user-plus')
                    ->form([
                        Select::make('assigned_to')
                            ->label('Monteur')
                            ->options(User::pluck('name', 'id'))
                            ->searchable(),
                    ])
                    ->action(function (mixed $record, array $data): void {
                        $record->update(['assigned_to' => $data['assigned_to']]);
                    }),
                Action::make('changeStatus')
                    ->label('Status')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Select::make('status')
                            ->label('Status')
                            ->options(TicketStatus::class)
                            ->required(),
                    ])
                    ->action(function (mixed $record, array $data): void {
                        $record->update(['status' => $data['status']]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
