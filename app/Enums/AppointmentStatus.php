<?php

declare(strict_types=1);

namespace App\Enums;

enum AppointmentStatus: string
{
    case Planned = 'planned';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'Gepland',
            self::Confirmed => 'Bevestigd',
            self::Completed => 'Afgerond',
            self::Cancelled => 'Geannuleerd',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Planned => 'info',
            self::Confirmed => 'success',
            self::Completed => 'gray',
            self::Cancelled => 'danger',
        };
    }
}
