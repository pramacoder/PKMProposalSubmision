<?php

namespace App\Enums;

enum ReviewStage: string
{
    case Admin = 'admin';
    case Substantive = 'substantive';
    case Final = 'final';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Review Administratif',
            self::Substantive => 'Review Substantif Awal',
            self::Final => 'Review Final',
        };
    }
}
