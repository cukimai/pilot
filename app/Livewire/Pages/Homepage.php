<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Enums\KnowledgeCategory;
use App\Models\KnowledgeEntry;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Homepage extends Component
{
    public string $companyName;

    public string $companyPhone;

    public string $companyEmail;

    public string $companyAddress;

    public array $services = [];

    public array $aboutInfo = [];

    public array $workArea = [];

    public function mount(): void
    {
        $this->companyName = Setting::get('company_name', 'Installatiebedrijf');
        $this->companyPhone = Setting::get('company_phone', '');
        $this->companyEmail = Setting::get('company_email', '');
        $this->companyAddress = Setting::get('company_address', '');

        $this->services = KnowledgeEntry::query()
            ->active()
            ->byCategory(KnowledgeCategory::Diensten)
            ->orderBy('sort_order')
            ->get()
            ->toArray();

        $this->aboutInfo = KnowledgeEntry::query()
            ->active()
            ->byCategory(KnowledgeCategory::Bedrijfsinfo)
            ->orderBy('sort_order')
            ->get()
            ->toArray();

        $this->workArea = KnowledgeEntry::query()
            ->active()
            ->byCategory(KnowledgeCategory::Werkgebied)
            ->orderBy('sort_order')
            ->get()
            ->toArray();
    }

    #[Title('Home')]
    public function render()
    {
        return view('livewire.pages.homepage');
    }
}
