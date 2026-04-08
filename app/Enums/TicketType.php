<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketType: string
{
    case Storing = 'storing';
    case Afspraak = 'afspraak';
    case Offerte = 'offerte';
    case Overig = 'overig';

    public function label(): string
    {
        return match ($this) {
            self::Storing => 'Storing',
            self::Afspraak => 'Afspraak',
            self::Offerte => 'Offerte',
            self::Overig => 'Overig',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Storing => 'danger',
            self::Afspraak => 'info',
            self::Offerte => 'warning',
            self::Overig => 'gray',
        };
    }
}
