<?php

declare(strict_types=1);

namespace App\Enums;

enum ConversationStatus: string
{
    case Active = 'active';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Actief',
            self::Closed => 'Gesloten',
        };
    }
}
