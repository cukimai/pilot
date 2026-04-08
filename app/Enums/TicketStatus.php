<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Scheduled = 'scheduled';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::InProgress => 'In behandeling',
            self::Scheduled => 'Ingepland',
            self::Resolved => 'Opgelost',
            self::Closed => 'Gesloten',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open => 'info',
            self::InProgress => 'warning',
            self::Scheduled => 'primary',
            self::Resolved => 'success',
            self::Closed => 'gray',
        };
    }
}
