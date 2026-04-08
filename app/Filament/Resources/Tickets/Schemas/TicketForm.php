<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Schemas\Schema;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
