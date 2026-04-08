<?php

declare(strict_types=1);

namespace App\Enums;

enum KnowledgeCategory: string
{
    case Diensten = 'diensten';
    case Faq = 'faq';
    case Bedrijfsinfo = 'bedrijfsinfo';
    case Werkgebied = 'werkgebied';
    case Prijzen = 'prijzen';

    public function label(): string
    {
        return match ($this) {
            self::Diensten => 'Diensten',
            self::Faq => 'Veelgestelde vragen',
            self::Bedrijfsinfo => 'Bedrijfsinformatie',
            self::Werkgebied => 'Werkgebied',
            self::Prijzen => 'Prijsindicaties',
        };
    }
}
