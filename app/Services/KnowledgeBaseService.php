<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\KnowledgeCategory;
use App\Models\KnowledgeEntry;
use App\Models\Setting;

class KnowledgeBaseService
{
    public function buildSystemPrompt(): string
    {
        $companyName = Setting::get('company_name', 'ons bedrijf');
        $tone = Setting::get('ai_tone', 'Vriendelijk en professioneel');

        $prompt = "Je bent de digitale assistent van {$companyName}.\n";
        $prompt .= "Je helpt klanten met vragen over diensten, het melden van storingen, het plannen van afspraken en het aanvragen van offertes.\n\n";

        foreach (KnowledgeCategory::cases() as $category) {
            $entries = KnowledgeEntry::query()
                ->active()
                ->byCategory($category)
                ->orderBy('sort_order')
                ->get();

            if ($entries->isEmpty()) {
                continue;
            }

            $prompt .= "{$category->label()}:\n";

            foreach ($entries as $entry) {
                $prompt .= "- {$entry->question}: {$entry->answer}\n";
            }

            $prompt .= "\n";
        }

        $prompt .= "Instructies:\n";
        $prompt .= "- Communiceer in het Nederlands\n";
        $prompt .= "- Tone of voice: {$tone}\n";
        $prompt .= "- Als iemand een storing meldt: vraag contactgegevens en maak een ticket aan via de create_ticket tool\n";
        $prompt .= "- Als iemand een afspraak wil: verzamel informatie en gebruik de schedule_appointment tool\n";
        $prompt .= "- Als iemand een offerte wil: verzamel details en maak een offerte-ticket aan\n";
        $prompt .= "- Verzamel altijd contactgegevens via de collect_contact_info tool\n";
        $prompt .= "- Als je het antwoord niet weet: escaleer via de escalate_to_human tool\n";
        $prompt .= "- Wees beknopt maar behulpzaam\n";

        return $prompt;
    }
}
