<?php

declare(strict_types=1);

namespace App\Filament\Resources\Appointments\Tables;

use App\Enums\AppointmentStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scheduled_at')
                    ->label('Datum & Tijd')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                TextColumn::make('contact.name')
                    ->label('Contact')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Monteur'),
                TextColumn::make('ticket.subject')
                    ->label('Ticket')
                    ->limit(30),
                TextColumn::make('duration_minutes')
                    ->label('Duur')
                    ->suffix(' min'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                IconColumn::make('google_event_id')
                    ->label('Google')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->getStateUsing(fn (mixed $record): bool => ! is_null($record->google_event_id)),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(AppointmentStatus::class),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('confirm')
                    ->label('Bevestigen')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn (mixed $record) => $record->update(['status' => AppointmentStatus::Confirmed->value]))
                    ->visible(fn (mixed $record): bool => $record->status === AppointmentStatus::Planned),
                Action::make('cancel')
                    ->label('Annuleren')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (mixed $record) => $record->update(['status' => AppointmentStatus::Cancelled->value]))
                    ->visible(fn (mixed $record): bool => $record->status !== AppointmentStatus::Completed
                        && $record->status !== AppointmentStatus::Cancelled),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('scheduled_at', 'asc');
    }
}
