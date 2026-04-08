<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Monteur = 'monteur';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Beheerder',
            self::Monteur => 'Monteur',
        };
    }
}
