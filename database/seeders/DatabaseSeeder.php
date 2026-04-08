<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\KnowledgeEntry;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@pilot.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Monteur
        User::create([
            'name' => 'Jan Monteur',
            'email' => 'monteur@pilot.test',
            'password' => bcrypt('password'),
            'role' => 'monteur',
            'phone' => '06-12345678',
        ]);

        // Settings
        Setting::set('company_name', 'Warmte & Koeling BV');
        Setting::set('company_address', 'Industrieweg 42, 3500 AA Utrecht');
        Setting::set('company_phone', '030-1234567');
        Setting::set('company_email', 'info@warmteenkoeling.nl');
        Setting::set('ai_greeting', 'Welkom bij Warmte & Koeling BV! Hoe kan ik u helpen?');
        Setting::set('ai_tone', 'Vriendelijk, professioneel en behulpzaam. Gebruik u in plaats van je.');
        Setting::set('voice_transfer_number', '06-12345678');
        Setting::set('notification_emails', 'admin@pilot.test');

        // Kennisbank: Diensten
        KnowledgeEntry::create([
            'category' => 'diensten',
            'question' => 'CV-ketel onderhoud & reparatie',
            'answer' => 'Wij verzorgen jaarlijks onderhoud, storingen en reparaties aan alle merken CV-ketels. Inclusief storing buiten kantooruren.',
            'sort_order' => 1,
        ]);

        KnowledgeEntry::create([
            'category' => 'diensten',
            'question' => 'Airconditioning',
            'answer' => 'Installatie, onderhoud en reparatie van airconditioningsystemen voor woning en bedrijf. Alle bekende merken.',
            'sort_order' => 2,
        ]);

        KnowledgeEntry::create([
            'category' => 'diensten',
            'question' => 'Zonnepanelen',
            'answer' => 'Advies, installatie en onderhoud van zonnepanelen. Wij verzorgen het complete traject van offerte tot oplevering.',
            'sort_order' => 3,
        ]);

        KnowledgeEntry::create([
            'category' => 'diensten',
            'question' => 'Vloerverwarming',
            'answer' => 'Aanleg en onderhoud van vloerverwarmingssystemen bij nieuwbouw en renovatie.',
            'sort_order' => 4,
        ]);

        // Kennisbank: FAQ
        KnowledgeEntry::create([
            'category' => 'faq',
            'question' => 'Wat kost een CV-ketel onderhoudsbeurt?',
            'answer' => 'Een standaard onderhoudsbeurt kost vanaf 89 euro inclusief BTW. De exacte prijs hangt af van het type ketel.',
            'sort_order' => 1,
        ]);

        KnowledgeEntry::create([
            'category' => 'faq',
            'question' => 'Hoe snel kan een monteur langskomen bij een storing?',
            'answer' => 'Bij urgente storingen (geen verwarming, gaslucht) streven wij naar een bezoek binnen 4 uur. Voor niet-urgente storingen meestal binnen 1-2 werkdagen.',
            'sort_order' => 2,
        ]);

        KnowledgeEntry::create([
            'category' => 'faq',
            'question' => 'Zijn jullie ook in het weekend bereikbaar?',
            'answer' => 'Ja, onze AI-assistent is 24/7 bereikbaar. Voor urgente storingen hebben wij een weekenddienst. Reguliere afspraken plannen wij op werkdagen.',
            'sort_order' => 3,
        ]);

        // Kennisbank: Bedrijfsinfo
        KnowledgeEntry::create([
            'category' => 'bedrijfsinfo',
            'question' => 'Over Warmte & Koeling BV',
            'answer' => 'Warmte & Koeling BV is een installatietechnisch bedrijf gespecialiseerd in verwarming, koeling en duurzame energie. Met meer dan 15 jaar ervaring staan wij bekend om vakmanschap en betrouwbaarheid.',
            'sort_order' => 1,
        ]);

        // Kennisbank: Werkgebied
        KnowledgeEntry::create([
            'category' => 'werkgebied',
            'question' => 'Utrecht',
            'answer' => 'Gemeente Utrecht en omstreken',
            'sort_order' => 1,
        ]);

        KnowledgeEntry::create([
            'category' => 'werkgebied',
            'question' => 'Amersfoort',
            'answer' => 'Gemeente Amersfoort en omstreken',
            'sort_order' => 2,
        ]);

        KnowledgeEntry::create([
            'category' => 'werkgebied',
            'question' => 'Hilversum',
            'answer' => 'Gemeente Hilversum en omstreken',
            'sort_order' => 3,
        ]);

        // Kennisbank: Prijzen
        KnowledgeEntry::create([
            'category' => 'prijzen',
            'question' => 'Voorrijkosten',
            'answer' => '35 euro voorrijkosten binnen ons werkgebied.',
            'sort_order' => 1,
        ]);

        KnowledgeEntry::create([
            'category' => 'prijzen',
            'question' => 'Uurtarief',
            'answer' => 'Ons uurtarief is 65 euro exclusief BTW voor reguliere werkzaamheden.',
            'sort_order' => 2,
        ]);
    }
}
