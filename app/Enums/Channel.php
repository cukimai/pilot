<?php

declare(strict_types=1);

namespace App\Enums;

enum Channel: string
{
    case Chat = 'chat';
    case Voice = 'voice';
    case Email = 'email';

    public function label(): string
    {
        return match ($this) {
            self::Chat => 'Chat',
            self::Voice => 'Telefoon',
            self::Email => 'E-mail',
        };
    }
}
