<?php

declare(strict_types=1);

namespace App\Filament\Resources\KnowledgeEntries\Schemas;

use App\Enums\KnowledgeCategory;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class KnowledgeEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category')
                    ->label('Categorie')
                    ->options(KnowledgeCategory::class)
                    ->required(),
                TextInput::make('question')
                    ->label('Vraag / Titel')
                    ->required()
                    ->maxLength(255),
                RichEditor::make('answer')
                    ->label('Antwoord / Inhoud')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Actief')
                    ->default(true),
                TextInput::make('sort_order')
                    ->label('Sortering')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
