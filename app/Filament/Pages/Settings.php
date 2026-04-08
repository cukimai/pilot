<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Instellingen';

    protected static ?string $title = 'Instellingen';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.settings';

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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bedrijfsgegevens')->schema([
                    Forms\Components\TextInput::make('company_name')
                        ->label('Bedrijfsnaam')
                        ->required(),
                    Forms\Components\TextInput::make('company_address')
                        ->label('Adres'),
                    Forms\Components\TextInput::make('company_phone')
                        ->label('Telefoonnummer')
                        ->tel(),
                    Forms\Components\TextInput::make('company_email')
                        ->label('E-mail')
                        ->email(),
                ])->columns(2),
                Forms\Components\Section::make('AI Instellingen')->schema([
                    Forms\Components\TextInput::make('ai_greeting')
                        ->label('Begroetingstekst')
                        ->helperText('De eerste tekst die de chatbot toont'),
                    Forms\Components\Textarea::make('ai_tone')
                        ->label('Tone of voice')
                        ->helperText('Beschrijf hoe de AI moet communiceren'),
                ]),
                Forms\Components\Section::make('Voice Instellingen')->schema([
                    Forms\Components\TextInput::make('voice_transfer_number')
                        ->label('Doorverbind-nummer bij urgentie')
                        ->tel()
                        ->helperText('Telefoonnummer waar urgente bellers naartoe worden doorverbonden'),
                ]),
                Forms\Components\Section::make('Notificaties')->schema([
                    Forms\Components\Textarea::make('notification_emails')
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
