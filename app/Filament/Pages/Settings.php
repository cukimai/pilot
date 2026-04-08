<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class Settings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Instellingen';

    protected static ?string $title = 'Instellingen';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'company_name' => Setting::get('company_name', ''),
            'company_address' => Setting::get('company_address', ''),
            'company_phone' => Setting::get('company_phone', ''),
            'company_email' => Setting::get('company_email', ''),
            'ai_greeting' => Setting::get('ai_greeting', 'Welkom! Hoe kan ik u helpen?'),
            'ai_tone' => Setting::get('ai_tone', 'Vriendelijk en professioneel'),
            'voice_transfer_number' => Setting::get('voice_transfer_number', ''),
            'notification_emails' => Setting::get('notification_emails', ''),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Bedrijfsgegevens')->schema([
                    TextInput::make('company_name')
                        ->label('Bedrijfsnaam')
                        ->required(),
                    TextInput::make('company_address')
                        ->label('Adres'),
                    TextInput::make('company_phone')
                        ->label('Telefoonnummer')
                        ->tel(),
                    TextInput::make('company_email')
                        ->label('E-mail')
                        ->email(),
                ])->columns(2),
                Section::make('AI Instellingen')->schema([
                    TextInput::make('ai_greeting')
                        ->label('Begroetingstekst')
                        ->helperText('De eerste tekst die de chatbot toont'),
                    Textarea::make('ai_tone')
                        ->label('Tone of voice')
                        ->helperText('Beschrijf hoe de AI moet communiceren'),
                ]),
                Section::make('Voice Instellingen')->schema([
                    TextInput::make('voice_transfer_number')
                        ->label('Doorverbind-nummer bij urgentie')
                        ->tel()
                        ->helperText('Telefoonnummer waar urgente bellers naartoe worden doorverbonden'),
                ]),
                Section::make('Notificaties')->schema([
                    Textarea::make('notification_emails')
                        ->label('E-mailadressen voor notificaties')
                        ->helperText('Eén e-mailadres per regel'),
                ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Instellingen opgeslagen')
            ->success()
            ->send();
    }
}
