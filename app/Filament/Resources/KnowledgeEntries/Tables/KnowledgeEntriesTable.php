<?php

declare(strict_types=1);

namespace App\Filament\Resources\KnowledgeEntries\Tables;

use App\Enums\KnowledgeCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KnowledgeEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category')
                    ->label('Categorie')
                    ->badge()
                    ->sortable(),
                TextColumn::make('question')
                    ->label('Vraag / Titel')
                    ->searchable()
                    ->limit(50),
                IconColumn::make('is_active')
                    ->label('Actief')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Volgorde')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options(KnowledgeCategory::class),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('category')
            ->reorderable('sort_order');
    }
}
